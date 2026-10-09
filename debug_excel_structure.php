<?php

require 'autoload.php';

$parser = new App\Services\ExcelParser();
$result = $parser->parse('Treasy_DRE.xlsx');

echo "Total records: " . count($result['data']) . "\n\n";

// Show first 12 records (first linha)
echo "First 12 records (Linha 1):\n";
for ($i = 0; $i < min(12, count($result['data'])); $i++) {
    $r = $result['data'][$i];
    echo sprintf(
        "Mês %2d: planejado=%.2f, realizado=%.2f\n",
        $r['mes'],
        $r['valor_planejado'] ?? 0,
        $r['valor_realizado'] ?? 0
    );
}
