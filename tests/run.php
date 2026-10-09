<?php

/**
 * Test Runner - Executa todos os testes do projeto
 * Sem dependências externas - usa autoloader simples
 *
 * Uso: php tests/run.php
 */

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/TestCase.php';

// Importar testes
require_once __DIR__ . '/ModelsTest.php';

// ExcelParserTest requer PhpSpreadsheet (opcional)
$hasExcelTest = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/ExcelParserTest.php';
    $hasExcelTest = true;
}

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║   DRE CEO Dashboard - Test Suite                              ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$testClasses = $hasExcelTest
    ? ['ExcelParserTest', 'ModelsTest']
    : ['ModelsTest'];

if (!$hasExcelTest) {
    echo "⚠️  ExcelParserTest skipped (requires PhpSpreadsheet)\n";
    echo "   To test ExcelParser, run: composer install\n\n";
}

$totalTests = 0;
$totalPassed = 0;
$totalFailed = 0;
$allErrors = [];

foreach ($testClasses as $testClass) {
    echo "Running {$testClass}...\n";
    echo str_repeat("-", 60) . "\n";

    $test = new $testClass();
    $test->run();

    $results = $test->getResults();
    $totalTests += $results['total'];
    $totalPassed += $results['passed'];
    $totalFailed += $results['failed'];
    $allErrors = array_merge($allErrors, $results['errors']);

    echo "\n✓ {$results['passed']}/{$results['total']} tests passed\n\n";
}

// Summary
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Test Summary                                                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "Total Tests:  $totalTests\n";
echo "Passed:       $totalPassed ✅\n";
echo "Failed:       $totalFailed ❌\n\n";

if ($totalFailed > 0) {
    echo "Errors:\n";
    foreach ($allErrors as $error) {
        echo "  • $error\n";
    }
    echo "\n";
    exit(1);
} else {
    echo "🎉 All tests passed!\n\n";
    exit(0);
}
