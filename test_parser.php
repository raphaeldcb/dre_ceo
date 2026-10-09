<?php

require 'autoload.php';

$parser = new App\Services\ExcelParser();
try {
    $result = $parser->parse('Treasy_DRE.xlsx');
    echo 'Sucesso! Records: ' . $result['linhas_count'] . PHP_EOL;
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
