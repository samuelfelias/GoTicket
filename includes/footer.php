</div>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> GoTicket - Todos os direitos reservados</p>
        </div>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'js/optimize.js' : '../js/optimize.js'; ?>" defer></script>
<script src="<?php echo (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/') === strlen($_SERVER['REQUEST_URI'])-1) ? 'js/password-toggle.js' : '../js/password-toggle.js'; ?>" defer></script>
<script>
// Funcionalidade de tema claro/escuro
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const moonIcon = themeToggle?.querySelector('.fa-moon');
    const sunIcon = themeToggle?.querySelector('.fa-sun');
    let notificationTimeout;
    
    // Aplicar tema imediatamente antes de carregar o resto da página
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
    
    // Função para aplicar tema
    function applyTheme(isDarkTheme) {
        if (isDarkTheme) {
            body.classList.add('dark-theme');
            if (moonIcon && sunIcon) {
                moonIcon.style.display = 'none';
                sunIcon.style.display = 'inline-block';
            }
            if (themeToggle) {
                themeToggle.setAttribute('aria-label', 'Mudar para tema claro');
                themeToggle.setAttribute('title', 'Mudar para tema claro');
            }
        } else {
            body.classList.remove('dark-theme');
            if (moonIcon && sunIcon) {
                moonIcon.style.display = 'inline-block';
                sunIcon.style.display = 'none';
            }
            if (themeToggle) {
                themeToggle.setAttribute('aria-label', 'Mudar para tema escuro');
                themeToggle.setAttribute('title', 'Mudar para tema escuro');
            }
        }
    }
    
    // Aplicar tema baseado na preferência salva
    applyTheme(savedTheme === 'dark');
    
    // Função para mostrar notificação (otimizada)
    function showNotification(message, icon) {
        // Limpar qualquer notificação existente
        clearTimeout(notificationTimeout);
        const existingNotification = document.querySelector('.theme-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Criar notificação usando template HTML para melhor performance
        const notification = document.createElement('div');
        notification.className = 'theme-notification';
        
        // Construir conteúdo da notificação de forma otimizada
        notification.innerHTML = `
            ${icon ? `<i class="${icon}"></i>` : ''}
            <span>${message}</span>
            <button class="notification-close" aria-label="Fechar notificação">&times;</button>
        `;
        
        // Configurar evento de clique no botão fechar
        notification.querySelector('.notification-close').onclick = function() {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        };
        
        // Adicionar ao corpo do documento
        document.body.appendChild(notification);
        
        // Adicionar classe para animação CSS em vez de usar animate() API
        notification.classList.add('notification-show');
        
        // Remover após 3 segundos
        notificationTimeout = setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Alternar tema ao clicar no botão
    themeToggle?.addEventListener('click', function() {
        const isDarkTheme = !body.classList.contains('dark-theme');
        
        // Aplicar tema
        applyTheme(isDarkTheme);
        
        // Salvar preferência
        localStorage.setItem('theme', isDarkTheme ? 'dark' : 'light');
        
        // Animação do botão
        themeToggle.animate([{transform: 'rotate(0deg)'}, {transform: 'rotate(360deg)'}], {
            duration: 500,
            easing: 'ease-out'
        });
        
        // Mostrar notificação com ícone apropriado
        const icon = isDarkTheme ? 'fas fa-moon' : 'fas fa-sun';
        const message = isDarkTheme ? 'Modo escuro ativado' : 'Modo claro ativado';
        showNotification(message, icon);
    });
    
    // Adicionar acessibilidade por teclado
    themeToggle?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            themeToggle.click();
        }
    });
    
    // Detectar preferência do sistema
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Aplicar tema baseado na preferência do sistema se não houver preferência salva
    if (!localStorage.getItem('theme')) {
        applyTheme(prefersDarkScheme.matches);
        localStorage.setItem('theme', prefersDarkScheme.matches ? 'dark' : 'light');
    }
    
    // Atualizar tema quando a preferência do sistema mudar
    prefersDarkScheme.addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches);
            localStorage.setItem('theme', e.matches ? 'dark' : 'light');
        }
    });
});
</script>

<style>
/* Estilo para notificação de mudança de tema */
.theme-notification {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: var(--primary-color);
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    opacity: 1;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 200px;
    justify-content: center;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.theme-notification i {
    font-size: 16px;
}

.theme-notification i.fa-sun {
    color: #ffcc00;
    filter: drop-shadow(0 0 2px rgba(255, 204, 0, 0.5));
}

.theme-notification i.fa-moon {
    color: #e1e1e1;
}

.theme-notification span {
    flex-grow: 1;
    text-align: center;
}

.notification-close {
    background: transparent;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    opacity: 0.7;
    transition: all 0.2s ease;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.notification-close:hover {
    opacity: 1;
    transform: scale(1.1);
}

.theme-notification.fade-out {
    opacity: 0;
    transform: translate(-50%, 20px);
}

.dark-theme .theme-notification {
    background-color: #2c3e50;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}
</style>
</body>
</html>
