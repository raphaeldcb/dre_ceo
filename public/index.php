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

// Session already started by parent index.php, skip if already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get base path (set by root index.php or set default)
$basePath = $_SESSION['base_path'] ?? '';
define('BASE_PATH', $basePath);

// Initialize router and define routes
$router = new \App\Utils\Router();

// Auth routes
$router->get('/', function() {
    $controller = new \App\Controllers\AuthController();
    $controller->showLogin();
});

$router->post('/api/login', function() {
    $controller = new \App\Controllers\AuthController();
    $controller->login();
});

$router->get('/home', function() {
    $controller = new \App\Controllers\AuthController();
    $controller->showHome();
});

$router->get('/logout', function() {
    $controller = new \App\Controllers\AuthController();
    $controller->logout();
});

// Upload routes (protected)
$router->get('/upload', function() {
    requireAuth();
    $controller = new \App\Controllers\UploadController();
    $controller->showForm();
});

$router->post('/api/upload', function() {
    requireAuth();
    $controller = new \App\Controllers\UploadController();
    $controller->handle();
});

// Dashboard routes (protected)
$router->get('/dashboard', function() {
    requireAuth();
    $controller = new \App\Controllers\DashboardController();
    $controller->index();
});

// Dashboard API routes (protected)
$router->get('/api/dashboard/area-data', function() {
    requireAuth();
    $controller = new \App\Controllers\DashboardController();
    $controller->apiAreaData();
});

$router->get('/api/dashboard/comparative-data', function() {
    requireAuth();
    $controller = new \App\Controllers\DashboardController();
    $controller->apiComparativeData();
});

$router->get('/api/dashboard/overview-summary', function() {
    requireAuth();
    $controller = new \App\Controllers\DashboardController();
    $controller->apiOverviewSummary();
});

$router->get('/api/dashboard/export-year', function() {
    requireAuth();
    $controller = new \App\Controllers\DashboardController();
    $controller->apiExportYear();
});

// Helper function to check authentication
function requireAuth() {
    if (empty($_SESSION['user_id'])) {
        header('Location: /');
        exit;
    }
}

// Dispatch the request
$router->dispatch();
?>
