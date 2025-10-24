<?php
// Iniciar sessão ANTES de qualquer output HTML
session_start();

// Processar o formulário quando enviado (ANTES de qualquer output HTML)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir arquivo de conexão com o banco de dados
    require_once 'config/database.php';
    
    // Obter os dados do formulário
    $nome = trim($_POST['nome']);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']); // Remove caracteres não numéricos
    $email = trim($_POST['email']);
    // Tipo de usuário é fixo no cadastro
    $tipo = 'CLIENTE';
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Função para validar CPF
    function validarCPF($cpf) {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
        
        // Verifica se foi informado todos os dígitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se foi informada uma sequência de dígitos repetidos
        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        
        // Faz o cálculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
    
    // Validar os dados
    $erros = [];
    
    // Validar nome
    if (empty($nome)) {
        $erros[] = "Nome é obrigatório";
    }
    
    // Validar CPF
    if (empty($cpf)) {
        $erros[] = "CPF é obrigatório";
    } elseif (strlen($cpf) != 11) {
        $erros[] = "CPF deve conter 11 dígitos";
    } elseif (!validarCPF($cpf)) {
        $erros[] = "CPF inválido";
    }
    
    // Validar email
    if (empty($email)) {
        $erros[] = "E-mail é obrigatório";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    }
    
    // Tipo fixo, sem seleção
    
    // Validar senha
    if (empty($senha)) {
        $erros[] = "Senha é obrigatória";
    } elseif (strlen($senha) < 6) {
        $erros[] = "Senha deve ter pelo menos 6 caracteres";
    } elseif ($senha != $confirmar_senha) {
        $erros[] = "As senhas não coincidem";
    }
    
    // Se não houver erros, prosseguir com o cadastro
    if (empty($erros)) {
        $conexao = conectarBD();
        
        // Verificar se o CPF já está cadastrado
        $stmt = $conexao->prepare("SELECT id_usuario FROM usuario WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $_SESSION['mensagem'] = "CPF já cadastrado no sistema";
            $_SESSION['mensagem_tipo'] = "danger";
        } else {
            // Verificar se o email já está cadastrado
            $stmt = $conexao->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $_SESSION['mensagem'] = "E-mail já cadastrado no sistema";
                $_SESSION['mensagem_tipo'] = "danger";
            } else {
                // Criptografar a senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Inserir o novo usuário no banco de dados com plano NORMAL
                $stmt = $conexao->prepare("INSERT INTO usuario (nome, cpf, email, tipo, senha, plano) VALUES (?, ?, ?, ?, ?, ?)");
                $plano_inicial = 'NORMAL';
                if ($stmt->execute([$nome, $cpf, $email, $tipo, $senha_hash, $plano_inicial])) {
                    $_SESSION['mensagem'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                    $_SESSION['mensagem_tipo'] = "success";
                    header("Location: login.php");
                    exit;
                } else {
                    $_SESSION['mensagem'] = "Erro ao cadastrar: " . $stmt->errorInfo()[2];
                    $_SESSION['mensagem_tipo'] = "danger";
                }
            }
        }
        
        // Não é necessário fechar a conexão no PDO
    } else {
        // Exibir erros de validação
        $_SESSION['mensagem'] = implode("<br>", $erros);
        $_SESSION['mensagem_tipo'] = "danger";
    }
    
    // Redirecionar para a mesma página para evitar reenvio do formulário
    header("Location: cadastro.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header_simples.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Cadastro de Usuário</h2>
            
            <?php
            // Verificar se já existe uma mensagem de erro ou sucesso
            if (isset($_SESSION['mensagem'])) {
                $tipo = $_SESSION['mensagem_tipo'];
                echo '<div class="alert alert-' . $tipo . '">' . $_SESSION['mensagem'] . '</div>';
                // Limpar as mensagens da sessão
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
            }
            ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" maxlength="14" placeholder="000.000.000-00" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <input type="hidden" name="tipo" value="CLIENTE">
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <div class="input-group">
                        <input type="password" id="senha" name="senha" class="form-control" minlength="6" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="senha">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <div class="input-group">
                        <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" minlength="6" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirmar_senha">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-block">Cadastrar</button>
                
                <div class="form-footer">
                    Já possui uma conta? <a href="login.php">Faça login</a>
                </div>
            </form>
        </div>
    </div>
    
    <footer>
        <div class="container footer-content">
            <div class="logo">GoTicket</div>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="eventos/listar_eventos.php">Eventos</a></li>
                <li><a href="#">Sobre</a></li>
                <li><a href="#">Contato</a></li>
            </ul>
            <div class="copyright">&copy; 2023 GoTicket - Todos os direitos reservados</div>
        </div>
    </footer>
    
    <script>
        // Função para validar CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^0-9]/g, '');
            
            if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
                return false;
            }
            
            let soma = 0;
            let resto;
            
            for (let i = 1; i <= 9; i++) {
                soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
            }
            
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(9, 10))) return false;
            
            soma = 0;
            for (let i = 1; i <= 10; i++) {
                soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
            }
            
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(10, 11))) return false;
            
            return true;
        }

        // Formatar CPF enquanto digita
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length > 9) {
                this.value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                this.value = value.replace(/^(\d{3})(\d{3})(\d{0,3})$/, '$1.$2.$3');
            } else if (value.length > 3) {
                this.value = value.replace(/^(\d{3})(\d{0,3})$/, '$1.$2');
            } else {
                this.value = value;
            }
        });

        // Validar formulário antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            let errors = [];
            
            if (!validarCPF(cpf)) {
                errors.push('CPF inválido');
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.push('E-mail inválido');
            }
            
            if (senha.length < 6) {
                errors.push('A senha deve ter pelo menos 6 caracteres');
            }
            
            if (senha !== confirmarSenha) {
                errors.push('As senhas não coincidem');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Erros encontrados:\n' + errors.join('\n'));
            }
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
