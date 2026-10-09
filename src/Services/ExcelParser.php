<?php

namespace App\Services;

class ExcelParser
{
    private const EXPECTED_SHEET = 'DRE Sintético';
    private const START_ROW = 3;
    private const END_ROW = 13;

    public function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $zip = new \ZipArchive();
        if (!$zip->open($filePath)) {
            throw new \Exception("Failed to open Excel file");
        }

        try {
            // Read workbook.xml
            $workbookXml = $zip->getFromName('xl/workbook.xml');
            if (!$workbookXml) {
                throw new \Exception("workbook.xml not found");
            }

            // Parse and find sheet
            $dom = new \DOMDocument();
            $dom->loadXML($workbookXml);
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('wb', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

            // Find all sheets and locate our target
            $sheets = $xpath->query('//wb:sheet');
            $targetSheetId = null;

            foreach ($sheets as $sheet) {
                if ($sheet->getAttribute('name') === self::EXPECTED_SHEET) {
                    $targetSheetId = $sheet->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
                    break;
                }
            }

            if (!$targetSheetId) {
                throw new \Exception("Sheet '" . self::EXPECTED_SHEET . "' not found");
            }

            // Read relationships to find sheet file
            $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
            if (!$relsXml) {
                throw new \Exception("workbook.xml.rels not found");
            }

            $relsDom = new \DOMDocument();
            $relsDom->loadXML($relsXml);
            $relsXpath = new \DOMXPath($relsDom);
            $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

            $rels = $relsXpath->query("//rel:Relationship[@Id='{$targetSheetId}']");

            // Fallback: try without namespace
            if ($rels->length === 0) {
                $rels = $relsXpath->query("//*[@Id='{$targetSheetId}']");
            }

            if ($rels->length === 0) {
                throw new \Exception("Sheet relationship not found for ID: {$targetSheetId}");
            }

            $sheetFile = $rels->item(0)->getAttribute('Target');

            // Try with xl/ prefix first, then without
            $sheetPath = "xl/{$sheetFile}";
            $sheetXml = $zip->getFromName($sheetPath);

            if (!$sheetXml && strpos($sheetFile, 'xl/') !== 0) {
                // Try without xl/ if it's already in the path
                $sheetXml = $zip->getFromName($sheetFile);
            }

            if (!$sheetXml) {
                throw new \Exception("Sheet XML not found: tried {$sheetPath} and {$sheetFile}");
            }

            return $this->parseSheetData($sheetXml);

        } finally {
            $zip->close();
        }
    }

    private function parseSheetData(string $sheetXml): array
    {
        $dom = new \DOMDocument();
        $dom->loadXML($sheetXml);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('ws', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = $xpath->query('//ws:row');
        $data = [];
        $errors = [];

        foreach ($rows as $row) {
            $rowNum = (int)$row->getAttribute('r');
            if ($rowNum < self::START_ROW || $rowNum > self::END_ROW) {
                continue;
            }

            $linhaId = $rowNum - 2;
            $cells = $xpath->query('.//ws:c', $row);
            $cellValues = [];

            foreach ($cells as $cell) {
                $ref = $cell->getAttribute('r');
                $values = $xpath->query('.//ws:v', $cell);
                if ($values->length > 0) {
                    $cellValues[$ref] = floatval($values->item(0)->nodeValue);
                }
            }

            // Extract 12 months
            for ($mes = 1; $mes <= 12; $mes++) {
                $colStart = 2 + (($mes - 1) * 5);

                $data[] = [
                    'linha_id' => $linhaId,
                    'mes' => $mes,
                    'valor_planejado' => $this->getCellValue($cellValues, $colStart, $rowNum),
                    'valor_realizado' => $this->getCellValue($cellValues, $colStart + 1, $rowNum),
                    'analise_vertical_planejado' => $this->getCellValue($cellValues, $colStart + 2, $rowNum),
                    'analise_vertical_realizado' => $this->getCellValue($cellValues, $colStart + 3, $rowNum),
                    'variacao_planejado_realizado' => $this->getCellValue($cellValues, $colStart + 4, $rowNum),
                ];
            }
        }

        return ['data' => $data, 'linhas_count' => count($data), 'errors' => $errors];
    }

    private function getCellValue(array $cellValues, int $col, int $row): ?float
    {
        $colLetter = $this->numToCol($col);
        $cellRef = $colLetter . $row;
        return $cellValues[$cellRef] ?? null;
    }

    private function numToCol(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $num--;
            $letter = chr(65 + ($num % 26)) . $letter;
            $num = intdiv($num, 26);
        }
        return $letter;
    }
}
