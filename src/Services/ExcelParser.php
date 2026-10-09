<?php

namespace App\Services;

class ExcelParser
{
    private const EXPECTED_SHEET = 'DRE Sintético';
    private const START_ROW = 3;
    private const END_ROW = 13;

    /**
     * Parse DRE Excel file (.xlsx) using only PHP native functions
     * .xlsx is a ZIP file containing XML files
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

        // Open .xlsx as ZIP
        $zip = new \ZipArchive();
        if (!$zip->open($filePath)) {
            throw new Exception("Failed to open Excel file as ZIP");
        }

        // Read workbook.xml to find sheet relationships
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        if (!$workbookXml) {
            throw new Exception("workbook.xml not found in Excel file");
        }

        // Read relationships to find sheet file names
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        if (!$relsXml) {
            throw new Exception("workbook.xml.rels not found in Excel file");
        }

        // Parse workbook.xml to find sheet name
        $workbookDom = new \DOMDocument();
        $workbookDom->loadXML($workbookXml);
        $sheetName = $this->findSheetFile($workbookDom, $relsXml);

        if (!$sheetName) {
            throw new Exception("Sheet '" . self::EXPECTED_SHEET . "' not found in workbook");
        }

        // Read the sheet XML
        $sheetXml = $zip->getFromName("xl/worksheets/{$sheetName}");
        if (!$sheetXml) {
            throw new Exception("Sheet file not found: {$sheetName}");
        }

        $zip->close();

        // Parse sheet data
        return $this->parseSheetXml($sheetXml);
    }

    /**
     * Find the sheet file corresponding to the expected sheet name
     */
    private function findSheetFile(\DOMDocument $workbookDom, string $relsXml): ?string
    {
        $xpath = new \DOMXPath($workbookDom);
        $xpath->registerNamespace('wb', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        // Find sheets
        $sheets = $xpath->query('//wb:sheet[@name="' . self::EXPECTED_SHEET . '"]');

        if ($sheets->length === 0) {
            return null;
        }

        $sheet = $sheets->item(0);
        $sheetId = $sheet->getAttribute('r:id');

        // Parse relationships to find file
        $relsDom = new \DOMDocument();
        $relsDom->loadXML($relsXml);
        $relsXpath = new \DOMXPath($relsDom);
        $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $rels = $relsXpath->query('//rel:Relationship[@Id="' . $sheetId . '"]');
        if ($rels->length === 0) {
            return null;
        }

        return $rels->item(0)->getAttribute('Target');
    }

    /**
     * Parse sheet XML and extract data
     */
    private function parseSheetXml(string $sheetXml): array
    {
        $dom = new \DOMDocument();
        $dom->loadXML($sheetXml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('ws', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        // Get all rows
        $rows = $xpath->query('//ws:row');

        $data = [];
        $errors = [];

        // Parse rows 3-13 (DRE lines)
        foreach ($rows as $row) {
            $rowNum = (int)$row->getAttribute('r');

            if ($rowNum < self::START_ROW || $rowNum > self::END_ROW) {
                continue;
            }

            $linhaId = $rowNum - 2; // DRE line ID (1-11)

            try {
                // Get cells in this row
                $cells = $xpath->query('.//ws:c', $row);
                $cellValues = [];

                foreach ($cells as $cell) {
                    $ref = $cell->getAttribute('r');
                    $value = $this->getCellValue($cell, $dom, $xpath);
                    $cellValues[$ref] = $value;
                }

                // Extract monthly data
                for ($mesNum = 1; $mesNum <= 12; $mesNum++) {
                    $colStart = 2 + (($mesNum - 1) * 5);

                    $record = [
                        'linha_id' => $linhaId,
                        'mes' => $mesNum,
                        'valor_planejado' => $this->getCellValueByCol($cellValues, $colStart, $rowNum),
                        'valor_realizado' => $this->getCellValueByCol($cellValues, $colStart + 1, $rowNum),
                        'analise_vertical_planejado' => $this->getCellValueByCol($cellValues, $colStart + 2, $rowNum),
                        'analise_vertical_realizado' => $this->getCellValueByCol($cellValues, $colStart + 3, $rowNum),
                        'variacao_planejado_realizado' => $this->getCellValueByCol($cellValues, $colStart + 4, $rowNum),
                    ];

                    if ($mesNum > 1) {
                        $record['analise_horizontal_planejado'] = $this->getCellValueByCol($cellValues, $colStart + 5, $rowNum);
                        $record['analise_horizontal_realizado'] = $this->getCellValueByCol($cellValues, $colStart + 6, $rowNum);
                    }

                    $data[] = $record;
                }
            } catch (Exception $e) {
                $errors[] = "Error parsing row {$rowNum}: " . $e->getMessage();
            }
        }

        return [
            'data' => $data,
            'linhas_count' => count($data),
            'errors' => $errors
        ];
    }

    /**
     * Get cell value from XML element
     */
    private function getCellValue(\DOMElement $cell, \DOMDocument $dom, \DOMXPath $xpath): ?float
    {
        $t = $cell->getAttribute('t');

        // Get value element
        $values = $xpath->query('.//ws:v', $cell);
        if ($values->length === 0) {
            return null;
        }

        $value = $values->item(0)->nodeValue;

        // If it's a shared string, skip
        if ($t === 's') {
            return null;
        }

        // Convert to float
        if (is_numeric($value)) {
            return floatval($value);
        }

        return null;
    }

    /**
     * Get cell value from cell array by column and row
     */
    private function getCellValueByCol(array $cellValues, int $col, int $row): ?float
    {
        $colLetter = $this->columnNumberToLetter($col);
        $cellRef = $colLetter . $row;

        return $cellValues[$cellRef] ?? null;
    }

    /**
     * Convert column number to Excel letter
     */
    private function columnNumberToLetter(int $col): string
    {
        $letter = '';
        while ($col > 0) {
            $col--;
            $letter = chr(65 + ($col % 26)) . $letter;
            $col = intdiv($col, 26);
        }
        return $letter;
    }
}
