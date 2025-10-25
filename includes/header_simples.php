<?php
// Iniciar sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se existe preferência de tema salva na sessão
$tema_escuro = isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? true : false;
?>

<header class="header header-simples">
    <div class="container header-content">
        <?php
            $homeHref = 'index.php';
            if (isset($_SESSION['usuario_id'])) {
                if ($_SESSION['usuario_tipo'] === 'CLIENTE') { $homeHref = 'painel_cliente.php'; }
                elseif ($_SESSION['usuario_tipo'] === 'ORGANIZADOR') { $homeHref = 'painel_organizador.php'; }
                elseif ($_SESSION['usuario_tipo'] === 'ADMIN') { $homeHref = 'painel_admin.php'; }
            }
        ?>
        <div class="logo"><a href="<?php echo htmlspecialchars($homeHref); ?>" aria-label="Ir para a página inicial">GoTicket</a></div>
        <div class="nav-menu-simples">
            <button id="theme-toggle" class="btn-theme-toggle" aria-label="Alternar tema" title="Alternar tema claro/escuro">
                <span class="theme-toggle-icon">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                </span>
            </button>
            <button id="lang-toggle" class="btn-lang-toggle" aria-label="Alternar idioma" title="Alternar idioma">
                <i class="fas fa-globe"></i>
                <span id="lang-toggle-label" class="lang-toggle-label">PT</span>
            </button>
        </div>
    </div>
    <script>
        try { if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark-theme'); } } catch(e) {}
        try {
            var lang = localStorage.getItem('lang') || 'pt';
            document.documentElement.setAttribute('lang', lang === 'en' ? 'en' : 'pt-BR');
        } catch(e) {}
    </script>
    <link rel="stylesheet" href="css/password-toggle.css">
    <script src="js/theme.js" defer></script>
    <script src="js/lang.js" defer></script>
</header>
