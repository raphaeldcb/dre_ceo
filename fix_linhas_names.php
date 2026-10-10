<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $db = App\Utils\Database::getInstance();

    // Corrigir os nomes das linhas
    $updates = [
        4 => 'Margem de Contribuição Bruta',
        5 => 'Gastos e Despesas Variáveis',
        6 => 'Margem de Contribuição',
        7 => 'Gastos e Despesas Fixas',
    ];

    foreach ($updates as $id => $nome) {
        $stmt = $db->prepare('UPDATE dre_linhas SET nome = ? WHERE id = ?');
        $stmt->execute([$nome, $id]);
        echo "✓ Linha $id: $nome\n";
    }

    echo "\n✓ Linhas corrigidas!\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
