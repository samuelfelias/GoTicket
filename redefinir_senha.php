<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header_simples.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Redefinir Senha</h2>
            
            <?php
            // Iniciar sessão
            session_start();
            
            // Incluir arquivo de conexão com o banco de dados
            require_once 'config/database.php';
            $conexao = conectarBD();
            
            // Verificar se o token foi fornecido
            if (!isset($_GET['token']) || empty($_GET['token'])) {
                echo '<div class="alert alert-danger">Token inválido ou não fornecido.</div>';
                echo '<div class="form-footer"><a href="login.php">Voltar para o login</a></div>';
                exit;
            }
            
            $token = $_GET['token'];
            
            // Verificar se o token é válido e não expirou
            $stmt = $conexao->prepare("SELECT r.id, r.id_usuario, r.data_expiracao, r.utilizado, u.nome 
                                     FROM RedefinicaoSenha r 
                                     JOIN Usuario u ON r.id_usuario = u.id_usuario 
                                     WHERE r.token = ? AND r.utilizado = 0");
            $stmt->execute([$token]);
            $redefinicao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$redefinicao) {
                echo '<div class="alert alert-danger">Token inválido ou já utilizado.</div>';
                echo '<div class="form-footer"><a href="esqueci_senha.php">Solicitar novo link</a></div>';
                exit;
            }
            
            // Redefinicao já foi obtida acima com fetch(PDO::FETCH_ASSOC)
            
            // Verificar se o token expirou
            $agora = new DateTime();
            $expiracao = new DateTime($redefinicao['data_expiracao']);
            
            if ($agora > $expiracao) {
                echo '<div class="alert alert-danger">O link de redefinição expirou. Por favor, solicite um novo.</div>';
                echo '<div class="form-footer"><a href="esqueci_senha.php">Solicitar novo link</a></div>';
                exit;
            }
            
            // Verificar se já existe uma mensagem de erro ou sucesso
            if (isset($_SESSION['mensagem'])) {
                $tipo = $_SESSION['mensagem_tipo'];
                echo '<div class="alert alert-' . $tipo . '">' . $_SESSION['mensagem'] . '</div>';
                // Limpar as mensagens da sessão
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
            }
            
            // Processar o formulário quando enviado
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Obter os dados do formulário
                $senha = $_POST['senha'];
                $confirmar_senha = $_POST['confirmar_senha'];
                $token_form = $_POST['token'];
                $id_redefinicao = $_POST['id_redefinicao'];
                $id_usuario = $_POST['id_usuario'];
                
                // Validar os dados
                $erros = [];
                
                // Validar senha
                if (empty($senha)) {
                    $erros[] = "Senha é obrigatória";
                } elseif (strlen($senha) < 6) {
                    $erros[] = "A senha deve ter pelo menos 6 caracteres";
                }
                
                // Validar confirmação de senha
                if ($senha !== $confirmar_senha) {
                    $erros[] = "As senhas não coincidem";
                }
                
                // Se não houver erros, prosseguir com a redefinição
                if (empty($erros)) {
                    // Hash da nova senha
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    
                    // Atualizar a senha do usuário
                    $stmt = $conexao->prepare("UPDATE Usuario SET senha = ? WHERE id_usuario = ?");
                    
                    if ($stmt->execute([$senha_hash, $id_usuario])) {
                        // Marcar o token como utilizado
                        $stmt = $conexao->prepare("UPDATE RedefinicaoSenha SET utilizado = 1 WHERE id = ?");
                        $stmt->execute([$id_redefinicao]);
                        
                        $_SESSION['mensagem'] = "Senha redefinida com sucesso! Você já pode fazer login com sua nova senha.";
                        $_SESSION['mensagem_tipo'] = "success";
                        
                        // Redirecionar para a página de login
                        header("Location: login.php");
                        exit;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao redefinir senha. Tente novamente.";
                        $_SESSION['mensagem_tipo'] = "danger";
                    }
                } else {
                    // Exibir erros de validação
                    $_SESSION['mensagem'] = implode("<br>", $erros);
                    $_SESSION['mensagem_tipo'] = "danger";
                }
                
                // Redirecionar para a mesma página para evitar reenvio do formulário
                header("Location: redefinir_senha.php?token=" . $token_form);
                exit;
            }
            ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="hidden" name="id_redefinicao" value="<?php echo $redefinicao['id']; ?>">
                <input type="hidden" name="id_usuario" value="<?php echo $redefinicao['id_usuario']; ?>">
                
                <div class="form-group">
                    <label for="senha">Nova Senha:</label>
                    <div class="input-group">
                        <input type="password" id="senha" name="senha" class="form-control" required minlength="6">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="senha">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Nova Senha:</label>
                    <div class="input-group">
                        <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required minlength="6">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirmar_senha">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-block">Redefinir Senha</button>
                
                <div class="form-footer">
                    <a href="login.php">Voltar para o login</a>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
