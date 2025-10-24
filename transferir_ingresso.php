<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar se o usuário é do tipo CLIENTE
if ($_SESSION['usuario_tipo'] != 'CLIENTE') {
    header("Location: index.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

$id_cliente = $_SESSION['usuario_id'];
$mensagem = '';
$tipo_mensagem = '';
$ingresso = null;

// Verificar se o ID do ingresso foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do ingresso não fornecido.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: meus_ingressos.php");
    exit;
}

$ingresso_id = $_GET['id'];

// Verificar se o ingresso pertence ao usuário e está disponível
    $stmt = $conexao->prepare("
        SELECT iu.id as ingresso_usuario_id, iu.codigo, iu.status as status_ingresso_usuario,
               i.id_ingresso, i.tipo, i.preco, 
               e.id_evento, e.nome as nome_evento, e.data, e.horario, e.local
        FROM ingressousuario iu
        JOIN ingresso i ON iu.ingresso_id = i.id_ingresso
        JOIN evento e ON i.id_evento = e.id_evento
        WHERE iu.id = ? AND iu.usuario_id = ? AND iu.status = 'ATIVO'
    ");
$stmt->execute([$ingresso_id, $id_cliente]);
$ingresso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ingresso) {
    $_SESSION['mensagem'] = "Ingresso não encontrado ou não está disponível para transferência.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: meus_ingressos.php");
    exit;
}

// Processar o formulário de transferência
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_destinatario = filter_input(INPUT_POST, 'email_destinatario', FILTER_SANITIZE_EMAIL);
    
    if (empty($email_destinatario)) {
        $mensagem = "Por favor, informe o e-mail do destinatário.";
        $tipo_mensagem = "danger";
    } else {
        // Verificar se o e-mail existe no sistema
        $stmt = $conexao->prepare("SELECT id_usuario, nome FROM usuario WHERE email = ?");
        $stmt->execute([$email_destinatario]);
        $usuario_destinatario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario_destinatario) {
            $mensagem = "O e-mail informado não está cadastrado no sistema.";
            $tipo_mensagem = "danger";
        } else if ($usuario_destinatario['id_usuario'] == $id_cliente) {
            $mensagem = "Você não pode transferir um ingresso para você mesmo.";
            $tipo_mensagem = "danger";
        } else {
            // Iniciar transação
            $conexao->beginTransaction();
            
            try {
                // Atualizar o proprietário do ingresso
                $stmt = $conexao->prepare("UPDATE ingressousuario SET usuario_id = ? WHERE id = ?");
                $resultado = $stmt->execute([$usuario_destinatario['id_usuario'], $ingresso_id]);
                
                if ($resultado) {
                    $conexao->commit();
                    $_SESSION['mensagem'] = "Ingresso transferido com sucesso para " . htmlspecialchars($usuario_destinatario['nome']) . ".";
                    $_SESSION['mensagem_tipo'] = "success";
                    header("Location: meus_ingressos.php");
                    exit;
                } else {
                    throw new Exception("Erro ao transferir o ingresso.");
                }
            } catch (Exception $e) {
                $conexao->rollBack();
                $mensagem = "Erro ao transferir o ingresso: " . $e->getMessage();
                $tipo_mensagem = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferir Ingresso - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .transferencia-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .ingresso-detalhe {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .form-transferencia {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .info-valor {
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>Transferir Ingresso</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="transferencia-container">
            <div class="ingresso-detalhe">
                <h3>Detalhes do Ingresso</h3>
                
                <div class="info-label">Evento:</div>
                <div class="info-valor"><?php echo htmlspecialchars($ingresso['nome_evento']); ?></div>
                
                <div class="info-label">Data e Horário:</div>
                <div class="info-valor">
                    <?php echo date('d/m/Y', strtotime($ingresso['data'])); ?> às <?php echo $ingresso['horario']; ?>
                </div>
                
                <div class="info-label">Local:</div>
                <div class="info-valor"><?php echo htmlspecialchars($ingresso['local']); ?></div>
                
                <div class="info-label">Tipo de Ingresso:</div>
                <div class="info-valor"><?php echo htmlspecialchars($ingresso['tipo']); ?></div>
                
                <div class="info-label">Código:</div>
                <div class="info-valor"><?php echo htmlspecialchars($ingresso['codigo']); ?></div>
                
                <div class="info-label">Valor:</div>
                <div class="info-valor">R$ <?php echo number_format($ingresso['preco'], 2, ',', '.'); ?></div>
            </div>
            
            <div class="form-transferencia">
                <h3>Transferir para</h3>
                <p>Informe o e-mail da pessoa para quem você deseja transferir este ingresso. O destinatário deve ter uma conta no GoTicket.</p>
                
                <form method="post" action="">
                    <div class="form-group">
                        <label for="email_destinatario">E-mail do Destinatário:</label>
                        <input type="email" id="email_destinatario" name="email_destinatario" class="form-control" required>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Transferir Ingresso</button>
                        <a href="meus_ingressos.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>