<?php

namespace App\Utils;

class Router
{
    private $routes = [];
    private $middlewares = [];

    public function get(string $path, callable $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    public function post(string $path, callable $handler): self
    {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    public function middleware(string $name, callable $handler): self
    {
        $this->middlewares[$name] = $handler;
        return $this;
    }

    public function dispatch(?string $method = null, ?string $path = null): void
    {
        $method = $method ?? $_SERVER['REQUEST_METHOD'];
        $path = $path ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Normalize path (remove trailing slash unless it's root)
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }

        http_response_code(404);
        echo "404 Not Found: {$method} {$path}";
    }

    public function applyMiddleware(string $name, callable $callback): void
    {
        if (isset($this->middlewares[$name])) {
            call_user_func($this->middlewares[$name], $callback);
        } else {
            $callback();
        }
    }
}
