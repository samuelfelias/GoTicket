<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Login - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/password-toggle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .test-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .dark-theme .test-container {
            background: #2c3e50;
            color: white;
        }
        
        .theme-toggle {
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body class="">
    <div class="test-container">
        <div class="theme-toggle">
            <button onclick="toggleTheme()" class="btn btn-secondary">Alternar Modo Escuro</button>
        </div>
        
        <h2 class="form-title">Teste de Botão</h2>
        
        <div class="form-group">
            <label for="senha">Senha:</label>
            <div class="input-group">
                <input type="password" id="senha" name="senha" class="form-control" placeholder="Digite sua senha" required>
                <button type="button" class="btn toggle-password" data-target="senha">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>
        
        <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
            <h4>Status do Modo Escuro:</h4>
            <p id="theme-status">Modo claro ativo</p>
        </div>
    </div>

    <script src="js/password-toggle.js"></script>
    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-theme');
            const status = document.getElementById('theme-status');
            status.textContent = document.body.classList.contains('dark-theme') 
                ? 'Modo escuro ativo' 
                : 'Modo claro ativo';
        }
        
        // Inicializar o toggle de senha
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.toggle-password');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                    } else {
                        targetInput.type = 'password';
                        this.innerHTML = '<i class="fa fa-eye"></i>';
                    }
                });
            });
        });
    </script>
</body>
</html>