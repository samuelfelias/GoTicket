<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar se o usuário é do tipo ORGANIZADOR ou ADMIN
if ($_SESSION['usuario_tipo'] != 'ORGANIZADOR' && $_SESSION['usuario_tipo'] != 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
$conexao = conectarBD();

// Verificar se o ID do evento foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do evento inválido";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

$id_evento = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['usuario_tipo'];

// Buscar informações do evento
$stmt = $conexao->prepare("SELECT * FROM Evento WHERE id_evento = ?");
$stmt->execute([$id_evento]);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($resultado)) {
    $_SESSION['mensagem'] = "Evento não encontrado";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

$evento = $resultado[0];

// Verificar se o usuário é o organizador do evento ou um administrador
if ($evento['id_organizador'] != $id_usuario && $tipo_usuario != 'ADMIN') {
    $_SESSION['mensagem'] = "Você não tem permissão para excluir este evento";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

// Processar a exclusão quando confirmada
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_exclusao'])) {
    // Verificar se há ingressos vendidos para este evento
    $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM IngressoUsuario iu 
                              JOIN Ingresso i ON iu.ingresso_id = i.id_ingresso 
                              WHERE i.id_evento = ?");
    $stmt->execute([$id_evento]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $ingressos_vendidos = $resultado['total'];
    
    // Apenas administradores podem excluir eventos com ingressos vendidos
    if ($ingressos_vendidos > 0 && $tipo_usuario != 'ADMIN') {
        $_SESSION['mensagem'] = "Não é possível excluir este evento pois existem ingressos vendidos";
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: gerenciar_eventos.php");
        exit;
    }
    
    // Se for administrador e houver ingressos vendidos, excluir primeiro os registros relacionados
    if ($tipo_usuario == 'ADMIN') {
        // Excluir registros da tabela ItemPedido relacionados aos ingressos do evento
        $stmt = $conexao->prepare("DELETE FROM itempedido WHERE id_ingresso IN (SELECT id_ingresso FROM Ingresso WHERE id_evento = ?)");
        $stmt->execute([$id_evento]);
        
        // Excluir registros da tabela IngressoUsuario
        $stmt = $conexao->prepare("DELETE FROM IngressoUsuario WHERE ingresso_id IN (SELECT id_ingresso FROM Ingresso WHERE id_evento = ?)");
        $stmt->execute([$id_evento]);
        
        // Excluir registros da tabela pedido relacionados ao evento
        $stmt = $conexao->prepare("DELETE FROM pedido WHERE id_evento = ?");
        $stmt->execute([$id_evento]);
    }
    
    // Excluir os ingressos associados ao evento
    $stmt = $conexao->prepare("DELETE FROM Ingresso WHERE id_evento = ?");
    $stmt->execute([$id_evento]);
    
    // Excluir o evento
    $stmt = $conexao->prepare("DELETE FROM Evento WHERE id_evento = ?");
    $stmt->execute([$id_evento]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['mensagem'] = "Evento excluído com sucesso!";
        $_SESSION['mensagem_tipo'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir evento: " . $conexao->errorInfo()[2];
        $_SESSION['mensagem_tipo'] = "danger";
    }
    
    header("Location: gerenciar_eventos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Evento - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .confirmation-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .event-details {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .event-details p {
            margin: 5px 0;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container" style="max-width: 700px;">
            <h2 class="form-title" data-i18n="h.delete_event">Excluir Evento</h2>
            
            <div class="confirmation-box">
                <h3 data-i18n="h.warning">Atenção!</h3>
                <p data-i18n="msg.delete_warning">Você está prestes a excluir o evento <strong><?php echo htmlspecialchars($evento['nome']); ?></strong>.</p>
                <p data-i18n="msg.delete_irreversible">Esta ação não poderá ser desfeita. Todos os ingressos não vendidos associados a este evento também serão excluídos.</p>
            </div>
            
            <div class="event-details">
                <h4 data-i18n="h.event_details">Detalhes do Evento:</h4>
                <p><strong data-i18n="label.name">Nome:</strong> <?php echo htmlspecialchars($evento['nome']); ?></p>
                <p><strong data-i18n="label.date">Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data'])); ?></p>
                <p><strong data-i18n="label.time">Horário:</strong> <?php echo $evento['horario_inicio']; ?></p>
                <p><strong data-i18n="label.location">Local:</strong> <?php echo htmlspecialchars($evento['local']); ?></p>
                <p><strong data-i18n="label.status">Status:</strong> <?php echo $evento['status']; ?></p>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_evento; ?>" method="post">
                <div class="form-actions">
                    <button type="submit" name="confirmar_exclusao" value="1" class="btn btn-danger" data-i18n="btn.confirm_deletion">Confirmar Exclusão</button>
                    <a href="gerenciar_eventos.php" class="btn btn-secondary" data-i18n="btn.cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
// Fechar a conexão
$conexao->close();
?>
