<?php

$zip = new ZipArchive();
$zip->open('Treasy_DRE.xlsx');

$workbookXml = $zip->getFromName('xl/workbook.xml');
$dom = new DOMDocument();
$dom->loadXML($workbookXml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('wb', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

$sheets = $xpath->query('//wb:sheet');
$sheetName = null;
foreach ($sheets as $sheet) {
    if ($sheet->getAttribute('name') === 'DRE Sintético') {
        $sheetName = $sheet->getAttribute('sheetId');
        break;
    }
}

$relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
$relsDom = new DOMDocument();
$relsDom->loadXML($relsXml);
$relsXpath = new DOMXPath($relsDom);
$relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

$rels = $relsXpath->query("//rel:Relationship[@Type='http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet']");
$sheetFile = $rels->item(0)->getAttribute('Target');

$sheetXml = $zip->getFromName("xl/{$sheetFile}");
$sheetDom = new DOMDocument();
$sheetDom->loadXML($sheetXml);
$sheetXpath = new DOMXPath($sheetDom);
$sheetXpath->registerNamespace('ws', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

// Get row 3 (first DRE line)
$rows = $sheetXpath->query('//ws:row[@r=3]');
if ($rows->length > 0) {
    $row = $rows->item(0);
    $cells = $sheetXpath->query('.//ws:c', $row);

    echo "Row 3 - All cell values:\n";
    foreach ($cells as $cell) {
        $ref = $cell->getAttribute('r');
        $values = $sheetXpath->query('.//ws:v', $cell);
        $value = $values->length > 0 ? $values->item(0)->nodeValue : '';
        echo "  {$ref}: {$value}\n";
    }
}

$zip->close();
