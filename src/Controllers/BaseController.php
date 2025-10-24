<?php

namespace App\Controllers;

/**
 * BaseController - Classe base para todos os Controllers
 * Contém métodos utilitários comuns
 */
abstract class BaseController
{
    /**
     * Carrega uma view
     * 
     * @param string $viewName Nome da view (ex: 'auth/login')
     * @param array $data Dados para passar à view
     */
    protected function loadView(string $viewName, array $data = []): void
    {
        // Iniciar sessão se necessário
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        extract($data);
        $viewPath = __DIR__ . '/../../views/' . $viewName . '.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            // Fallback para arquivos antigos na raiz
            $oldPath = __DIR__ . '/../../' . $viewName . '.php';
            if (file_exists($oldPath)) {
                require $oldPath;
            } else {
                http_response_code(404);
                echo "View não encontrada: $viewName";
            }
        }
    }

    /**
     * Redireciona para uma URL
     * 
     * @param string $url URL de destino
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Retorna JSON
     * 
     * @param mixed $data Dados para retornar
     * @param int $statusCode Código HTTP
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Define mensagem flash na sessão
     * 
     * @param string $message Mensagem
     * @param string $type Tipo (success, danger, warning, info)
     */
    protected function setFlash(string $message, string $type = 'info'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['mensagem'] = $message;
        $_SESSION['mensagem_tipo'] = $type;
    }

    /**
     * Verifica se usuário está autenticado
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']);
    }

    /**
     * Requer autenticação - redireciona para login se não autenticado
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->setFlash('Você precisa estar logado para acessar esta página.', 'warning');
            $this->redirect('/login');
        }
    }

    /**
     * Verifica se usuário tem um tipo específico
     * 
     * @param string $tipo Tipo esperado (ADMIN, ORGANIZADOR, CLIENTE)
     * @return bool
     */
    protected function hasUserType(string $tipo): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === $tipo;
    }

    /**
     * Requer tipo de usuário específico
     * 
     * @param string $tipo Tipo requerido
     */
    protected function requireUserType(string $tipo): void
    {
        $this->requireAuth();
        
        if (!$this->hasUserType($tipo)) {
            $this->setFlash('Você não tem permissão para acessar esta página.', 'danger');
            $this->redirect('/');
        }
    }

    /**
     * Obtém dados do usuário logado
     * 
     * @return array|null
     */
    protected function getCurrentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nome' => $_SESSION['usuario_nome'] ?? null,
            'email' => $_SESSION['usuario_email'] ?? null,
            'tipo' => $_SESSION['usuario_tipo'] ?? null,
        ];
    }

    /**
     * Sanitiza entrada do usuário
     * 
     * @param string $input
     * @return string
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida CSRF token (básico)
     * 
     * @return bool
     */
    protected function validateCsrf(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        return !empty($token) && hash_equals($sessionToken, $token);
    }

    /**
     * Gera CSRF token
     * 
     * @return string
     */
    protected function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}
