<?php
// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Processar o formulário quando enviado (ANTES de qualquer output HTML)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir arquivo de conexão com o banco de dados
    require_once 'config/database.php';
    
    // Obter os dados do formulário
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    // Validar os dados
    $erros = [];
    
    // Validar email
    if (empty($email)) {
        $erros[] = "E-mail é obrigatório";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido";
    }
    
    // Validar senha
    if (empty($senha)) {
        $erros[] = "Senha é obrigatória";
    }
    
    // Se não houver erros, prosseguir com o login
    if (empty($erros)) {
        try {
            $conexao = conectarBD();
            
            // Verificar primeiro se o e-mail existe
            $verificaEmail = $conexao->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
            $verificaEmail->execute([$email]);
            $emailExiste = (int)$verificaEmail->fetchColumn() > 0;
            
            if (!$emailExiste) {
                // E-mail não existe no banco
                $_SESSION['mensagem'] = "E-mail não cadastrado no sistema";
                $_SESSION['mensagem_tipo'] = "danger";
                header("Location: login.php");
                exit;
            }
            
            // Buscar usuário pelo email
            $stmt = $conexao->prepare("SELECT id_usuario, nome, email, tipo, senha FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Verificar a senha
                if (password_verify($senha, $usuario['senha'])) {
                    // Senha correta, iniciar sessão
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_tipo'] = $usuario['tipo'];
                    
                    // Verificar e atualizar eventos expirados antes de redirecionar
                    require_once 'includes/verificar_eventos_expirados.php';
                    atualizarEventosExpirados($conexao);
                    deletarEventosExpirados($conexao);
                    
                    // Redirecionar para a página inicial
                    header("Location: index.php");
                    exit;
                } else {
                    // Senha incorreta
                    $_SESSION['mensagem'] = "Senha incorreta";
                    $_SESSION['mensagem_tipo'] = "danger";
                }
            } else {
                // Usuário não encontrado (não deveria chegar aqui devido à verificação anterior)
                $_SESSION['mensagem'] = "Erro ao recuperar dados do usuário";
                $_SESSION['mensagem_tipo'] = "danger";
            }
        } catch (PDOException $e) {
            // Registra o erro no log
            error_log("Erro no login: " . $e->getMessage());
            
            // Mensagem amigável para o usuário
            $_SESSION['mensagem'] = "Erro de conexão com o servidor. Por favor, tente novamente.";
            $_SESSION['mensagem_tipo'] = "danger";
        }
        
        // Não é necessário fechar o statement ou a conexão explicitamente em PDO
    } else {
        // Exibir erros de validação
        $_SESSION['mensagem'] = implode("<br>", $erros);
        $_SESSION['mensagem_tipo'] = "danger";
    }
    
    // Redirecionar para a mesma página para evitar reenvio do formulário
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header_simples.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title" data-i18n="h.login">Login</h2>
            
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
                    <label for="email" data-i18n="label.email">E-mail:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="senha" data-i18n="label.password">Senha:</label>
                    <div class="input-group">
                        <input type="password" id="senha" name="senha" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="senha">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-block" data-i18n="btn.login">Entrar</button>
                
                <div class="form-footer">
                    Não possui uma conta? <a href="cadastro.php">Cadastre-se</a><br>
                    <a href="esqueci_senha.php">Esqueci minha senha</a>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
