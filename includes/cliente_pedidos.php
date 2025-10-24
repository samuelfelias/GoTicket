<?php
// Verificar se o usuário está logado e é do tipo CLIENTE
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
    header("Location: index.php");
    exit;
}

// Buscar todos os pedidos do cliente
$id_cliente = $_SESSION['usuario_id'];
$sql_pedidos = "SELECT DISTINCT p.*, e.nome as nome_evento, e.data as data_evento, e.local 
                FROM pedido p 
                JOIN itempedido ip ON p.id_pedido = ip.id_pedido
                JOIN ingresso i ON ip.id_ingresso = i.id_ingresso
                JOIN evento e ON i.id_evento = e.id_evento 
                WHERE p.id_usuario = ? 
                ORDER BY p.data_pedido DESC";
$stmt_pedidos = $conexao->prepare($sql_pedidos);
$stmt_pedidos->execute([$id_cliente]);
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

// Buscar detalhes dos ingressos para cada pedido
$pedidos_detalhados = [];
foreach ($pedidos as $pedido) {
    $id_pedido = $pedido['id_pedido'];
    
    // Buscar itens do pedido
    $sql_itens = "SELECT ip.*, i.tipo, i.preco 
                  FROM itempedido ip 
                  JOIN ingresso i ON ip.id_ingresso = i.id_ingresso 
                  WHERE ip.id_pedido = ?";
    $stmt_itens = $conexao->prepare($sql_itens);
    $stmt_itens->execute([$id_pedido]);
    $itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
    
    // Adicionar itens ao pedido
    $pedido['itens'] = $itens;
    $pedidos_detalhados[] = $pedido;
}
?>

<h2 class="panel-title">Meus Pedidos</h2>

<div class="pedidos-container">
    <?php if (count($pedidos_detalhados) > 0): ?>
        <?php foreach ($pedidos_detalhados as $pedido): ?>
            <div class="pedido-card">
                <div class="pedido-header">
                    <div class="pedido-info">
                        <span>Pedido #<?php echo $pedido['id_pedido']; ?></span> - 
                        <span><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></span>
                    </div>
                    <div class="pedido-status status-<?php echo strtolower($pedido['status']); ?>">
                        <?php echo $pedido['status']; ?>
                    </div>
                </div>
                
                <div class="pedido-body">
                    <div class="evento-info">
                        <h4><?php echo htmlspecialchars($pedido['nome_evento']); ?></h4>
                        <p class="evento-data"><?php echo date('d/m/Y', strtotime($pedido['data_evento'])); ?></p>
                        <p class="evento-local"><?php echo htmlspecialchars($pedido['local']); ?></p>
                    </div>
                    
                    <div class="pedido-itens">
                        <h4>Ingressos</h4>
                        <table class="itens-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Valor Unitário</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedido['itens'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['tipo']); ?></td>
                                        <td><?php echo $item['quantidade']; ?></td>
                                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                        <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="total-label">Total</td>
                                    <td class="total-valor">R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <?php if ($pedido['status'] == 'CONFIRMADO'): ?>
                    <div class="pedido-footer">
                        <a href="meus_ingressos.php?pedido_id=<?php echo $pedido['id_pedido']; ?>" class="btn btn-primary">Ver Ingressos</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="mensagem-vazio">
            <p>Você ainda não realizou nenhum pedido.</p>
            <a href="eventos/listar_eventos.php" class="btn btn-primary">Ver Eventos Disponíveis</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .pedidos-container {
        margin-top: 20px;
    }
    
    .pedido-card {
        background-color: #fff;
        border-radius: 8px;
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
    
    .pedido-body {
        padding: 20px;
    }
    
    .pedido-footer {
        padding: 15px;
        border-top: 1px solid #e9ecef;
        text-align: right;
    }
    
    .evento-info {
        margin-bottom: 20px;
    }
    
    .evento-data {
        color: #3498db;
        font-weight: bold;
    }
    
    .evento-local {
        color: #7f8c8d;
    }
    
    .pedido-itens h4 {
        margin-bottom: 15px;
    }
    
    .itens-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .itens-table th, .itens-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }
    
    .itens-table th {
        background-color: #f8f9fa;
    }
    
    .total-label {
        text-align: right;
        font-weight: bold;
    }
    
    .total-valor {
        font-weight: bold;
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
    
    .mensagem-vazio {
        text-align: center;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .mensagem-vazio p {
        margin-bottom: 20px;
        font-size: 1.1em;
    }
</style>
