<?php

namespace App\Core;

/**
 * Router - Sistema de Roteamento
 * Implementa o padrão Front Controller
 */
class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
    }

    /**
     * Adiciona rota GET
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Adiciona rota POST
     */
    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Adiciona rota (qualquer método)
     */
    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Processa a requisição atual
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover basePath da URI
        if ($this->basePath && strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }

        // Garantir que começa com /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }

        // Buscar rota correspondente
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Remove o match completo
                    $this->callHandler($route['handler'], $matches);
                    return;
                }
            }
        }

        // Rota não encontrada - tentar compatibilidade com arquivos antigos
        $this->handleLegacyRoute($requestUri);
    }

    /**
     * Converte padrão de rota para regex
     */
    private function convertToRegex(string $path): string
    {
        // Substituir parâmetros {id} por regex
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Executa o handler da rota
     */
    private function callHandler(callable|array $handler, array $params = []): void
    {
        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        } else {
            call_user_func_array($handler, $params);
        }
    }

    /**
     * Trata rotas legadas (arquivos PHP antigos)
     */
    private function handleLegacyRoute(string $uri): void
    {
        // Remover trailing slash
        $uri = rtrim($uri, '/');
        
        // Mapear rotas legadas comuns
        $legacyMap = [
            '/login' => 'login.php',
            '/cadastro' => 'cadastro.php',
            '/logout' => 'logout.php',
            '/painel-admin' => 'painel_admin.php',
            '/painel-cliente' => 'painel_cliente.php',
            '/painel-organizador' => 'painel_organizador.php',
            '/meus-ingressos' => 'meus_ingressos.php',
            '/perfil' => 'perfil_usuario.php',
        ];

        if (isset($legacyMap[$uri])) {
            $file = __DIR__ . '/../../' . $legacyMap[$uri];
            if (file_exists($file)) {
                require $file;
                return;
            }
        }

        // Tentar arquivo direto
        $possibleFile = __DIR__ . '/../../' . ltrim($uri, '/');
        if (file_exists($possibleFile . '.php')) {
            require $possibleFile . '.php';
            return;
        }

        // 404
        http_response_code(404);
        echo "Página não encontrada: $uri";
    }
}
