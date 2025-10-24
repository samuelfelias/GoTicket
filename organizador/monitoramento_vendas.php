<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar se o usuário é do tipo ORGANIZADOR
if ($_SESSION['usuario_tipo'] != 'ORGANIZADOR') {
    header("Location: ../index.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';

// Obter o ID do organizador
$id_organizador = $_SESSION['usuario_id'];

// Filtros
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'todos';
$id_evento = isset($_GET['evento']) ? intval($_GET['evento']) : 0;

// Definir intervalo de datas com base no período selecionado
$data_inicio = null;
$data_fim = date('Y-m-d H:i:s'); // Data atual

switch ($periodo) {
    case 'hoje':
        $data_inicio = date('Y-m-d 00:00:00');
        break;
    case 'semana':
        $data_inicio = date('Y-m-d 00:00:00', strtotime('-7 days'));
        break;
    case 'mes':
        $data_inicio = date('Y-m-d 00:00:00', strtotime('-30 days'));
        break;
    case 'ano':
        $data_inicio = date('Y-m-d 00:00:00', strtotime('-365 days'));
        break;
    default:
        $data_inicio = null; // Todos os períodos
}

try {
    $conexao = conectarBD();
    
    // Obter eventos do organizador
    $stmt_eventos = $conexao->prepare("
        SELECT id_evento, nome, data_criacao
        FROM evento
        WHERE id_organizador = ?
        ORDER BY data_criacao DESC
    ");
    $stmt_eventos->execute([$id_organizador]);
    $eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
    
    // === 1. Vendas por evento (para totais e tabela) ===
    $sql_vendas_evento = "
        SELECT 
            e.id_evento,
            e.nome AS nome_evento,
            COUNT(DISTINCT iu.id) AS total_ingressos_vendidos,
            COALESCE(SUM(i.preco), 0) AS valor_total_vendas
        FROM 
            evento e
        LEFT JOIN 
            ingresso i ON e.id_evento = i.id_evento
        LEFT JOIN 
            ingressousuario iu ON i.id_ingresso = iu.ingresso_id AND iu.data_aquisicao IS NOT NULL
        WHERE 
            e.id_organizador = ?
    ";
    
    $params_evento = [$id_organizador];
    
    if ($data_inicio) {
        $sql_vendas_evento .= " AND iu.data_aquisicao BETWEEN ? AND ?";
        $params_evento[] = $data_inicio;
        $params_evento[] = $data_fim;
    }
    
    if ($id_evento > 0) {
        $sql_vendas_evento .= " AND e.id_evento = ?";
        $params_evento[] = $id_evento;
    }
    
    $sql_vendas_evento .= " GROUP BY e.id_evento, e.nome ORDER BY valor_total_vendas DESC";
    
    $stmt_vendas_evento = $conexao->prepare($sql_vendas_evento);
    $stmt_vendas_evento->execute($params_evento);
    $vendas_por_evento_db = $stmt_vendas_evento->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular totais e montar array por evento
    $total_ingressos = 0;
    $total_valor = 0;
    $vendas_por_evento = [];
    
    // Verificar se há resultados
    if (count($vendas_por_evento_db) > 0) {
        foreach ($vendas_por_evento_db as $venda) {
            $ingressos_vendidos = isset($venda['total_ingressos_vendidos']) ? (int)$venda['total_ingressos_vendidos'] : 0;
            $valor_vendas = isset($venda['valor_total_vendas']) ? (float)$venda['valor_total_vendas'] : 0;
            
            $total_ingressos += $ingressos_vendidos;
            $total_valor += $valor_vendas;
            $vendas_por_evento[$venda['id_evento']] = [
                'nome_evento' => $venda['nome_evento'],
                'total_ingressos' => $ingressos_vendidos,
                'total_valor' => $valor_vendas
            ];
        }
    }
    
    // === 2. Vendas por data (para gráfico de evolução) ===
    $sql_vendas_data = "
        SELECT 
            DATE(iu.data_aquisicao) AS data_venda,
            COUNT(DISTINCT iu.id) AS total_ingressos,
            COALESCE(SUM(i.preco), 0) AS total_valor
        FROM 
            evento e
        LEFT JOIN 
            ingresso i ON e.id_evento = i.id_evento
        LEFT JOIN 
            ingressousuario iu ON i.id_ingresso = iu.ingresso_id
        WHERE 
            e.id_organizador = ?
            AND iu.data_aquisicao IS NOT NULL
    ";
    
    $params_data = [$id_organizador];
    
    if ($data_inicio) {
        $sql_vendas_data .= " AND iu.data_aquisicao BETWEEN ? AND ?";
        $params_data[] = $data_inicio;
        $params_data[] = $data_fim;
    }
    
    if ($id_evento > 0) {
        $sql_vendas_data .= " AND e.id_evento = ?";
        $params_data[] = $id_evento;
    }
    
    $sql_vendas_data .= " GROUP BY DATE(iu.data_aquisicao) ORDER BY data_venda ASC";
    
    $stmt_vendas_data = $conexao->prepare($sql_vendas_data);
    $stmt_vendas_data->execute($params_data);
    $vendas_data_db = $stmt_vendas_data->fetchAll(PDO::FETCH_ASSOC);
    
    $vendas_por_data = [];
    foreach ($vendas_data_db as $row) {
        $ingressos = isset($row['total_ingressos']) ? (int)$row['total_ingressos'] : 0;
        $valor = isset($row['total_valor']) ? (float)$row['total_valor'] : 0;
        
        $vendas_por_data[$row['data_venda']] = [
            'total_ingressos' => $ingressos,
            'total_valor' => $valor
        ];
    }
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de Vendas - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .dark-theme .dashboard-card {
            background-color: #2c3e50;
            color: #fff;
        }
        
        .card-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
            color: #007bff;
        }
        
        .dark-theme .card-value {
            color: #0d6efd;
        }
        
        .card-title {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .dark-theme .card-title {
            color: #adb5bd;
        }
        
        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .dark-theme .chart-container {
            background-color: #2c3e50;
            color: #fff;
        }
        
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .filter-container select, .filter-container button {
            padding: 8px 15px;
            border-radius: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .dark-theme th, .dark-theme td {
            border-bottom: 1px solid #4b5563;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .dark-theme th {
            background-color: #374151;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .dark-theme tr:hover {
            background-color: #3e4c5e;
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="panel-container">
            <h2 class="panel-title">Monitoramento de Vendas</h2>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php else: ?>
            
            <div class="filter-container">
                <div>
                    <label for="periodo">Período:</label>
                    <select id="periodo" name="periodo" onchange="aplicarFiltros()">
                        <option value="todos" <?php echo $periodo == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="hoje" <?php echo $periodo == 'hoje' ? 'selected' : ''; ?>>Hoje</option>
                        <option value="semana" <?php echo $periodo == 'semana' ? 'selected' : ''; ?>>Últimos 7 dias</option>
                        <option value="mes" <?php echo $periodo == 'mes' ? 'selected' : ''; ?>>Últimos 30 dias</option>
                        <option value="ano" <?php echo $periodo == 'ano' ? 'selected' : ''; ?>>Último ano</option>
                    </select>
                </div>
                
                <div>
                    <label for="evento">Evento:</label>
                    <select id="evento" name="evento" onchange="aplicarFiltros()">
                        <option value="0">Todos os eventos</option>
                        <?php foreach ($eventos as $evento): ?>
                            <option value="<?php echo $evento['id_evento']; ?>" <?php echo $id_evento == $evento['id_evento'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($evento['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="dashboard-container">
                <div class="dashboard-card">
                    <p class="card-title">Total de Ingressos Vendidos</p>
                    <p class="card-value"><?php echo number_format($total_ingressos, 0, ',', '.'); ?></p>
                </div>
                
                <div class="dashboard-card">
                    <p class="card-title">Valor Total de Vendas</p>
                    <p class="card-value">R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></p>
                </div>
                
                <div class="dashboard-card">
                    <p class="card-title">Eventos Ativos</p>
                    <p class="card-value"><?php echo count($eventos); ?></p>
                </div>
            </div>
            
            <div class="chart-container">
                <h3>Evolução de Vendas</h3>
                <?php if (!empty($vendas_por_data)): ?>
                    <canvas id="vendasChart"></canvas>
                <?php else: ?>
                    <p>Nenhuma venda registrada no período selecionado.</p>
                <?php endif; ?>
            </div>
            
            <h3>Resumo de Vendas por Evento</h3>
            <?php if (!empty($vendas_por_evento)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Ingressos Vendidos</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendas_por_evento as $id_evento => $dados): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dados['nome_evento']); ?></td>
                            <td><?php echo number_format($dados['total_ingressos'], 0, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($dados['total_valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Nenhuma venda registrada no período selecionado.</p>
            <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        function aplicarFiltros() {
            const periodo = document.getElementById('periodo').value;
            const evento = document.getElementById('evento').value;
            window.location.href = `monitoramento_vendas.php?periodo=${encodeURIComponent(periodo)}&evento=${encodeURIComponent(evento)}`;
        }
        
        <?php if (!isset($erro) && !empty($vendas_por_data)): ?>
        const ctx = document.getElementById('vendasChart').getContext('2d');
        
        const labels = <?php echo json_encode(array_keys($vendas_por_data)); ?>;
        const dadosIngressos = <?php 
            echo json_encode(array_column(array_values($vendas_por_data), 'total_ingressos')); 
        ?>;
        const dadosValores = <?php 
            echo json_encode(array_column(array_values($vendas_por_data), 'total_valor')); 
        ?>;
        
        const vendasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingressos Vendidos',
                        data: dadosIngressos,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Valor de Vendas (R$)',
                        data: dadosValores,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Ingressos'
                        },
                        grid: {
                            color: document.body.classList.contains('dark-theme') ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            color: document.body.classList.contains('dark-theme') ? '#fff' : '#000'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Valor (R$)'
                        },
                        grid: {
                            drawOnChartArea: false,
                            color: document.body.classList.contains('dark-theme') ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            color: document.body.classList.contains('dark-theme') ? '#fff' : '#000'
                        }
                    },
                    x: {
                        grid: {
                            color: document.body.classList.contains('dark-theme') ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            color: document.body.classList.contains('dark-theme') ? '#fff' : '#000'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: document.body.classList.contains('dark-theme') ? '#fff' : '#000'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.datasetIndex === 1) {
                                    return label + 'R$ ' + parseFloat(context.parsed.y).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                } else {
                                    return label + parseInt(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>