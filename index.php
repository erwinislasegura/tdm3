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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$detectedBaseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$detectedBaseDir = ($detectedBaseDir === '.' || $detectedBaseDir === '/') ? '' : $detectedBaseDir;

$config = require BASE_PATH . '/config/app.php';
if (empty($config['app']['base_path'])) {
    $config['app']['base_path'] = $detectedBaseDir;
}
App\Core\Container::set('config', $config);

if ($detectedBaseDir && strpos($requestUri, $detectedBaseDir) === 0) {
    $requestUri = substr($requestUri, strlen($detectedBaseDir)) ?: '/';
}

if ($requestUri === '/index.php' || $requestUri === '/index.php/') {
    $requestUri = '/';
} elseif (strpos($requestUri, '/index.php/') === 0) {
    $requestUri = substr($requestUri, strlen('/index.php')) ?: '/';
}

$uri = '/' . ltrim($requestUri, '/');

$router = new App\Core\Router();
require BASE_PATH . '/routes/web.php';

try {
    $router->dispatch($method, $uri);
} catch (\Throwable $exception) {
    error_log(sprintf('[%s] %s in %s:%d', date('c'), $exception->getMessage(), $exception->getFile(), $exception->getLine()));
    http_response_code(200);
    (new App\Controllers\AuthController())->loginForm();
}
