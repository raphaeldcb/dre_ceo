<?php

require 'autoload.php';

$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_PORT'] = 3306;
$_ENV['DB_NAME'] = 'dre_ceo_dev';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASSWORD'] = '';

try {
    $db = App\Utils\Database::getInstance();

    // Delete all records from dre_valores
    $stmt = $db->prepare('DELETE FROM dre_valores');
    $stmt->execute();

    $count = $stmt->rowCount();
    echo "✓ Deleted {$count} records from dre_valores\n";

    // Delete all records from uploads
    $stmt = $db->prepare('DELETE FROM uploads');
    $stmt->execute();

    $count = $stmt->rowCount();
    echo "✓ Deleted {$count} records from uploads\n";

    echo "\nBanco limpo! Pronto para novo upload.\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
