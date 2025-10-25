<?php
// Iniciar sessão
session_start();

// Importações do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Processar o formulário ANTES de qualquer saída HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluir dependências apenas quando necessário
    require_once 'config/database.php';
    require_once 'lib/PHPMailer-6.8.1/src/Exception.php';
    require_once 'lib/PHPMailer-6.8.1/src/PHPMailer.php';
    require_once 'lib/PHPMailer-6.8.1/src/SMTP.php';
    require_once 'config/email.php';

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $erros = [];

    if (!$email) {
        $erros[] = "Por favor, informe um email válido.";
    }

    if (empty($erros)) {
        try {
            $conexao = conectarBD();

            // Verificar se o email existe no banco de dados
            $stmt = $conexao->prepare("SELECT id_usuario, nome FROM usuario WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                $id_usuario = $usuario['id_usuario'];
                $nome_usuario = $usuario['nome'];

                // Gerar token único
                $token = bin2hex(random_bytes(32));
                $data_expiracao = date('Y-m-d H:i:s', strtotime('+24 hours'));

                // Salvar token no banco
                $stmt = $conexao->prepare("INSERT INTO RedefinicaoSenha (id_usuario, token, data_expiracao) VALUES (:id_usuario, :token, :data_expiracao)");
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->bindParam(':data_expiracao', $data_expiracao, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $link_redefinicao = "http://" . $_SERVER['HTTP_HOST'] . "/teste-pit2/redefinir_senha.php?token=" . $token;

                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = MAIL_HOST;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = MAIL_USERNAME;
                        $mail->Password   = MAIL_PASSWORD;
                        $mail->SMTPSecure = MAIL_ENCRYPTION;
                        $mail->Port       = MAIL_PORT;
                        $mail->CharSet    = 'UTF-8';

                        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
                        $mail->addAddress($email, $nome_usuario);

                        $mail->isHTML(true);
                        $mail->Subject = 'Redefinição de Senha - GoTicket';

                        $mensagem_html = "<p>Olá <strong>$nome_usuario</strong>,</p>";
                        $mensagem_html .= "<p>Recebemos uma solicitação para redefinir sua senha.</p>";
                        $mensagem_html .= "<p>Para criar uma nova senha, clique no botão abaixo:</p>";
                        $mensagem_html .= "<p><a href='$link_redefinicao' style='display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Redefinir Senha</a></p>";
                        $mensagem_html .= "<p>Ou copie e cole o link abaixo no seu navegador:</p>";
                        $mensagem_html .= "<p>$link_redefinicao</p>";
                        $mensagem_html .= "<p>Este link expira em 24 horas.</p>";
                        $mensagem_html .= "<p>Se você não solicitou a redefinição de senha, ignore este email.</p>";
                        $mensagem_html .= "<p>Atenciosamente,<br>Equipe GoTicket</p>";

                        $mail->Body    = $mensagem_html;
                        $mail->AltBody = "Olá $nome_usuario, recebemos uma solicitação para redefinir sua senha. Acesse: $link_redefinicao (válido por 24h). Se não foi você, ignore este email. Equipe GoTicket";

                        $mail->send();
                        $_SESSION['mensagem'] = "Um link para redefinição de senha foi enviado para o seu email.";
                        $_SESSION['mensagem_tipo'] = "success";
                    } catch (Exception $e) {
                        // Por segurança, não revelar falha no envio
                        error_log("Erro ao enviar email para $email: " . $mail->ErrorInfo);
                        $_SESSION['mensagem'] = "Se o email estiver cadastrado, você receberá um link para redefinição de senha.";
                        $_SESSION['mensagem_tipo'] = "info";
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro ao processar a solicitação. Tente novamente mais tarde.";
                    $_SESSION['mensagem_tipo'] = "error";
                }
            } else {
                // Não confirmar se o email existe ou não
                $_SESSION['mensagem'] = "Se o email estiver cadastrado, você receberá um link para redefinição de senha.";
                $_SESSION['mensagem_tipo'] = "info";
            }

            $conexao = null;
        } catch (Exception $e) {
            error_log("Erro no banco de dados: " . $e->getMessage());
            $_SESSION['mensagem'] = "Erro interno. Tente novamente mais tarde.";
            $_SESSION['mensagem_tipo'] = "error";
        }
    } else {
        $_SESSION['mensagem'] = implode("<br>", $erros);
        $_SESSION['mensagem_tipo'] = "error";
    }

    header("Location: esqueci_senha.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="h.forgot_password">Esqueci Minha Senha - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header_simples.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title" data-i18n="h.forgot_password">Esqueci Minha Senha</h2>

            <?php
            // Exibir mensagem da sessão, se existir
            if (isset($_SESSION['mensagem'])) {
                $tipo = $_SESSION['mensagem_tipo'] ?? 'info';
                // Normalizar classes: usar "error", "success", "info"
                // Se seu CSS usa "danger", troque "error" → "danger"
                echo '<div class="alert alert-' . htmlspecialchars($tipo) . '">' . $_SESSION['mensagem'] . '</div>';
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
            }
            ?>

            <form method="post">
                <div class="form-group">
                    <label for="email" data-i18n="label.registered_email">E-mail cadastrado:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-block" data-i18n="btn.send_reset_link">Enviar Link de Redefinição</button>
                <div class="form-footer">
                    <a href="login.php" data-i18n="btn.back_to_login">Voltar para o login</a>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>