<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Database\Database;

/**
 * Controller de Autenticação
 * Segue o padrão MVC: Controller recebe requisições e coordena Model e View
 */
class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->authService = new AuthService($db->getConnection());
    }

    /**
     * Exibe página de login
     */
    public function showLogin(): void
    {
        $this->loadView('auth/login');
    }

    /**
     * Processa login
     */
    public function processLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $resultado = $this->authService->login($email, $senha);

        if ($resultado['success']) {
            // Iniciar sessão
            $this->startUserSession($resultado['user']);
            
            // Atualizar eventos expirados
            require_once __DIR__ . '/../../includes/verificar_eventos_expirados.php';
            $conexao = Database::getInstance()->getConnection();
            atualizarEventosExpirados($conexao);
            deletarEventosExpirados($conexao);

            $this->redirect('/');
        } else {
            $this->setFlash($resultado['message'], 'danger');
            $this->redirect('/login');
        }
    }

    /**
     * Exibe página de cadastro
     */
    public function showRegister(): void
    {
        $this->loadView('auth/cadastro');
    }

    /**
     * Processa cadastro
     */
    public function processRegister(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cadastro');
            return;
        }

        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'cpf' => $_POST['cpf'] ?? '',
            'email' => $_POST['email'] ?? '',
            'senha' => $_POST['senha'] ?? '',
            'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
            'tipo' => 'CLIENTE'
        ];

        $resultado = $this->authService->registrar($dados);

        $this->setFlash($resultado['message'], $resultado['success'] ? 'success' : 'danger');

        if ($resultado['success']) {
            $this->redirect('/login');
        } else {
            $this->redirect('/cadastro');
        }
    }

    /**
     * Processa logout
     */
    public function logout(): void
    {
        session_start();
        session_destroy();
        $this->redirect('/login');
    }

    /**
     * Inicia sessão do usuário
     */
    private function startUserSession(array $usuario): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
    }

}
