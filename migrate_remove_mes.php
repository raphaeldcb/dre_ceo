<?php
/**
 * Migration: Remove mes field from uploads table
 */

require_once __DIR__ . '/autoload.php';

use App\Utils\Database;

try {
    $conn = Database::getInstance();

    echo "🔄 Iniciando migração: remover campo 'mes' da tabela 'uploads'\n\n";

    // Step 1: Drop the CHECK constraint
    echo "Step 1: Removendo constraint CHECK...\n";
    $conn->exec("ALTER TABLE uploads DROP CONSTRAINT uploads_chk_1");
    echo "✅ Constraint removida\n\n";

    // Step 2: Drop the mes column
    echo "Step 2: Removendo coluna 'mes'...\n";
    $conn->exec("ALTER TABLE uploads DROP COLUMN mes");
    echo "✅ Coluna removida\n\n";

    echo "✨ Migração concluída com sucesso!\n";

    // Verify
    echo "\n📊 Estrutura atual da tabela 'uploads':\n";
    echo "================================\n";
    $stmt = $conn->query("DESCRIBE uploads");
    $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($columns as $col) {
        echo "- {$col['Field']}: {$col['Type']}";
        echo ($col['Null'] === 'NO' ? ' [NOT NULL]' : '');
        echo "\n";
    }

} catch (\Exception $e) {
    echo "❌ Erro durante migração: " . $e->getMessage() . "\n";
    exit(1);
}
