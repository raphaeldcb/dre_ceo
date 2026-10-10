<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $db = App\Utils\Database::getInstance();

    // Check what's in linha 6 (Despesas Operacionais)
    $stmt = $db->prepare('
        SELECT mes, valor_planejado, valor_realizado
        FROM dre_valores
        WHERE dre_linha_id = 6
        ORDER BY mes ASC
        LIMIT 3
    ');
    $stmt->execute();

    echo "Dados na linha 6 (Despesas Operacionais):\n";
    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        echo sprintf(
            "Mês %d: planejado=%.2f, realizado=%.2f\n",
            $row['mes'],
            $row['valor_planejado'],
            $row['valor_realizado']
        );
    }

    echo "\n\nParece ser Margem de Contribuição? (y/n)\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
