<?php

require_once 'autoload.php';

$zip = new ZipArchive();
$zip->open('Treasy_DRE.xlsx');

$workbookXml = $zip->getFromName('xl/workbook.xml');
$dom = new DOMDocument();
$dom->loadXML($workbookXml);

$xpath = new DOMXPath($dom);
$xpath->registerNamespace('wb', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

$sheets = $xpath->query('//wb:sheet');
echo "Abas encontradas no arquivo:\n";
foreach ($sheets as $sheet) {
    $name = $sheet->getAttribute('name');
    echo "  - '{$name}'\n";
}
$zip->close();
