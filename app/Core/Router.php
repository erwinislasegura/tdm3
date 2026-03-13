<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action, array $middlewares = []): void
    {
        $this->add('GET', $uri, $action, $middlewares);
    }

    public function post(string $uri, array $action, array $middlewares = []): void
    {
        $this->add('POST', $uri, $action, $middlewares);
    }

    private function add(string $method, string $uri, array $action, array $middlewares): void
    {
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([0-9]+)', $uri);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[$method][] = compact('uri', 'pattern', 'action', 'middlewares');
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            foreach ($route['middlewares'] as $middlewareClass) {
                (new $middlewareClass())->handle();
            }

            array_shift($matches);
            [$controllerClass, $controllerMethod] = $route['action'];
            (new $controllerClass())->{$controllerMethod}(...$matches);
            return;
        }

        http_response_code(404);
        echo 'Ruta no encontrada';
    }
}
