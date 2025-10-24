<?php
session_start();
require_once '../config/database.php';

// Verificar se o usuário está logado e é um CLIENTE
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
    $_SESSION['mensagem'] = "Acesso não autorizado!";
    header("Location: ../login.php");
    exit();
}

// Verificar se existe um pedido confirmado na sessão
if (!isset($_SESSION['pedido_id']) || !isset($_SESSION['pagamento_aprovado']) || $_SESSION['pagamento_aprovado'] !== true) {
    $_SESSION['mensagem'] = "Nenhum pedido confirmado encontrado!";
    header("Location: listar_eventos.php");
    exit();
}

$pedido_id = $_SESSION['pedido_id'];
$conn = conectarBD();

try {
    // Iniciar transação
    $conn->beginTransaction();
    
    // Buscar informações do pedido
    $stmt = $conn->prepare("SELECT p.*, e.nome as evento_nome, e.data, e.local 
                           FROM Pedido p 
                           JOIN Evento e ON p.evento_id = e.id 
                           WHERE p.id = ? AND p.usuario_id = ? AND p.status = 'CONFIRMADO'");
    $stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        throw new Exception("Pedido não encontrado ou não confirmado!");
    }
    
    // Buscar itens do pedido
    $stmt = $conn->prepare("SELECT ip.*, i.tipo, i.id as ingresso_id 
                           FROM ItemPedido ip 
                           JOIN Ingresso i ON ip.ingresso_id = i.id 
                           WHERE ip.pedido_id = ?");
    $stmt->execute([$pedido_id]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($itens) == 0) {
        throw new Exception("Nenhum item encontrado para este pedido!");
    }
    
    // Criar diretório para armazenar os códigos se não existir
    $qrDir = '../uploads/qrcodes';
    if (!file_exists($qrDir)) {
        mkdir($qrDir, 0777, true);
    }
    
    $ingressos_gerados = [];
    
    // Para cada item do pedido, gerar a quantidade de ingressos individuais
    foreach ($itens as $item) {
        for ($i = 0; $i < $item['quantidade']; $i++) {
            // Gerar código único para o ingresso
            $codigo = uniqid('ING-') . '-' . mt_rand(1000, 9999);
            
            // Inserir na tabela IngressoUsuario
            $stmt = $conn->prepare("INSERT INTO IngressoUsuario (pedido_id, ingresso_id, codigo, status, data_geracao) 
                                   VALUES (?, ?, ?, 'VALIDO', NOW())");
            $stmt->execute([$pedido_id, $item['ingresso_id'], $codigo]);
            $ingresso_usuario_id = $conn->lastInsertId();
            
            // Dados para o código QR
            $qrData = json_encode([
                'codigo' => $codigo,
                'evento' => $pedido['evento_nome'],
                'data' => $pedido['data'],
                'local' => $pedido['local'],
                'tipo' => $item['tipo'],
                'id' => $ingresso_usuario_id
            ]);
            
            // Simular a geração do QR Code (como não temos a biblioteca)
            $qrFilename = $qrDir . '/' . $codigo . '.txt';
            file_put_contents($qrFilename, $qrData);
            
            // Adicionar à lista de ingressos gerados
            $ingressos_gerados[] = [
                'id' => $ingresso_usuario_id,
                'codigo' => $codigo,
                'tipo' => $item['tipo'],
                'qr_path' => $qrFilename
            ];
        }
    }
    
    // Salvar ingressos na sessão para exibição
    $_SESSION['ingressos_gerados'] = $ingressos_gerados;
    
    // Limpar variáveis de sessão relacionadas ao processo de compra
    unset($_SESSION['pagamento_aprovado']);
    
    // Commit da transação
    $conn->commit();
    
} catch (Exception $e) {
    // Rollback em caso de erro
    $conn->rollBack();
    $_SESSION['mensagem'] = "Erro ao gerar ingressos: " . $e->getMessage();
    header("Location: ../meus_ingressos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingressos Gerados</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .ingresso-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .qr-code {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
        }
        .ingresso-info {
            margin-top: 15px;
        }
        .download-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Seus Ingressos Foram Gerados!</h2>
                
                <?php if (isset($_SESSION['mensagem'])): ?>
                    <div class="alert alert-info">
                        <?= $_SESSION['mensagem']; ?>
                        <?php unset($_SESSION['mensagem']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="alert alert-success">
                    <h4>Compra realizada com sucesso!</h4>
                    <p>Evento: <?= $pedido['evento_nome'] ?></p>
                    <p>Data: <?= date('d/m/Y H:i', strtotime($pedido['data'])) ?></p>
                    <p>Local: <?= $pedido['local'] ?></p>
                    <p>Total: R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
                </div>
                
                <h3 class="mb-3">Ingressos:</h3>
                
                <div class="row">
                    <?php foreach ($_SESSION['ingressos_gerados'] as $ingresso): ?>
                    <div class="col-md-6">
                        <div class="ingresso-card">
                            <h4>Ingresso <?= $ingresso['tipo'] ?></h4>
                            <div class="qr-code">
                                <p><strong>Código QR simulado</strong></p>
                                <p>Código: <?= $ingresso['codigo'] ?></p>
                            </div>
                            <div class="ingresso-info">
                                <p><strong>Código:</strong> <?= $ingresso['codigo'] ?></p>
                                <p><strong>Evento:</strong> <?= $pedido['evento_nome'] ?></p>
                                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data'])) ?></p>
                                <p><strong>Local:</strong> <?= $pedido['local'] ?></p>
                            </div>
                            <div class="download-btn">
                                <a href="../download_ingresso.php?id=<?= $ingresso['id'] ?>" class="btn btn-primary btn-block">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 mb-5">
                    <a href="../meus_ingressos.php" class="btn btn-success">Ver Todos Meus Ingressos</a>
                    <a href="listar_eventos.php" class="btn btn-secondary ml-2">Voltar para Eventos</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
