<?php
/**
 * DRE CEO Dashboard - Universal Entry Point
 * Works anywhere: localhost, subfolders, production servers
 * No server configuration needed - uses only PHP
 */

// Get the base path dynamically
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath === '' || $basePath === '/') {
    $basePath = '';
} else {
    $basePath = '/' . trim($basePath, '/');
}

// Set base path in session for use in app
session_start();
$_SESSION['base_path'] = $basePath;

// Check if request is for a file/directory
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = str_replace($basePath, '', $requestUri);
$requestPath = '/' . ltrim($requestPath, '/');

// Handle static files (CSS, JS, images, etc)
if ($requestPath !== '/' && !in_array(pathinfo($requestPath, PATHINFO_EXTENSION), ['', 'php'])) {
    $filePath = __DIR__ . '/public' . $requestPath;
    if (file_exists($filePath) && is_file($filePath)) {
        // Let web server serve static files
        return false;
    }
}

// Rewrite to public/index.php
chdir('public');
require 'index.php';
?>
