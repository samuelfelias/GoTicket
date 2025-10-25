<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado e é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'ADMIN') {
    $_SESSION['mensagem'] = "Acesso restrito. Você precisa ser um administrador para acessar esta página.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: ../login.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
$conexao = conectarBD();

// Buscar estatísticas gerais
$stats = [];

// Total de usuários
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM usuario");
$stmt->execute();
$stats['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de eventos
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM evento");
$stmt->execute();
$stats['total_eventos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de ingressos vendidos
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM ingressousuario");
$stmt->execute();
$stats['total_ingressos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Receita total
$stmt = $conexao->prepare("SELECT SUM(valor_total) as total FROM pedido WHERE status = 'CONFIRMADO'");
$stmt->execute();
$stats['receita_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Eventos por status
$stmt = $conexao->prepare("SELECT status, COUNT(*) as total FROM evento GROUP BY status");
$stmt->execute();
$stats['eventos_por_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Usuários por tipo
$stmt = $conexao->prepare("SELECT tipo, COUNT(*) as total FROM usuario GROUP BY tipo");
$stmt->execute();
$stats['usuarios_por_tipo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eventos recentes
$stmt = $conexao->prepare("
    SELECT e.*, u.nome as organizador_nome 
    FROM evento e
    JOIN usuario u ON e.id_organizador = u.id_usuario
    ORDER BY e.data_criacao DESC
    LIMIT 10
");
$stmt->execute();
$stats['eventos_recentes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .card-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .card-value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .card-subtitle {
            font-size: 12px;
            color: #999;
        }
        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .chart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .chart-item:last-child {
            border-bottom: none;
        }
        .chart-label {
            font-weight: 500;
        }
        .chart-value {
            font-weight: bold;
            color: #007bff;
        }
        .recent-events {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .event-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .event-item:last-child {
            border-bottom: none;
        }
        .event-name {
            font-weight: 500;
        }
        .event-date {
            color: #666;
            font-size: 14px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-ativo {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-adiado {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1>Relatórios e Estatísticas</h1>
        
        <div class="dashboard">
            <div class="card">
                <div class="card-title">Total de Usuários</div>
                <div class="card-value"><?php echo number_format($stats['total_usuarios']); ?></div>
                <div class="card-subtitle">Cadastrados no sistema</div>
            </div>
            
            <div class="card">
                <div class="card-title">Total de Eventos</div>
                <div class="card-value"><?php echo number_format($stats['total_eventos']); ?></div>
                <div class="card-subtitle">Eventos criados</div>
            </div>
            
            <div class="card">
                <div class="card-title">Ingressos Vendidos</div>
                <div class="card-value"><?php echo number_format($stats['total_ingressos']); ?></div>
                <div class="card-subtitle">Total de vendas</div>
            </div>
            
            <div class="card">
                <div class="card-title">Receita Total</div>
                <div class="card-value">R$ <?php echo number_format($stats['receita_total'], 2, ',', '.'); ?></div>
                <div class="card-subtitle">Valor arrecadado</div>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-title">Eventos por Status</div>
            <?php foreach ($stats['eventos_por_status'] as $item): ?>
                <div class="chart-item">
                    <span class="chart-label"><?php echo $item['status']; ?></span>
                    <span class="chart-value"><?php echo $item['total']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="chart-container">
            <div class="chart-title">Usuários por Tipo</div>
            <?php foreach ($stats['usuarios_por_tipo'] as $item): ?>
                <div class="chart-item">
                    <span class="chart-label"><?php echo $item['tipo']; ?></span>
                    <span class="chart-value"><?php echo $item['total']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="recent-events">
            <div class="chart-title">Eventos Recentes</div>
            <?php if (empty($stats['eventos_recentes'])): ?>
                <p>Nenhum evento encontrado.</p>
            <?php else: ?>
                <?php foreach ($stats['eventos_recentes'] as $evento): ?>
                    <div class="event-item">
                        <div>
                            <div class="event-name"><?php echo htmlspecialchars($evento['nome']); ?></div>
                            <div class="event-date"><?php echo date('d/m/Y', strtotime($evento['data'])); ?> - <?php echo htmlspecialchars($evento['organizador_nome']); ?></div>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($evento['status']); ?>">
                            <?php echo $evento['status']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
