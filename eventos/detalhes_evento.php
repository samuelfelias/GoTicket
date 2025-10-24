<?php
// Iniciar sessão
session_start();

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
require_once '../includes/funcoes.php';
require_once '../includes/verificar_eventos_expirados.php';

// Verificar se o ID do evento foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do evento inválido";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: listar_eventos.php");
    exit;
}

$id_evento = $_GET['id'];
$conexao = conectarBD();

// Atualizar status de eventos expirados
atualizarEventosExpirados($conexao);
// Deletar eventos expirados automaticamente
deletarEventosExpirados($conexao);

// Buscar informações do evento
$stmt = $conexao->prepare("
    SELECT e.*, u.nome as organizador_nome 
    FROM evento e
    JOIN usuario u ON e.id_organizador = u.id_usuario
    WHERE e.id_evento = ?
");
$stmt->execute([$id_evento]);
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resultado) {
    $_SESSION['mensagem'] = "Evento não encontrado";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: listar_eventos.php");
    exit;
}

$evento = $resultado;

// Buscar tipos de ingressos disponíveis
$stmt = $conexao->prepare("
    SELECT id_ingresso, tipo, preco, quantidade_disponivel as quantidade
    FROM ingresso
    WHERE id_evento = ? AND quantidade_disponivel > 0
");
$stmt->execute([$id_evento]);
$ingressos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evento['nome']); ?> - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .evento-detalhes {
            background-color: var(--card-bg);
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            color: var(--text-color);
            box-shadow: 0 2px 5px var(--shadow-color);
        }
        .evento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .evento-titulo {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-color);
            margin: 0;
        }
        .evento-status {
            padding: 5px 10px;
            border-radius: 3px;
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
        .evento-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-item {
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .ingresso-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .ingresso-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .ingresso-tipo {
            font-size: 18px;
            font-weight: bold;
        }
        .ingresso-preco {
            font-size: 18px;
            color: #28a745;
            font-weight: bold;
        }
        .ingresso-quantidade {
            margin-bottom: 10px;
        }
        .form-compra {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
        }
        .evento-descricao {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .ingressos-container {
            margin-top: 30px;
        }
        .ingresso-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .ingresso-card h4 {
            margin-top: 0;
            color: #343a40;
        }
        .btn-comprar {
            background-color: #28a745;
            color: white;
        }
        .sem-ingressos {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
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
        
        <div class="evento-detalhes">
            <div class="evento-header">
                <h2 class="evento-titulo"><?php echo htmlspecialchars($evento['nome']); ?></h2>
                <span class="evento-status status-<?php echo strtolower($evento['status']); ?>">
                    <?php echo $evento['status']; ?>
                </span>
            </div>
            
            <div class="evento-info">
                <div class="info-item">
                    <span class="info-label">Data:</span>
                    <span class="info-value"><?php echo date('d/m/Y', strtotime($evento['data'])); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Horário de início:</span>
                    <span class="info-value"><?php echo $evento['horario_inicio']; ?></span>
                </div>
                
                <?php if (isset($evento['horario_encerramento']) && !empty($evento['horario_encerramento'])): ?>
                <div class="info-item">
                    <span class="info-label">Horário de encerramento:</span>
                    <span class="info-value"><?php echo $evento['horario_encerramento']; ?></span>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <span class="info-label">Endereço:</span>
                    <span class="info-value">
                        <?php 
                        if (isset($evento['cidade']) && isset($evento['bairro']) && isset($evento['rua']) && isset($evento['numero'])) {
                            echo htmlspecialchars($evento['rua']) . ', ' . 
                                 htmlspecialchars($evento['numero']) . ' - ' . 
                                 htmlspecialchars($evento['bairro']) . ', ' . 
                                 htmlspecialchars($evento['cidade']);
                        } else if (isset($evento['local'])) {
                            // Compatibilidade com eventos antigos
                            echo htmlspecialchars($evento['local']);
                        } else {
                            echo "Endereço não disponível";
                        }
                        ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Organizador:</span>
                    <span class="info-value"><?php echo htmlspecialchars($evento['organizador_nome']); ?></span>
                </div>
            </div>
            
            <?php if (isset($evento['imagem_url']) && !empty($evento['imagem_url'])): ?>
            <div class="evento-imagem" style="margin-bottom: 20px;">
                <h3>Imagem do Local</h3>
                <img src="../<?php echo htmlspecialchars($evento['imagem_url']); ?>" alt="Imagem do local" style="max-width: 100%; max-height: 400px; border-radius: 5px;">
            </div>
            <?php endif; ?>
            
            <div class="evento-descricao">
                <h3>Descrição</h3>
                <p><?php echo nl2br(htmlspecialchars($evento['descricao'])); ?></p>
            </div>
            
            <div class="ingressos-disponiveis">
                <h3>Ingressos Disponíveis</h3>
                <?php if (count($ingressos) > 0): ?>
                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] == 'CLIENTE'): ?>
                        <form action="comprar_ingresso.php" method="post" class="form-compra">
                            <input type="hidden" name="evento_id" value="<?php echo $id_evento; ?>">
                            
                            <?php foreach ($ingressos as $ingresso): ?>
                                <div class="ingresso-card">
                                    <div class="ingresso-header">
                                        <span class="ingresso-tipo"><?php echo htmlspecialchars($ingresso['tipo']); ?></span>
                                        <span class="ingresso-preco">R$ <?php echo number_format($ingresso['preco'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="ingresso-quantidade">Disponíveis: <?php echo $ingresso['quantidade']; ?></div>
                                    
                                    <div class="form-group">
                                        <label for="quantidade_<?php echo $ingresso['id_ingresso']; ?>">Quantidade:</label>
                                        <select name="ingressos[<?php echo $ingresso['id_ingresso']; ?>]" id="quantidade_<?php echo $ingresso['id_ingresso']; ?>" class="form-control">
                                            <option value="0">0</option>
                                            <?php for ($i = 1; $i <= min(10, $ingresso['quantidade']); $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <button type="submit" class="btn btn-primary">Comprar Ingressos</button>
                        </form>
                    <?php else: ?>
                        <?php foreach ($ingressos as $ingresso): ?>
                            <div class="ingresso-card">
                                <div class="ingresso-header">
                                    <span class="ingresso-tipo"><?php echo htmlspecialchars($ingresso['tipo']); ?></span>
                                    <span class="ingresso-preco">R$ <?php echo number_format($ingresso['preco'], 2, ',', '.'); ?></span>
                                </div>
                                <div class="ingresso-quantidade">Disponíveis: <?php echo $ingresso['quantidade']; ?></div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="alert alert-info mt-3">
                            <p>Para comprar ingressos, você precisa <a href="../login.php">fazer login</a> como cliente.</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Não há ingressos disponíveis para este evento.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="listar_eventos.php" class="btn" style="background-color: #6c757d;">Voltar para Eventos</a>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
// Fechar a conexão
$conexao->close();
?>
