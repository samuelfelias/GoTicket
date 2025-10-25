<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Idioma - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Aplicar tema e idioma o mais cedo possível
        try { if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark-theme'); } } catch (e) {}
        try {
            var lang = localStorage.getItem('lang') || 'pt';
            document.documentElement.setAttribute('lang', lang === 'en' ? 'en' : 'pt-BR');
        } catch (e) {}
    </script>
    <script src="js/theme.js" defer></script>
    <script src="js/lang.js" defer></script>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo"><a href="index.php" aria-label="Ir para a página inicial">GoTicket</a></div>
            <ul class="nav-menu">
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
            </ul>
        </div>
    </header>
    
    <div class="container main-content">
        <div class="panel-container">
            <h1 data-i18n="h.login">Teste de Idioma</h1>
            <p>Esta é uma página de teste para verificar se o sistema de mudança de idioma está funcionando corretamente.</p>
            
            <div class="form-group">
                <label data-i18n="label.email">E-mail:</label>
                <input type="email" class="form-control" data-i18n-placeholder="label.email">
            </div>
            
            <div class="form-group">
                <label data-i18n="label.password">Senha:</label>
                <input type="password" class="form-control" data-i18n-placeholder="label.password">
            </div>
            
            <button class="btn" data-i18n="btn.login">Entrar</button>
            <button class="btn btn-success" data-i18n="btn.signup">Cadastrar</button>
            
            <h2 data-i18n="nav.events">Eventos</h2>
            <p data-i18n="msg.welcome_prefix">Bem-vindo(a), </p>
            <p data-i18n="msg.no_events_available">Não há eventos disponíveis no momento.</p>
        </div>
    </div>
</body>
</html>
