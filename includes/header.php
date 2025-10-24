<?php
// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Não redirecionamos aqui para evitar loops de redirecionamento
// A verificação de login deve ser feita na página específica, não no header
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoTicket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'css/style.css' : '../css/style.css'; ?>">
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'css/password-toggle.css' : '../css/password-toggle.css'; ?>">
    <script>
        // Aplicar tema o mais cedo possível
        try { if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark-theme'); } } catch (e) {}
    </script>
    <!-- Preconectar a domínios externos para melhorar o tempo de carregamento -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <script src="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'js/theme.js' : '../js/theme.js'; ?>" defer></script>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <?php
                $isRoot = (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) || (substr($_SERVER['REQUEST_URI'], -1) === '/');
                $base = $isRoot ? '' : '../';
                $homeHref = $base . 'index.php';
                if (isset($_SESSION['usuario_id'])) {
                    if ($_SESSION['usuario_tipo'] === 'CLIENTE') { $homeHref = $base . 'painel_cliente.php'; }
                    elseif ($_SESSION['usuario_tipo'] === 'ORGANIZADOR') { $homeHref = $base . 'painel_organizador.php'; }
                    elseif ($_SESSION['usuario_tipo'] === 'ADMIN') { $homeHref = $base . 'painel_admin.php'; }
                }
            ?>
            <div class="logo"><a href="<?php echo htmlspecialchars($homeHref); ?>" aria-label="Ir para a página inicial">GoTicket</a></div>
            <button class="menu-toggle" id="menu-toggle" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-menu" id="nav-menu">
                <li>
                    <button id="theme-toggle" class="btn-theme-toggle" aria-label="Alternar tema" title="Alternar tema claro/escuro">
                        <span class="theme-toggle-icon">
                            <i class="fas fa-moon"></i>
                            <i class="fas fa-sun"></i>
                        </span>
                    </button>
                </li>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_tipo'] == 'CLIENTE'): ?>
                        <li><a href="../painel_cliente.php">Início</a></li>
                        <li><a href="../eventos/listar_eventos.php">Eventos</a></li>
                        <li><a href="../meus_ingressos.php">Meus Ingressos</a></li>
                        <li><a href="../painel_cliente.php?tab=pedidos">Meus Pedidos</a></li>
                        <li><a href="../perfil_usuario.php">Meu Perfil</a></li>
                        <li><a href="../logout.php">Sair</a></li>
                    <?php elseif ($_SESSION['usuario_tipo'] == 'ORGANIZADOR'): ?>
                        <li><a href="../painel_organizador.php">Início</a></li>
                        <li><a href="../eventos/gerenciar_eventos.php">Meus Eventos</a></li>
                        <li><a href="../eventos/criar_evento.php">Criar Evento</a></li>
                        <li><a href="../validar_ingresso.php">Validar Ingressos</a></li>
                        <li><a href="../perfil_usuario.php">Meu Perfil</a></li>
                        <li><a href="../pagamentos.php">Planos</a></li>
                        <li><a href="../logout.php">Sair</a></li>
                    <?php elseif ($_SESSION['usuario_tipo'] == 'ADMIN'): ?>
                        <li><a href="../painel_admin.php">Início</a></li>
                        <li><a href="../admin/gerenciar_usuarios.php">Usuários</a></li>
                        <li><a href="../eventos/gerenciar_eventos.php?admin=true">Eventos</a></li>
                        <li><a href="../admin/relatorios.php">Relatórios</a></li>
                        <li><a href="../logout.php">Sair</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="../index.php">Início</a></li>
                    <li><a href="../eventos/listar_eventos.php">Eventos</a></li>
                    <li><a href="../login.php">Login</a></li>
                    <li><a href="../cadastro.php">Cadastro</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <div class="container main-content">
    
    <script>
        // Menu mobile toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const navMenu = document.getElementById('nav-menu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });
                
                // Fechar menu ao clicar em um link
                const navLinks = navMenu.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                    });
                });
                
                // Fechar menu ao clicar fora dele
                document.addEventListener('click', function(event) {
                    if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                        navMenu.classList.remove('active');
                    }
                });
            }
        });
    </script>
