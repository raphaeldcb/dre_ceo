<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $parser = new App\Services\ExcelParser();
    echo "Parser created\n";

    $result = $parser->parse('Treasy_DRE.xlsx');
    echo "Parsed! Records: " . count($result['data']) . "\n";
    echo "Errors: " . json_encode($result['errors']) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
