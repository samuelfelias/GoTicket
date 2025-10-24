<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem'] = "Você precisa estar logado para acessar a página de pagamento.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: ../login.php");
    exit;
}

// Verificar se o usuário é do tipo CLIENTE
if ($_SESSION['usuario_tipo'] != 'CLIENTE') {
    $_SESSION['mensagem'] = "Apenas clientes podem acessar a página de pagamento.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: ../index.php");
    exit;
}

// Verificar se existe um pedido na sessão
if (!isset($_SESSION['pedido']) || !isset($_SESSION['pedido']['id_pedido'])) {
    $_SESSION['mensagem'] = "Nenhum pedido em andamento.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: listar_eventos.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
$conexao = conectarBD();

$pedido = $_SESSION['pedido'];
$id_pedido = $pedido['id_pedido'];
$valor_total = $pedido['valor_total'];

// Verificar se o pedido existe e está pendente
$stmt = $conexao->prepare("SELECT status FROM pedido WHERE id_pedido = ?");
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    $_SESSION['mensagem'] = "Pedido não encontrado.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: listar_eventos.php");
    exit;
}

$status_pedido = $resultado->fetch_assoc()['status'];
if ($status_pedido != 'PENDENTE') {
    $_SESSION['mensagem'] = "Este pedido já foi processado.";
    $_SESSION['mensagem_tipo'] = "warning";
    header("Location: ../meus_ingressos.php");
    exit;
}

// Processar o formulário de pagamento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['metodo_pagamento'])) {
        $_SESSION['mensagem'] = "Selecione um método de pagamento.";
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: pagamento.php");
        exit;
    }

    $metodo_pagamento = $_POST['metodo_pagamento'];
    $metodos_validos = ['CARTAO', 'DEBITO', 'PIX', 'BOLETO'];
    
    if (!in_array($metodo_pagamento, $metodos_validos)) {
        $_SESSION['mensagem'] = "Método de pagamento inválido.";
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: pagamento.php");
        exit;
    }

    // Iniciar transação
    $conexao->begin_transaction();

    try {
        // Criar registro de pagamento com status PENDENTE
        $stmt = $conexao->prepare("
            INSERT INTO pagamento (id_pedido, metodo_pagamento, valor, status_pagamento, data_pagamento) 
            VALUES (?, ?, ?, 'PENDENTE', NOW())
        ");
        $stmt->bind_param("isd", $id_pedido, $metodo_pagamento, $valor_total);
        $stmt->execute();
        $id_pagamento = $conexao->insert_id;

        // Simular processamento de pagamento (em um sistema real, aqui seria integração com gateway)
        // Para fins de demonstração, vamos apenas atualizar o status para APROVADO após 2 segundos
        sleep(2);

        // Atualizar status do pagamento para APROVADO
        $stmt = $conexao->prepare("
            UPDATE pagamento 
            SET status_pagamento = 'APROVADO' 
            WHERE id_pagamento = ?
        ");
        $stmt->bind_param("i", $id_pagamento);
        $stmt->execute();

        // Atualizar status do pedido para CONFIRMADO
        $stmt = $conexao->prepare("
            UPDATE pedido 
            SET status = 'CONFIRMADO' 
            WHERE id_pedido = ?
        ");
        $stmt->bind_param("i", $id_pedido);
        $stmt->execute();

        // Criar registros na tabela IngressoUsuario para os ingressos vendidos
        // Primeiro, obter os ingressos do pedido
        $stmt = $conexao->prepare("
            SELECT i.id_ingresso, ip.id_pedido 
            FROM itempedido ip 
            JOIN ingresso i ON ip.id_ingresso = i.id_ingresso 
            WHERE ip.id_pedido = ?
        ");
        $stmt->bind_param("i", $id_pedido);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Para cada ingresso, criar um registro na tabela IngressoUsuario
        $stmt_insert = $conexao->prepare("
            INSERT INTO ingressousuario (ingresso_id, usuario_id, codigo, status) 
            VALUES (?, ?, ?, 'ATIVO')
        ");
        
        while ($ingresso = $resultado->fetch_assoc()) {
            $codigo = uniqid('ING-') . rand(1000, 9999);
            $stmt_insert->bind_param("iis", $ingresso['id_ingresso'], $_SESSION['usuario_id'], $codigo);
            $stmt_insert->execute();
            
            // Atualizar a quantidade disponível na tabela Ingresso
            $stmt_update = $conexao->prepare("UPDATE ingresso SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id_ingresso = ?");
            $stmt_update->bind_param("i", $ingresso['id_ingresso']);
            $stmt_update->execute();
        }

        // Confirmar transação
        $conexao->commit();

        // Limpar dados do pedido da sessão
        unset($_SESSION['pedido']);

        // Redirecionar para a página de geração de ingressos
        $_SESSION['id_pagamento'] = $id_pagamento;
        header("Location: gerar_ingressos.php?id_pedido=" . $id_pedido);
        exit;

    } catch (Exception $e) {
        // Reverter transação em caso de erro
        $conexao->rollback();
        
        $_SESSION['mensagem'] = "Erro ao processar o pagamento: " . $e->getMessage();
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: pagamento.php");
        exit;
    }
}

// Buscar informações do evento
$stmt = $conexao->prepare("
    SELECT e.nome as nome_evento, e.data, e.horario, e.local
    FROM ingresso i
    JOIN evento e ON i.id_evento = e.id_evento
    WHERE i.id_pedido = ?
    LIMIT 1
");
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$resultado = $stmt->get_result();
$evento = $resultado->fetch_assoc();

// Buscar itens do pedido
$stmt = $conexao->prepare("
    SELECT i.tipo, COUNT(*) as quantidade, i.preco
    FROM ingresso i
    WHERE i.id_pedido = ? AND i.status = 'RESERVADO'
    GROUP BY i.tipo, i.preco
");
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$resultado_itens = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - GoTicket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .pagamento-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .resumo-pedido {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .resumo-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .resumo-total {
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            text-align: right;
        }
        .metodos-pagamento {
            margin-top: 30px;
        }
        .metodo-item {
            display: block;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .metodo-item:hover {
            background-color: #f8f9fa;
        }
        .metodo-item input {
            margin-right: 10px;
        }
        .btn-finalizar {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            margin-top: 20px;
            width: 100%;
        }
        .evento-info {
            margin-bottom: 20px;
        }
        .evento-info h3 {
            margin-top: 0;
            color: #343a40;
        }
        .evento-info p {
            margin: 5px 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h2 class="page-title">Finalizar Pagamento</h2>
        
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?php echo $_SESSION['mensagem_tipo']; ?>">
                <?php 
                echo $_SESSION['mensagem']; 
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="pagamento-container">
            <div class="evento-info">
                <h3><?php echo htmlspecialchars($evento['nome_evento']); ?></h3>
                <p>Data: <?php echo date('d/m/Y', strtotime($evento['data'])); ?> às <?php echo $evento['horario']; ?></p>
                <p>Local: <?php echo htmlspecialchars($evento['local']); ?></p>
            </div>
            
            <div class="resumo-pedido">
                <h3>Resumo do Pedido</h3>
                
                <?php while ($item = $resultado_itens->fetch_assoc()): ?>
                    <div class="resumo-item">
                        <div>
                            <strong><?php echo htmlspecialchars($item['tipo']); ?></strong>
                            <div><?php echo $item['quantidade']; ?> x R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                        </div>
                        <div>
                            R$ <?php echo number_format($item['quantidade'] * $item['preco'], 2, ',', '.'); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <div class="resumo-total">
                    Total: R$ <?php echo number_format($valor_total, 2, ',', '.'); ?>
                </div>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="metodos-pagamento">
                    <h3>Escolha o método de pagamento</h3>
                    
                    <label class="metodo-item">
                        <input type="radio" name="metodo_pagamento" value="CARTAO" required>
                        Cartão de Crédito
                    </label>
                    
                    <label class="metodo-item">
                        <input type="radio" name="metodo_pagamento" value="DEBITO">
                        Cartão de Débito
                    </label>
                    
                    <label class="metodo-item">
                        <input type="radio" name="metodo_pagamento" value="PIX">
                        PIX
                    </label>
                    
                    <label class="metodo-item">
                        <input type="radio" name="metodo_pagamento" value="BOLETO">
                        Boleto Bancário
                    </label>
                </div>
                
                <button type="submit" class="btn btn-finalizar">Finalizar Pagamento</button>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
// Fechar a conexão
$conexao->close();
?>
