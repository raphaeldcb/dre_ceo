<?php
/**
 * Analyze Excel structure
 */

require_once __DIR__ . '/autoload.php';

try {
    $file = 'Treasy_DRE.xlsx';

    // Load using PhpSpreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getSheetByName('DRE Sintético');

    echo "📊 Analisando estrutura do Excel: DRE Sintético\n";
    echo "================================================\n\n";

    // Get dimensions
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    echo "Dimensões: {$highestRow} linhas × {$highestColumn} colunas\n\n";

    // Print first 3 rows (headers)
    echo "📋 HEADERS:\n";
    for ($row = 1; $row <= 3; $row++) {
        echo "Linha {$row}: ";
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cell = $sheet->getCell("{$col}{$row}");
            $value = $cell->getValue();
            echo "[{$col}] " . (is_numeric($value) ? number_format($value, 2) : $value) . " | ";
        }
        echo "\n";
    }

    echo "\n📋 PRIMEIRA LINHA DE DADOS (linha 4):\n";
    for ($col = 'A'; $col <= 'P'; $col++) {
        $cell = $sheet->getCell("{$col}4");
        $value = $cell->getValue();
        echo "[{$col}] " . (is_numeric($value) ? number_format($value, 2) : $value) . " | ";
    }
    echo "\n";

    echo "\n📋 ÚLTIMAS LINHAS:\n";
    for ($row = $highestRow - 2; $row <= $highestRow; $row++) {
        echo "Linha {$row}: ";
        for ($col = 'A'; $col <= 'D'; $col++) {
            $cell = $sheet->getCell("{$col}{$row}");
            $value = $cell->getValue();
            echo "[{$col}] " . $value . " | ";
        }
        echo "\n";
    }

} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
