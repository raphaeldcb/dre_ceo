<?php
/**
 * DRE CEO Dashboard - Entry Point
 *
 * Pure PHP implementation (no dependencies)
 * All requests are routed through this file.
 * PSR-4 autoloading via autoload.php
 */

// Load environment configuration
if (!file_exists(__DIR__ . '/../.env')) {
    die('Error: .env file not found. Copy .env.example to .env and configure it.');
}

// Load PSR-4 autoloader (pure PHP, no Composer)
require_once __DIR__ . '/../autoload.php';

// Start session
session_start();

// Initialize CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get base path (set by root index.php or set default)
$basePath = $_SESSION['base_path'] ?? '';
define('BASE_PATH', $basePath);

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

// Dashboard routes
$router->get('/dashboard', function() {
    $controller = new \App\Controllers\DashboardController();
    $controller->index();
});

// Dashboard API routes
$router->get('/api/dashboard/area-data', function() {
    $controller = new \App\Controllers\DashboardController();
    $controller->apiAreaData();
});

$router->get('/api/dashboard/comparative-data', function() {
    $controller = new \App\Controllers\DashboardController();
    $controller->apiComparativeData();
});

$router->get('/api/dashboard/overview-summary', function() {
    $controller = new \App\Controllers\DashboardController();
    $controller->apiOverviewSummary();
});

$router->get('/api/dashboard/export-year', function() {
    $controller = new \App\Controllers\DashboardController();
    $controller->apiExportYear();
});

// Default route
$router->get('/', function() {
    echo '<h1>DRE CEO Dashboard</h1>';
    echo '<p><a href="/upload">📤 Upload DRE</a> | <a href="/dashboard">📊 Dashboard</a></p>';
});

// Dispatch the request
$router->dispatch();
?>
