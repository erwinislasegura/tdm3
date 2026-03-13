<?php

declare(strict_types=1);

session_start();

define('BASE_PATH', __DIR__);

require BASE_PATH . '/app/helpers/functions.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$config = require BASE_PATH . '/config/app.php';
App\Core\Container::set('config', $config);

$router = new App\Core\Router();
require BASE_PATH . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($baseDir && $baseDir !== '.' && str_starts_with($requestUri, $baseDir)) {
    $requestUri = substr($requestUri, strlen($baseDir)) ?: '/';
}

if ($requestUri === '/index.php' || $requestUri === '/index.php/') {
    $requestUri = '/';
} elseif (str_starts_with($requestUri, '/index.php/')) {
    $requestUri = substr($requestUri, strlen('/index.php')) ?: '/';
}

$uri = '/' . ltrim($requestUri, '/');
$router->dispatch($method, $uri);
