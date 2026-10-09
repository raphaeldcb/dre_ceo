<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $db = App\Utils\Database::getInstance();

    // Check first 12 records (linha_id = 1)
    $stmt = $db->prepare('
        SELECT mes, valor_planejado, valor_realizado
        FROM dre_valores
        WHERE dre_linha_id = 1
        ORDER BY mes ASC
        LIMIT 12
    ');
    $stmt->execute();

    echo "Registros na tabela (linha_id = 1):\n";
    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        echo sprintf(
            "Mês %2d: planejado=%.2f, realizado=%.2f\n",
            $row['mes'],
            $row['valor_planejado'],
            $row['valor_realizado']
        );
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
