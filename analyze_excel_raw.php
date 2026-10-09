<?php
/**
 * Analyze Excel raw structure
 */

try {
    $file = 'Treasy_DRE.xlsx';

    $zip = new ZipArchive();
    if (!$zip->open($file)) {
        throw new Exception("Falha ao abrir Excel");
    }

    // Read sheet
    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if (!$sheetXml) {
        $sheetXml = $zip->getFromName('xl/worksheets/sheet2.xml');
    }

    $dom = new DOMDocument();
    $dom->loadXML($sheetXml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('ws', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

    echo "📊 Analisando estrutura bruta do Excel\n";
    echo "======================================\n\n";

    // Get first row (headers)
    $rows = $xpath->query('//ws:row[@r="1"]');
    if ($rows->length > 0) {
        $row = $rows->item(0);
        $cells = $xpath->query('.//ws:c', $row);

        echo "📋 HEADERS (Linha 1):\n";
        foreach ($cells as $cell) {
            $ref = $cell->getAttribute('r');
            $values = $xpath->query('.//ws:v', $cell);
            $value = $values->length > 0 ? $values->item(0)->nodeValue : '';
            echo "  [{$ref}] {$value}\n";
        }
    }

    // Get row 3 (first data row)
    $rows = $xpath->query('//ws:row[@r="3"]');
    if ($rows->length > 0) {
        $row = $rows->item(0);
        $cells = $xpath->query('.//ws:c', $row);

        echo "\n📋 PRIMEIRA LINHA DE DADOS (Linha 3):\n";
        foreach ($cells as $cell) {
            $ref = $cell->getAttribute('r');
            $values = $xpath->query('.//ws:v', $cell);
            $value = $values->length > 0 ? floatval($values->item(0)->nodeValue) : '';
            if (is_numeric($value)) {
                echo "  [{$ref}] " . number_format($value, 2) . "\n";
            } else {
                echo "  [{$ref}] {$value}\n";
            }
        }
    }

    // Count total columns in row 3
    $rows = $xpath->query('//ws:row[@r="3"]');
    if ($rows->length > 0) {
        $row = $rows->item(0);
        $cells = $xpath->query('.//ws:c', $row);
        echo "\n📊 Total de colunas na linha 3: " . $cells->length . "\n";
    }

    // Expected structure:
    echo "\n📐 ESTRUTURA ESPERADA:\n";
    echo "- Coluna A: Linha\n";
    echo "- Coluna B: Nome da linha\n";
    echo "- JANEIRO (5 colunas):\n";
    echo "  - C1: Planejado\n";
    echo "  - D1: Realizado\n";
    echo "  - E1: Análise Vertical Planejado\n";
    echo "  - F1: Análise Vertical Realizado\n";
    echo "  - G1: Planejado X Realizado (%)\n";
    echo "- FEVEREIRO a DEZEMBRO (7 colunas cada):\n";
    echo "  - Planejado\n";
    echo "  - Realizado\n";
    echo "  - Análise Horizontal Planejado\n";
    echo "  - Análise Horizontal Realizado\n";
    echo "  - Análise Vertical Planejado\n";
    echo "  - Análise Vertical Realizado\n";
    echo "  - Planejado X Realizado (%)\n";

    $zip->close();

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
