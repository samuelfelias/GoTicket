<?php
// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'includes/funcoes.php';
require_once 'includes/verificar_eventos_expirados.php';

// Redirecionar para a página de login se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obter informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$conexao = conectarBD();

// Atualizar status de eventos expirados
atualizarEventosExpirados($conexao);

// Deletar eventos expirados automaticamente
deletarEventosExpirados($conexao);

// Redirecionar para o painel apropriado com base no tipo de usuário
$tipo_usuario = $_SESSION['usuario_tipo'];
if ($tipo_usuario == 'ADMIN') {
    header("Location: painel_admin.php");
} elseif ($tipo_usuario == 'ORGANIZADOR') {
    header("Location: painel_organizador.php");
} else {
    header("Location: painel_cliente.php");
}
exit;
?>
