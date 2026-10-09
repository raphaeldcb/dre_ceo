<?php
/**
 * DRE CEO Dashboard - Entry Point
 *
 * All requests are routed through this file.
 * PSR-4 autoloading is configured via Composer.
 */

// Load environment configuration
if (!file_exists(__DIR__ . '/../.env')) {
    die('Error: .env file not found. Copy .env.example to .env and configure it.');
}

require_once __DIR__ . '/../vendor/autoload.php';

// Start session
session_start();

// Initialize CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize router and define routes
$router = new \App\Utils\Router();

// Upload routes
$router->get('/upload', function() {
    $controller = new \App\Controllers\UploadController();
    $controller->showForm();
});

$router->post('/api/upload', function() {
    $controller = new \App\Controllers\UploadController();
    $controller->handle();
});

// Default route
$router->get('/', function() {
    echo '<h1>DRE CEO Dashboard</h1>';
    echo '<p><a href="/upload">Upload DRE</a></p>';
});

// Dispatch the request
$router->dispatch();
?>
