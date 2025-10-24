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

// Verificar se o usuário é do tipo CLIENTE
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

// Buscar ingressos do cliente através da tabela IngressoUsuario
$stmt = $conexao->prepare("
    SELECT iu.id as ingresso_usuario_id, iu.codigo, iu.status as status_ingresso_usuario, iu.data_aquisicao, iu.data_uso,
           i.id_ingresso, i.tipo, i.preco, 
           e.id_evento, e.nome as nome_evento, e.data, e.horario_inicio, e.horario_encerramento, e.local, e.status as status_evento
    FROM ingressousuario iu
    JOIN ingresso i ON iu.ingresso_id = i.id_ingresso
    JOIN evento e ON i.id_evento = e.id_evento
    WHERE iu.usuario_id = ?
    ORDER BY iu.data_aquisicao DESC, e.data ASC
");
$stmt->execute([$id_cliente]);
$ingressos_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar ingressos por evento
$eventos = [];
foreach ($ingressos_data as $row) {
    $id_evento = $row['id_evento'];
    
    if (!isset($eventos[$id_evento])) {
        $eventos[$id_evento] = [
            'id_evento' => $id_evento,
            'nome_evento' => $row['nome_evento'],
            'data' => $row['data'],
            'horario_inicio' => $row['horario_inicio'],
            'horario_encerramento' => $row['horario_encerramento'],
            'local' => $row['local'],
            'status_evento' => $row['status_evento'],
            'ingressos' => []
        ];
    }
    
    $eventos[$id_evento]['ingressos'][] = [
        'ingresso_usuario_id' => $row['ingresso_usuario_id'],
        'id_ingresso' => $row['id_ingresso'],
        'tipo' => $row['tipo'],
        'preco' => $row['preco'],
        'codigo' => $row['codigo'],
        'status_ingresso_usuario' => $row['status_ingresso_usuario'],
        'data_aquisicao' => $row['data_aquisicao'],
        'data_uso' => $row['data_uso']
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Ingressos - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .pedidos-container {
            margin-top: 20px;
        }
        .pedido-card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .pedido-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pedido-info {
            font-weight: bold;
        }
        .pedido-status {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-confirmado {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .pedido-body {
            padding: 15px;
        }
        .ingresso-item {
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }
        .ingresso-item:last-child {
            margin-bottom: 0;
        }
        .ingresso-evento {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .ingresso-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
        }
        .evento-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-ativo {
            background-color: #d4edda;
            color: #155724;
        }
        .status-adiado {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .sem-ingressos {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-radius: 5px;
        }
        .btn-detalhes {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body class="<?php echo isset($_SESSION['tema_escuro']) && $_SESSION['tema_escuro'] ? 'dark-theme' : ''; ?>">
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>Meus Ingressos</h2>
        
        <?php
        // Verificar se existe mensagem
        if (isset($_SESSION['mensagem'])) {
            $tipo = $_SESSION['mensagem_tipo'];
            echo '<div class="alert alert-' . $tipo . '">' . $_SESSION['mensagem'] . '</div>';
            // Limpar as mensagens da sessão
            unset($_SESSION['mensagem']);
            unset($_SESSION['mensagem_tipo']);
        }
        ?>
        
        <div class="pedidos-container">
            <?php if (empty($eventos)): ?>
                <div class="sem-ingressos">
                    <h3>Você ainda não possui ingressos</h3>
                    <p>Explore os eventos disponíveis e adquira seus ingressos!</p>
                    <a href="eventos/listar_eventos.php" class="btn">Ver Eventos</a>
                </div>
            <?php else: ?>
                <?php foreach ($eventos as $evento): ?>
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <div class="pedido-info">
                                <?php echo htmlspecialchars($evento['nome_evento']); ?>
                            </div>
                            <div>
                                <span class="pedido-status status-<?php echo strtolower($evento['status_evento']); ?>">
                                    <?php echo $evento['status_evento']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="pedido-body">
                            <div class="evento-info" style="margin-bottom: 20px;">
                                <div class="ingresso-info">
                                    <div>
                                        <div class="info-label">Data:</div>
                                        <?php echo date('d/m/Y', strtotime($evento['data'])); ?>
                                    </div>
                                    
                                    <div>
                                        <div class="info-label">Horário:</div>
                                        <?php echo $evento['horario_inicio']; ?>
                                    </div>
                                    
                                    <div>
                                        <div class="info-label">Local:</div>
                                        <?php echo htmlspecialchars($evento['local']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <h4>Seus Ingressos</h4>
                            
                            <?php foreach ($evento['ingressos'] as $ingresso): ?>
                                <div class="ingresso-item">
                                    <div class="ingresso-evento">
                                        <?php echo htmlspecialchars($ingresso['tipo']); ?>
                                    </div>
                                    
                                    <span class="evento-status status-<?php echo strtolower($ingresso['status_ingresso_usuario']); ?>">
                                        <?php echo $ingresso['status_ingresso_usuario']; ?>
                                    </span>
                                    
                                    <div class="ingresso-info">
                                        <div>
                                            <div class="info-label">Código:</div>
                                            <?php echo htmlspecialchars($ingresso['codigo']); ?>
                                        </div>
                                        
                                        <div>
                                            <div class="info-label">Valor:</div>
                                            R$ <?php echo number_format($ingresso['preco'], 2, ',', '.'); ?>
                                        </div>
                                        
                                        <?php if ($ingresso['status_ingresso_usuario'] == 'ATIVO'): ?>
                                        <div>
                                            <a href="transferir_ingresso.php?id=<?php echo $ingresso['ingresso_usuario_id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-exchange-alt"></i> Transferir
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="ingresso-info">
                                        <div>
                                            <div class="info-label">Data de Aquisição:</div>
                                            <?php echo date('d/m/Y H:i', strtotime($ingresso['data_aquisicao'])); ?>
                                        </div>
                                        
                                        <?php if ($ingresso['status_ingresso_usuario'] == 'USADO' && !empty($ingresso['data_uso'])): ?>
                                        <div>
                                            <div class="info-label">Data de Uso:</div>
                                            <?php echo date('d/m/Y H:i', strtotime($ingresso['data_uso'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="margin-top: 10px;">
                                        <a href="download_ingresso.php?id=<?php echo $ingresso['ingresso_usuario_id']; ?>" class="btn btn-detalhes">Baixar Ingresso</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div style="margin-top: 15px;">
                                <a href="eventos/detalhes_evento.php?id=<?php echo $evento['id_evento']; ?>" class="btn">Ver Detalhes do Evento</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php
// Fechar a conexão
// PDO não usa método close(), então removemos essa linha
?>
