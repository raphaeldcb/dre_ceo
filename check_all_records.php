<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $db = App\Utils\Database::getInstance();

    // Total records
    $stmt = $db->prepare('SELECT COUNT(*) as total FROM dre_valores');
    $stmt->execute();
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    echo "Total registros no banco: " . $result['total'] . "\n\n";

    // Group by dre_linha_id
    $stmt = $db->prepare('SELECT dre_linha_id, COUNT(*) as count FROM dre_valores GROUP BY dre_linha_id ORDER BY dre_linha_id');
    $stmt->execute();
    echo "Registros por linha:\n";
    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        echo "  Linha " . $row['dre_linha_id'] . ": " . $row['count'] . " registros\n";
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
