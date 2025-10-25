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
        // Aplicar tema e idioma o mais cedo possível
        try { if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark-theme'); } } catch (e) {}
        try {
            var lang = localStorage.getItem('lang') || 'pt';
            document.documentElement.setAttribute('lang', lang === 'en' ? 'en' : 'pt-BR');
        } catch (e) {}
    </script>
    <!-- Preconectar a domínios externos para melhorar o tempo de carregamento -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <script src="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'js/theme.js' : '../js/theme.js'; ?>" defer></script>
    <script src="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'js/lang.js' : '../js/lang.js'; ?>" defer></script>
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
                <li>
                    <button id="lang-toggle" class="btn-lang-toggle" aria-label="Alternar idioma" title="Alternar idioma">
                        <i class="fas fa-globe"></i>
                        <span id="lang-toggle-label" class="lang-toggle-label">PT</span>
                    </button>
                </li>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['usuario_tipo'] == 'CLIENTE'): ?>
                        <li><a href="<?php echo htmlspecialchars($base); ?>painel_cliente.php" data-i18n="nav.home">Início</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>eventos/listar_eventos.php" data-i18n="nav.events">Eventos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>meus_ingressos.php" data-i18n="nav.my_tickets">Meus Ingressos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>painel_cliente.php?tab=pedidos" data-i18n="nav.my_orders">Meus Pedidos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>perfil_usuario.php" data-i18n="nav.my_profile">Meu Perfil</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>logout.php" data-i18n="nav.logout">Sair</a></li>
                    <?php elseif ($_SESSION['usuario_tipo'] == 'ORGANIZADOR'): ?>
                        <li><a href="<?php echo htmlspecialchars($base); ?>painel_organizador.php" data-i18n="nav.home">Início</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>eventos/gerenciar_eventos.php" data-i18n="nav.my_events">Meus Eventos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>eventos/criar_evento.php" data-i18n="nav.create_event">Criar Evento</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>validar_ingresso.php" data-i18n="nav.validate_tickets">Validar Ingressos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>perfil_usuario.php" data-i18n="nav.my_profile">Meu Perfil</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>pagamentos.php" data-i18n="nav.plans">Planos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>logout.php" data-i18n="nav.logout">Sair</a></li>
                    <?php elseif ($_SESSION['usuario_tipo'] == 'ADMIN'): ?>
                        <li><a href="<?php echo htmlspecialchars($base); ?>painel_admin.php" data-i18n="nav.home">Início</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>admin/gerenciar_usuarios.php" data-i18n="nav.users">Usuários</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>eventos/gerenciar_eventos.php?admin=true" data-i18n="nav.events">Eventos</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>admin/relatorios.php" data-i18n="nav.reports">Relatórios</a></li>
                        <li><a href="<?php echo htmlspecialchars($base); ?>logout.php" data-i18n="nav.logout">Sair</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?php echo htmlspecialchars($base); ?>index.php" data-i18n="nav.home">Início</a></li>
                    <li><a href="<?php echo htmlspecialchars($base); ?>eventos/listar_eventos.php" data-i18n="nav.events">Eventos</a></li>
                    <li><a href="<?php echo htmlspecialchars($base); ?>login.php" data-i18n="nav.login">Login</a></li>
                    <li><a href="<?php echo htmlspecialchars($base); ?>cadastro.php" data-i18n="nav.signup">Cadastro</a></li>
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
