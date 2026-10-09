<?php
require_once __DIR__ . '/autoload.php';

use App\Utils\Database;

try {
    $conn = Database::getInstance();

    // Check table structure
    echo "📊 Estrutura da tabela 'uploads':\n";
    echo "================================\n\n";

    $stmt = $conn->query("DESCRIBE uploads");
    $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($columns as $col) {
        echo "- {$col['Field']}: {$col['Type']}";
        echo ($col['Null'] === 'NO' ? ' [NOT NULL]' : '');
        echo ($col['Key'] ? " [KEY: {$col['Key']}]" : '');
        echo "\n";
    }

    echo "\n📋 Constraints:\n";
    echo "================================\n\n";

    $stmt = $conn->query("SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'uploads'");
    $constraints = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($constraints as $c) {
        echo "- {$c['CONSTRAINT_NAME']}: {$c['CONSTRAINT_TYPE']}\n";
    }

    echo "\n🔍 Check Constraints:\n";
    echo "================================\n\n";

    $stmt = $conn->query("SELECT CONSTRAINT_NAME, CHECK_CLAUSE FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE()");
    $checks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($checks as $c) {
        echo "- {$c['CONSTRAINT_NAME']}: {$c['CHECK_CLAUSE']}\n";
    }

} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
