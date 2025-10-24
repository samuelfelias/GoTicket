<?php
// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'includes/funcoes.php';
require_once 'includes/verificar_eventos_expirados.php';

// Redirecionar para a página de login se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar se o usuário é cliente
if ($_SESSION['usuario_tipo'] != 'CLIENTE') {
    header("Location: index.php");
    exit;
}

// Obter informações do usuário
$id_cliente = $_SESSION['usuario_id'];
$conexao = conectarBD();

// Atualizar status de eventos expirados
atualizarEventosExpirados($conexao);
// Deletar eventos expirados automaticamente
deletarEventosExpirados($conexao);

// Buscar eventos disponíveis
$sql = "SELECT e.*, u.nome as organizador_nome 
        FROM evento e 
        INNER JOIN usuario u ON e.id_organizador = u.id_usuario 
        WHERE e.status = 'ATIVO' 
        ORDER BY e.data ASC 
        LIMIT 6";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar ingressos do cliente
$id_cliente = $_SESSION['usuario_id'];
$sql_ingressos = "
    SELECT 
        iu.*, 
        i.tipo, 
        i.preco, 
        e.nome as nome_evento, 
        e.data, 
        e.local 
    FROM ingressousuario iu 
    JOIN ingresso i ON iu.ingresso_id = i.id_ingresso 
    JOIN evento e ON i.id_evento = e.id_evento 
    WHERE iu.usuario_id = ? 
    ORDER BY e.data ASC 
    LIMIT 6
";

$stmt_ingressos = $conexao->prepare($sql_ingressos);
$stmt_ingressos->execute([$id_cliente]);
$ingressos = $stmt_ingressos->fetchAll(PDO::FETCH_ASSOC);

// Buscar pedidos do cliente — CORRIGIDO (não usa p.id_evento, que não existe)
$sql_pedidos = "
    SELECT DISTINCT
        p.*,
        e.nome as nome_evento
    FROM pedido p
    JOIN itempedido ip ON p.id_pedido = ip.id_pedido
    JOIN ingresso i ON ip.id_ingresso = i.id_ingresso
    JOIN evento e ON i.id_evento = e.id_evento
    WHERE p.id_usuario = ?
    ORDER BY p.data_pedido DESC
    LIMIT 6
";

$stmt_pedidos = $conexao->prepare($sql_pedidos);
$stmt_pedidos->execute([$id_cliente]);
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="panel-container">
            <h2 class="panel-title">Painel do Cliente</h2>
            
            <div class="welcome-message">
                <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
            </div>
            
            <div class="panel-content">
                <?php 
                // Determinar qual aba mostrar
                $tab = isset($_GET['tab']) ? $_GET['tab'] : 'inicio';
                
                // Mostrar conteúdo baseado na aba selecionada
                if ($tab == 'pedidos') {
                    // Mostrar pedidos
                    include 'includes/cliente_pedidos.php';
                } else {
                    // Mostrar página inicial padrão
                ?>
                    <h3>Próximos Eventos</h3>
                    <div class="eventos-grid">
                        <?php if (count($eventos) > 0): ?>
                            <?php foreach ($eventos as $evento): ?>
                                <div class="evento-card">
                                    <h4><?php echo htmlspecialchars($evento['nome']); ?></h4>
                                    <p class="evento-data"><?php echo date('d/m/Y', strtotime($evento['data'])); ?></p>
                                    <p class="evento-local"><?php echo htmlspecialchars($evento['local']); ?></p>
                                    <p class="evento-organizador">Organizado por: <?php echo htmlspecialchars($evento['organizador_nome']); ?></p>
                                    <a href="eventos/detalhes_evento.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Não há eventos disponíveis no momento.</p>
                        <?php endif; ?>
                    </div>
                    <p class="ver-mais"><a href="eventos/listar_eventos.php">Ver todos os eventos</a></p>
                    
                    <h3>Meus Ingressos</h3>
                    <div class="ingressos-grid">
                        <?php if (count($ingressos) > 0): ?>
                            <?php foreach ($ingressos as $ingresso): ?>
                                <div class="ingresso-card">
                                    <h4><?php echo htmlspecialchars($ingresso['nome_evento']); ?></h4>
                                    <p class="ingresso-tipo"><?php echo htmlspecialchars($ingresso['tipo']); ?></p>
                                    <p class="ingresso-data"><?php echo date('d/m/Y', strtotime($ingresso['data'])); ?></p>
                                    <p class="ingresso-local"><?php echo htmlspecialchars($ingresso['local']); ?></p>
                                    <p class="ingresso-codigo">Código: <?php echo htmlspecialchars($ingresso['codigo']); ?></p>
                                    <a href="download_ingresso.php?id=<?php echo $ingresso['id']; ?>" class="btn btn-secondary">Download</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Você ainda não possui ingressos.</p>
                        <?php endif; ?>
                    </div>
                    <p class="ver-mais"><a href="meus_ingressos.php">Ver todos os ingressos</a></p>
                    
                    <h3>Meus Pedidos Recentes</h3>
                    <div class="pedidos-lista">
                        <?php if (count($pedidos) > 0): ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <div class="pedido-item">
                                    <div class="pedido-info">
                                        <p class="pedido-id">Pedido #<?php echo $pedido['id_pedido']; ?></p>
                                        <p class="pedido-evento"><?php echo htmlspecialchars($pedido['nome_evento']); ?></p>
                                        <p class="pedido-data"><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                                    </div>
                                    <div class="pedido-status-valor">
                                        <p class="pedido-status status-<?php echo strtolower($pedido['status']); ?>"><?php echo $pedido['status']; ?></p>
                                        <p class="pedido-valor">R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Você ainda não realizou nenhum pedido.</p>
                        <?php endif; ?>
                    </div>
                    <p class="ver-mais"><a href="painel_cliente.php?tab=pedidos">Ver todos os pedidos</a></p>
                <?php } ?>
            </div>
        </div>
    </div>

    <style>
        .eventos-grid, .ingressos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .evento-card, .ingresso-card {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-radius: 8px;
            box-shadow: 0 2px 5px var(--shadow-color);
            padding: 15px;
            transition: transform 0.3s;
        }
        
        .evento-card:hover, .ingresso-card:hover {
            transform: translateY(-5px);
        }
        
        .evento-data, .ingresso-data {
            color: #3498db;
            font-weight: bold;
        }
        
        .evento-local, .ingresso-local {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .evento-organizador, .ingresso-tipo {
            font-style: italic;
            margin-bottom: 15px;
        }
        
        .ingresso-codigo {
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 4px;
            font-family: monospace;
            margin-bottom: 15px;
        }
        
        .ver-mais {
            text-align: right;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        
        .pedidos-lista {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .pedido-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pedido-id {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .pedido-evento {
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        
        .pedido-data {
            color: #7f8c8d;
        }
        
        .pedido-status {
            font-weight: bold;
            text-align: right;
            margin-bottom: 5px;
        }
        
        .pedido-valor {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
        }
        
        .status-pendente {
            color: #f39c12;
        }
        
        .status-confirmado {
            color: #2ecc71;
        }
        
        .status-cancelado {
            color: #e74c3c;
        }
    </style>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
