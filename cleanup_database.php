<?php
/**
 * Database Cleanup Script
 * Remove areas 5-8 and optionally clear all data
 */

require_once __DIR__ . '/autoload.php';

use App\Database\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    echo "🧹 Iniciando limpeza do banco de dados...\n\n";

    // Get user input
    echo "Selecione a opção:\n";
    echo "1 - Remover dados das áreas 5-8 apenas\n";
    echo "2 - Limpar TODOS os dados (manter estrutura)\n";
    echo "3 - Remover e recriar estrutura completa\n";
    echo "\nOpção (1-3): ";

    $input = trim(fgets(STDIN));

    if (!in_array($input, ['1', '2', '3'])) {
        echo "❌ Opção inválida!\n";
        exit(1);
    }

    // Option 1: Remove areas 5-8
    if ($input === '1') {
        echo "\n🔄 Removendo dados das áreas 5-8...\n";
        $stmt = $conn->prepare("DELETE FROM dre_values WHERE area_id > 4");
        $stmt->execute();
        $deletedRows = $stmt->rowCount();
        echo "✅ {$deletedRows} registros removidos das áreas 5-8\n";
    }

    // Option 2: Clear all data
    elseif ($input === '2') {
        echo "\n⚠️  ATENÇÃO: Você está prestes a limpar TODOS os dados!\n";
        echo "Digite 'confirmar' para prosseguir: ";
        $confirm = trim(fgets(STDIN));

        if ($confirm !== 'confirmar') {
            echo "❌ Operação cancelada!\n";
            exit(1);
        }

        echo "\n🔄 Removendo todos os dados...\n";
        $stmt = $conn->prepare("TRUNCATE TABLE dre_values");
        $stmt->execute();
        echo "✅ Todos os dados foram removidos (estrutura mantida)\n";
    }

    // Option 3: Full reset
    elseif ($input === '3') {
        echo "\n⚠️  ATENÇÃO: Você está prestes a recriar o banco COMPLETAMENTE!\n";
        echo "Digite 'confirmar' para prosseguir: ";
        $confirm = trim(fgets(STDIN));

        if ($confirm !== 'confirmar') {
            echo "❌ Operação cancelada!\n";
            exit(1);
        }

        echo "\n🔄 Removendo tabelas...\n";
        $conn->exec("DROP TABLE IF EXISTS dre_values");
        echo "✅ Tabelas removidas\n";

        echo "\n🔄 Recriando estrutura do banco...\n";
        $schema = "
            CREATE TABLE dre_values (
                id INT AUTO_INCREMENT PRIMARY KEY,
                area_id INT NOT NULL,
                linha_id INT NOT NULL,
                ano INT NOT NULL,
                mes INT NOT NULL,
                valor_planejado DECIMAL(15, 2) DEFAULT 0,
                valor_realizado DECIMAL(15, 2) DEFAULT 0,
                variancia DECIMAL(15, 2) DEFAULT 0,
                percentual_realizacao DECIMAL(10, 2) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_entry (area_id, linha_id, ano, mes),
                INDEX idx_area_ano (area_id, ano),
                INDEX idx_ano (ano),
                INDEX idx_linha_id (linha_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $conn->exec($schema);
        echo "✅ Estrutura recriada com sucesso\n";
    }

    echo "\n✨ Limpeza concluída!\n";
    echo "\n📊 Status do banco de dados:\n";

    $stmt = $conn->query("SELECT COUNT(*) as total FROM dre_values");
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    echo "- Total de registros: {$result['total']}\n";

    $stmt = $conn->query("SELECT COUNT(DISTINCT area_id) as total FROM dre_values");
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    echo "- Áreas com dados: {$result['total']}\n";

    $stmt = $conn->query("SELECT COUNT(DISTINCT ano) as total FROM dre_values");
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    echo "- Anos com dados: {$result['total']}\n";

} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
