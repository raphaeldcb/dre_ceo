<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $db = App\Utils\Database::getInstance();
    $stmt = $db->prepare('SELECT id, nome FROM dre_linhas ORDER BY ordem');
    $stmt->execute();

    echo "Linhas DRE disponíveis:\n";
    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        echo $row['id'] . ': ' . $row['nome'] . "\n";
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
