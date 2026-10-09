<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ExcelParser
{
    private const EXPECTED_SHEET = 'DRE Sintético';
    private const MONTHS = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
    private const START_ROW = 3; // Row 3 is first DRE line
    private const END_ROW = 13; // Row 13 is last DRE line (11 lines total)

    /**
     * Parse DRE Excel file and extract monthly data
     *
     * @param string $filePath Path to .xlsx file
     * @return array ['data' => [...], 'linhas_count' => int, 'errors' => []]
     * @throws Exception If file or sheet not found
     */
    public function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (Exception $e) {
            throw new Exception("Failed to load Excel file: " . $e->getMessage());
        }

        $sheet = $spreadsheet->getSheetByName(self::EXPECTED_SHEET);
        if (!$sheet) {
            throw new Exception("Sheet '" . self::EXPECTED_SHEET . "' not found in workbook");
        }

        $data = [];
        $errors = [];

        // Parse rows 3-13 (11 DRE lines)
        for ($rowIndex = self::START_ROW; $rowIndex <= self::END_ROW; $rowIndex++) {
            $linhaId = $rowIndex - 2; // DRE line ID (1-11)

            try {
                // Extract 12 months of data for this line
                for ($mesNum = 1; $mesNum <= 12; $mesNum++) {
                    // Each month has 5 columns (jan: 2-6, fev: 7-11, etc.)
                    // Starting at column 2 (index 1)
                    $colStart = 2 + (($mesNum - 1) * 5);

                    $record = [
                        'linha_id' => $linhaId,
                        'mes' => $mesNum,
                        'valor_planejado' => $this->getCellValue($sheet, $colStart, $rowIndex),
                        'valor_realizado' => $this->getCellValue($sheet, $colStart + 1, $rowIndex),
                        'analise_vertical_planejado' => $this->getCellValue($sheet, $colStart + 2, $rowIndex),
                        'analise_vertical_realizado' => $this->getCellValue($sheet, $colStart + 3, $rowIndex),
                        'variacao_planejado_realizado' => $this->getCellValue($sheet, $colStart + 4, $rowIndex),
                    ];

                    // Análise horizontal only from Feb onwards (mesNum > 1)
                    if ($mesNum > 1) {
                        $record['analise_horizontal_planejado'] = $this->getCellValue($sheet, $colStart + 5, $rowIndex);
                        $record['analise_horizontal_realizado'] = $this->getCellValue($sheet, $colStart + 6, $rowIndex);
                    }

                    $data[] = $record;
                }
            } catch (Exception $e) {
                $errors[] = "Error parsing row {$rowIndex} (linha_id {$linhaId}): " . $e->getMessage();
            }
        }

        return [
            'data' => $data,
            'linhas_count' => count($data),
            'errors' => $errors
        ];
    }

    /**
     * Extract numeric value from a cell, return null if non-numeric
     *
     * @param object $sheet PhpSpreadsheet sheet object
     * @param int $col Column number (1-indexed)
     * @param int $row Row number
     * @return float|null
     */
    private function getCellValue($sheet, $col, $row): ?float
    {
        try {
            // Convert column number to letter (1='A', 2='B', etc.)
            $colLetter = $this->columnNumberToLetter($col);
            $cellRef = $colLetter . $row;

            $cell = $sheet->getCell($cellRef);
            $value = $cell->getValue();

            // Return null for empty/null values
            if ($value === null || $value === '') {
                return null;
            }

            // Try to convert to float
            if (is_numeric($value)) {
                return floatval($value);
            }

            // If value is string but numeric, convert it
            if (is_string($value) && is_numeric($value)) {
                return floatval($value);
            }

            // Non-numeric values return null
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Convert column number (1-indexed) to Excel column letter
     * 1='A', 2='B', ..., 26='Z', 27='AA', etc.
     *
     * @param int $col Column number
     * @return string Column letter(s)
     */
    private function columnNumberToLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $col--; // Adjust for 0-indexing in modulo operation
            $letter = chr(65 + ($col % 26)) . $letter;
            $col = intdiv($col, 26);
        }
        return $letter;
    }
}
