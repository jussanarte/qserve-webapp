<?php
namespace App\Router;

use App\Helpers\ResponseHelper;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

   public function dispatch(): void {
    $method = $_SERVER['REQUEST_METHOD'];

    // Extrai apenas o PATH da URI (sem query string)
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Remove o caminho do script (ex: /qserve-webapp/backend/public/index.php)
    // para ficar apenas com /api/auth/register
    $scriptName = $_SERVER['SCRIPT_NAME']; // /qserve-webapp/backend/public/index.php
    if (str_starts_with($uri, $scriptName)) {
        $uri = substr($uri, strlen($scriptName));
    }

    // Normaliza: remove trailing slash, garante que começa com /
    $uri = '/' . trim($uri, '/');
    if ($uri === '/') $uri = '/';

    foreach ($this->routes as $route) {
        $params = $this->match($route['path'], $uri);
        if ($route['method'] === $method && $params !== null) {
            call_user_func_array($route['handler'], $params);
            return;
        }
    }

    ResponseHelper::error('Rota não encontrada', 404);
}
    private function match(string $routePath, string $uri): ?array {
        $pattern = preg_replace('/\/:([^\/]+)/', '/(\d+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            return array_map('intval', $matches);
        }
        return null;
    }
}