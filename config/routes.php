<?php
/**
 * Arquivo de Rotas - Configuração do sistema de rotas MVC
 * Este arquivo define todas as rotas da aplicação seguindo o padrão MVC
 */

use App\Core\Router;
use App\Controllers\AuthController;

// Criar instância do Router
$router = new Router();

// ==========================================
// ROTAS DE AUTENTICAÇÃO
// ==========================================
$router->get('/auth/login', [AuthController::class, 'showLogin']);
$router->post('/auth/login', [AuthController::class, 'processLogin']);
$router->get('/auth/cadastro', [AuthController::class, 'showRegister']);
$router->post('/auth/cadastro', [AuthController::class, 'processRegister']);
$router->get('/auth/logout', [AuthController::class, 'logout']);

// ==========================================
// ROTA PRINCIPAL (Front Controller)
// ==========================================
$router->get('/', function() {
    // Redirecionar para página apropriada baseado no usuário
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /auth/login");
        exit;
    }

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/funcoes.php';
    require_once __DIR__ . '/../includes/verificar_eventos_expirados.php';
    
    $conexao = conectarBD();
    atualizarEventosExpirados($conexao);
    deletarEventosExpirados($conexao);
    
    $tipo_usuario = $_SESSION['usuario_tipo'];
    if ($tipo_usuario == 'ADMIN') {
        header("Location: /painel_admin.php");
    } elseif ($tipo_usuario == 'ORGANIZADOR') {
        header("Location: /painel_organizador.php");
    } else {
        header("Location: /painel_cliente.php");
    }
    exit;
});

return $router;
