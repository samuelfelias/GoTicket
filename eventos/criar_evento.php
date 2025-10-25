<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['usuario_tipo'] != 'ORGANIZADOR' && $_SESSION['usuario_tipo'] != 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexao = conectarBD();
    $id_organizador = $_SESSION['usuario_id'];

    $stmt_plano = $conexao->prepare("SELECT plano FROM usuario WHERE id_usuario = ?");
    $stmt_plano->execute([$id_organizador]);
    $plano_data = $stmt_plano->fetch(PDO::FETCH_ASSOC);
    $plano = $plano_data['plano'] ?? 'NORMAL';

    $limite_eventos = ($plano == 'GOLD') ? 3 : 1;

    $data_inicio_semana = date('Y-m-d', strtotime('-7 days'));
    $stmt_contagem = $conexao->prepare("SELECT COUNT(*) as total FROM evento WHERE id_organizador = ? AND data_criacao >= ?");
    $stmt_contagem->execute([$id_organizador, $data_inicio_semana]);
    $contagem_data = $stmt_contagem->fetch(PDO::FETCH_ASSOC);
    $eventos_semana = $contagem_data['total'];

    if ($eventos_semana >= $limite_eventos) {
        $_SESSION['mensagem'] = "Você atingiu o limite de {$limite_eventos} evento(s) por semana. Atualize para o plano GOLD para criar até 3 eventos por semana.";
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: gerenciar_eventos.php");
        exit;
    }

    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'];
    $horario_inicio = $_POST['horario'];
    $horario_encerramento = $_POST['horario_encerramento'];
    $cidade = trim($_POST['cidade']);
    $bairro = trim($_POST['bairro']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $status = $_POST['status'];
    $local = trim($rua . ", " . $numero . " - " . $bairro . ", " . $cidade);

    $erros = [];

    if (empty($nome)) $erros[] = "Nome do evento é obrigatório";
    if (empty($data)) $erros[] = "Data do evento é obrigatória";
    elseif (strtotime($data) < strtotime(date('Y-m-d'))) $erros[] = "A data do evento não pode ser no passado";
    if (empty($horario_inicio)) $erros[] = "Horário de início é obrigatório";
    if (empty($horario_encerramento)) $erros[] = "Horário de encerramento é obrigatório";
    // Validar que o horário de encerramento é posterior ao horário de início
    if (!empty($horario_inicio) && !empty($horario_encerramento) && strtotime($horario_encerramento) <= strtotime($horario_inicio)) {
        $erros[] = "O horário de encerramento deve ser posterior ao horário de início";
    }
    if (empty($cidade)) $erros[] = "Cidade é obrigatória";
    if (empty($bairro)) $erros[] = "Bairro é obrigatório";
    if (empty($rua)) $erros[] = "Rua é obrigatória";
    if (empty($numero)) $erros[] = "Número é obrigatório";

    if (empty($erros)) {
        $data_criacao = date('Y-m-d H:i:s');
        $stmt = $conexao->prepare("
            INSERT INTO evento 
            (nome, descricao, data, horario_inicio, horario_encerramento, local, cidade, bairro, rua, numero, id_organizador, status, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt->execute([$nome, $descricao, $data, $horario_inicio, $horario_encerramento, $local, $cidade, $bairro, $rua, $numero, $id_organizador, $status, $data_criacao])) {
            $_SESSION['mensagem'] = "Evento criado com sucesso!";
            $_SESSION['mensagem_tipo'] = "success";
            header("Location: gerenciar_eventos.php");
            exit;
        } else {
            $_SESSION['mensagem'] = "Erro ao criar evento: " . implode(", ", $stmt->errorInfo());
            $_SESSION['mensagem_tipo'] = "danger";
        }
    } else {
        $_SESSION['mensagem'] = implode("<br>", $erros);
        $_SESSION['mensagem_tipo'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Evento - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container" style="max-width: 700px;">
            <h2 class="form-title" data-i18n="h.create_event">Criar Novo Evento</h2>

            <?php
            if (isset($_SESSION['mensagem'])) {
                $tipo = $_SESSION['mensagem_tipo'];
                echo '<div class="alert alert-' . $tipo . '">' . $_SESSION['mensagem'] . '</div>';
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="nome" data-i18n="label.event_name">Nome do Evento:</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="descricao" data-i18n="label.description">Descrição:</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="data" data-i18n="label.date">Data:</label>
                    <input type="date" id="data" name="data" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="horario" data-i18n="label.start_time">Horário de Início:</label>
                    <input type="time" id="horario" name="horario" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="horario_encerramento" data-i18n="label.end_time">Horário de Encerramento:</label>
                    <input type="time" id="horario_encerramento" name="horario_encerramento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="cidade" data-i18n="label.city">Cidade:</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="bairro" data-i18n="label.district">Bairro:</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="rua" data-i18n="label.street">Rua:</label>
                    <input type="text" id="rua" name="rua" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="numero" data-i18n="label.number">Número:</label>
                    <input type="text" id="numero" name="numero" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="status" data-i18n="label.status">Status:</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="ATIVO">Ativo</option>
                        <option value="ADIADO">Adiado</option>
                        <option value="CANCELADO">Cancelado</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn" data-i18n="btn.create_event">Criar Evento</button>
                    <a href="gerenciar_eventos.php" class="btn" style="background-color: #95a5a6;" data-i18n="btn.cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
