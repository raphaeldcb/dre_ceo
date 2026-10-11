<?php

namespace App\Controllers;

use App\Services\ExcelParser;
use App\Utils\Database;

class UploadController
{
    private $db;
    private $parser;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->parser = new ExcelParser();
    }

    /**
     * Show upload form
     */
    public function showForm(): void
    {
        include __DIR__ . '/../../views/upload.php';
    }

    /**
     * Handle file upload
     */
    public function handle(): void
    {
        header('Content-Type: application/json');

        // Validate POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        // Validate CSRF token
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
            return;
        }

        // Validate area_id
        if (empty($_POST['area_id']) || !is_numeric($_POST['area_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid area_id']);
            return;
        }

        // Validate file
        if (empty($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];
        $areaId = (int)$_POST['area_id'];
        $ano = (int)($_POST['ano'] ?? date('Y'));

        try {
            // Validate file
            $this->validateFile($file);

            // Save uploaded file temporarily
            $tmpPath = tempnam(sys_get_temp_dir(), 'dre_upload_');
            if (!move_uploaded_file($file['tmp_name'], $tmpPath)) {
                throw new \Exception('Failed to save uploaded file');
            }

            try {
                // Parse Excel file
                $parseResult = $this->parser->parse($tmpPath);

                if (!empty($parseResult['errors'])) {
                    throw new \Exception('Parse errors: ' . implode('; ', $parseResult['errors']));
                }

                // Insert data in transaction
                $pdo = $this->db;
                $pdo->beginTransaction();

                try {
                    // Delete old records for this area/year combination
                    $deleteStmt = $pdo->prepare(
                        'DELETE FROM dre_valores WHERE area_id = ? AND ano = ?'
                    );
                    $deleteStmt->execute([$areaId, $ano]);
                    $deletedRows = $deleteStmt->rowCount();

                    // Insert new records with duplicate handling
                    $insertStmt = $pdo->prepare(
                        'INSERT INTO dre_valores (area_id, dre_linha_id, mes, ano, valor_planejado, valor_realizado, analise_vertical_realizado, status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE
                         valor_planejado = VALUES(valor_planejado),
                         valor_realizado = VALUES(valor_realizado),
                         analise_vertical_realizado = VALUES(analise_vertical_realizado),
                         status = VALUES(status)'
                    );

                    $inserted = 0;
                    foreach ($parseResult['data'] as $record) {
                        $inserted += $insertStmt->execute([
                            $areaId,
                            $record['linha_id'],
                            $record['mes'],
                            $ano,
                            $record['valor_planejado'] ?? 0,
                            $record['valor_realizado'] ?? 0,
                            $record['analise_vertical_realizado'] ?? 0,
                            'FINALIZADO'
                        ]) ? 1 : 0;
                    }

                    // Record upload history
                    $hashFile = hash_file('sha256', $tmpPath);
                    $userId = $_SESSION['user_id'] ?? 1; // Default to admin if session not set

                    $historyStmt = $pdo->prepare(
                        'INSERT INTO uploads (area_id, user_id, arquivo_nome, arquivo_hash, ano, linhas_importadas, status, processado_em)
                         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())'
                    );
                    $historyStmt->execute([
                        $areaId,
                        $userId,
                        $file['name'],
                        $hashFile,
                        $ano,
                        $inserted,
                        'SUCESSO'
                    ]);

                    $pdo->commit();

                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'linhas_count' => $inserted,
                        'deletedRows' => $deletedRows,
                        'message' => "✅ Dados anteriores removidos ({$deletedRows} registros). {$inserted} novos registros importados com sucesso!"
                    ]);

                } catch (\Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }

            } finally {
                // Clean up temporary file
                if (file_exists($tmpPath)) {
                    unlink($tmpPath);
                }
            }

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Validate uploaded file
     *
     * @param array $file $_FILES entry
     * @throws \Exception
     */
    private function validateFile(array $file): void
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Upload error code: {$file['error']}");
        }

        // Check file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            throw new \Exception("File size exceeds 5MB limit");
        }

        // Check MIME type
        $allowedMimes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception("Invalid file type. Only .xlsx files are allowed. Got: {$mimeType}");
        }

        // Check file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'xlsx') {
            throw new \Exception("Invalid file extension. Only .xlsx files are allowed. Got: .{$ext}");
        }
    }
}
