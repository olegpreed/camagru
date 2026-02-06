<?php

/**
 * Entry point for the application
 * All requests are routed through this file
 */

// Load autoloader
require_once __DIR__ . '/../src/Config/autoload.php';

use Core\Router;
use Core\Session;

// Start session
Session::start();

// Create router
$router = new Router();

// Define routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('/', ['controller' => 'Home', 'action' => 'index']);
$router->add('home', ['controller' => 'Home', 'action' => 'index']);

// Authentication routes
$router->add('auth/register', ['controller' => 'Auth', 'action' => 'register']);
$router->add('auth/register-post', ['controller' => 'Auth', 'action' => 'registerPost']);
$router->add('auth/forgot-password', ['controller' => 'User', 'action' => 'forgotPassword']);
$router->add('verify', ['controller' => 'Auth', 'action' => 'verify']);
$router->add('auth/login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('auth/logout', ['controller' => 'Auth', 'action' => 'logout']);

// User routes
$router->add('user/profile', ['controller' => 'User', 'action' => 'profile']);
$router->add('user/edit-profile', ['controller' => 'User', 'action' => 'editProfile']);
$router->add('user/change-password', ['controller' => 'User', 'action' => 'changePassword']);
$router->add('user/reset-password', ['controller' => 'User', 'action' => 'resetPassword']);

// Image routes
$router->add('image/edit', ['controller' => 'Image', 'action' => 'edit']);
$router->add('image/getCsrfToken', ['controller' => 'Image', 'action' => 'getCsrfToken']);
$router->add('image/compose', ['controller' => 'Image', 'action' => 'compose']);
$router->add('image/getUserImages', ['controller' => 'Image', 'action' => 'getUserImages']);
$router->add('image/delete', ['controller' => 'Image', 'action' => 'delete']);
$router->add('image/upload', ['controller' => 'Image', 'action' => 'upload']);
$router->add('gallery', ['controller' => 'Image', 'action' => 'gallery']);

// Get the URL path
$url = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string
$url = parse_url($url, PHP_URL_PATH);

// Remove leading slash
$url = ltrim($url, '/');

// Empty string for root
if ($url === '') {
    $url = '';
}

// Dispatch the route
try {
    $router->dispatch($url);
} catch (\Exception $e) {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}