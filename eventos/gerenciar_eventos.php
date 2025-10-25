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

// Obter os eventos do organizador logado
$id_usuario = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['usuario_tipo'];

// Se for ADMIN, pode ver todos os eventos, caso contrário, apenas os próprios eventos
if ($tipo_usuario == 'ADMIN') {
    $sql = "SELECT e.*, e.horario_encerramento, u.nome as organizador_nome 
            FROM evento e 
            INNER JOIN usuario u ON e.id_organizador = u.id_usuario 
            ORDER BY e.data DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
} else {
    $sql = "SELECT e.*, e.horario_encerramento, u.nome as organizador_nome 
            FROM evento e 
            INNER JOIN usuario u ON e.id_organizador = u.id_usuario 
            WHERE e.id_organizador = ? 
            ORDER BY e.data DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$id_usuario]);
}

$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eventos - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .eventos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .eventos-table th, .eventos-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .eventos-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .eventos-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .status-ativo {
            color: #2ecc71;
            font-weight: bold;
        }
        
        .status-cancelado {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .status-adiado {
            color: #f39c12;
            font-weight: bold;
        }
        
        .btn-group {
            display: flex;
            gap: 5px;
        }
        
        .btn-editar {
            background-color: #3498db;
        }
        
        .btn-excluir {
            background-color: #e74c3c;
        }
        
        .btn-ingressos {
            background-color: #2ecc71;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 14px;
        }
        
        .no-events {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .alert {
            margin-top: 20px;
        }
        
        /* Estilos para tema escuro */
        .dark-theme .eventos-table th {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--text-color) !important;
            border-bottom-color: var(--border-color) !important;
        }
        
        .dark-theme .eventos-table td {
            color: var(--text-color) !important;
            border-bottom-color: var(--border-color) !important;
        }
        
        .dark-theme .eventos-table tr:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        
        .dark-theme .eventos-table {
            background-color: var(--card-bg) !important;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="panel-container">
            <h2 class="panel-title" data-i18n="h.manage_events">Gerenciar Eventos</h2>
            
            <?php
            // Verificar se existe mensagem de sucesso ou erro
            if (isset($_SESSION['mensagem'])) {
                $tipo = $_SESSION['mensagem_tipo'];
                echo '<div class="alert alert-' . $tipo . '">' . $_SESSION['mensagem'] . '</div>';
                // Limpar as mensagens da sessão
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
            }
            ?>
            
            <div class="panel-actions">
                <a href="criar_evento.php" class="btn" data-i18n="h.create_event">Criar Novo Evento</a>
            </div>
            
            <?php if (count($eventos) > 0): ?>
                <table class="eventos-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Horário Início</th>
                            <th>Horário Fim</th>
                            <th>Local</th>
                            <th>Status</th>
                            <?php if ($tipo_usuario == 'ADMIN'): ?>
                            <th>Organizador</th>
                            <?php endif; ?>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eventos as $evento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($evento['nome']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($evento['data'])); ?></td>
                                <td><?php 
                                    // Verificar se horario_inicio existe e não é nulo
                                    if (isset($evento['horario_inicio']) && $evento['horario_inicio'] !== null) {
                                        echo date('H:i', strtotime($evento['horario_inicio']));
                                    } else {
                                        echo '00:00';
                                    }
                                ?></td>
                                <td>
                                    <?php 
                                    // Verificar se o valor existe e não é nulo antes de usar strtotime
                                    if (isset($evento['horario_encerramento']) && $evento['horario_encerramento'] !== null) {
                                        echo date('H:i', strtotime($evento['horario_encerramento']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($evento['local']); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    switch ($evento['status']) {
                                        case 'ATIVO':
                                            $status_class = 'status-ativo';
                                            break;
                                        case 'CANCELADO':
                                            $status_class = 'status-cancelado';
                                            break;
                                        case 'ADIADO':
                                            $status_class = 'status-adiado';
                                            break;
                                    }
                                    echo '<span class="' . $status_class . '">' . $evento['status'] . '</span>';
                                    ?>
                                </td>
                                <?php if ($tipo_usuario == 'ADMIN'): ?>
                                <td><?php echo htmlspecialchars($evento['organizador_nome']); ?></td>
                                <?php endif; ?>
                                <td>
                                    <div class="btn-group">
                                        <a href="editar_evento.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-small btn-editar" data-i18n="btn.edit">Editar</a>
                                        <a href="gerenciar_ingressos.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-small btn-ingressos" data-i18n="btn.tickets">Ingressos</a>
                                        <a href="excluir_evento.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-small btn-excluir" data-i18n="btn.delete" onclick="return confirm('Tem certeza que deseja excluir este evento?');">Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-events">
                    <p>Você ainda não possui eventos cadastrados.</p>
                    <a href="criar_evento.php" class="btn">Criar Primeiro Evento</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
