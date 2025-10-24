<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mensagem'] = "Você precisa estar logado para comprar ingressos.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: ../login.php");
    exit;
}

// Verificar se o usuário é do tipo CLIENTE
if ($_SESSION['usuario_tipo'] != 'CLIENTE') {
    $_SESSION['mensagem'] = "Apenas clientes podem comprar ingressos.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: ../index.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
$conexao = conectarBD();

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se o evento_id foi enviado
    if (!isset($_POST['evento_id']) || !isset($_POST['ingressos'])) {
        $_SESSION['mensagem'] = "Dados incompletos para a compra.";
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: listar_eventos.php");
        exit;
    }

    $id_evento = $_POST['evento_id'];
    $ingressos = $_POST['ingressos'];
    $id_cliente = $_SESSION['usuario_id'];
    
    // Filtrar apenas os ingressos com quantidade > 0
    $ingressos_selecionados = array_filter($ingressos, function($quantidade) {
        return intval($quantidade) > 0;
    });
    
    // Verificar se algum ingresso foi selecionado
    if (empty($ingressos_selecionados)) {
        $_SESSION['mensagem'] = "Selecione pelo menos um ingresso para comprar.";
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: detalhes_evento.php?id=" . $id_evento);
        exit;
    }

    // Inicializar variáveis para o pedido
    $valor_total = 0;
    $quantidade_total = 0;
    $ingressos_para_compra = [];
    
    // Verificar disponibilidade e calcular valor total
    foreach ($ingressos_selecionados as $id_ingresso => $quantidade) {
        $quantidade = intval($quantidade);
        
        // Buscar informações do ingresso
        $stmt = $conexao->prepare("
            SELECT id_ingresso, tipo, preco, descricao, quantidade_disponivel as disponiveis
            FROM Ingresso 
            WHERE id_ingresso = ? 
            LIMIT 1
        ");
        $stmt->execute([$id_ingresso]);
        $ingresso_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$ingresso_info) {
            $_SESSION['mensagem'] = "Ingresso não encontrado.";
            $_SESSION['mensagem_tipo'] = "danger";
            header("Location: detalhes_evento.php?id=" . $id_evento);
            exit;
        }
        
        $disponiveis = $ingresso_info['disponiveis'];
        
        // Verificar se há ingressos suficientes
        if ($disponiveis < $quantidade) {
            $_SESSION['mensagem'] = "Não há ingressos suficientes disponíveis para " . $ingresso_info['tipo'] . ". Disponíveis: " . $disponiveis;
            $_SESSION['mensagem_tipo'] = "danger";
            header("Location: detalhes_evento.php?id=" . $id_evento);
            exit;
        }
        
        // Adicionar ao valor total
        $preco_unitario = $ingresso_info['preco'];
        $valor_total += $preco_unitario * $quantidade;
        $quantidade_total += $quantidade;
        
        // Armazenar informações para processamento
        $ingressos_para_compra[] = [
            'id_ingresso' => $id_ingresso,
            'tipo' => $ingresso_info['tipo'],
            'preco' => $preco_unitario,
            'quantidade' => $quantidade,
            'descricao' => $ingresso_info['descricao']
        ];
    }

    // Iniciar transação
    $conexao->beginTransaction();

    try {
        // Criar pedido com status PENDENTE
        $stmt = $conexao->prepare("
            INSERT INTO Pedido (id_usuario, id_evento, data_pedido, valor_total, status) 
            VALUES (?, ?, NOW(), ?, 'PENDENTE')
        ");
        $stmt->execute([$id_cliente, $id_evento, $valor_total]);
        $id_pedido = $conexao->lastInsertId();

        // Adicionar itens ao pedido na tabela ItemPedido
        $stmt_item = $conexao->prepare("
            INSERT INTO ItemPedido (id_pedido, id_ingresso, quantidade, preco_unitario) 
            VALUES (?, ?, ?, ?)
        ");
        
        // Preparar statement para criar registros na tabela IngressoUsuario
        $stmt_ingresso_usuario = $conexao->prepare("
            INSERT INTO IngressoUsuario (ingresso_id, usuario_id, id_evento, codigo, status, data_aquisicao) 
            VALUES (?, ?, ?, ?, 'ATIVO', NOW())
        ");
        
        // Preparar statement para atualizar quantidade disponível
        $stmt_atualizar_quantidade = $conexao->prepare("
            UPDATE Ingresso SET quantidade_disponivel = quantidade_disponivel - ? 
            WHERE id_ingresso = ?
        ");

        // Processar cada tipo de ingresso selecionado
        foreach ($ingressos_para_compra as $ingresso) {
            $id_ingresso = $ingresso['id_ingresso'];
            $quantidade = $ingresso['quantidade'];
            
            // Buscar o preço atual do ingresso
            $stmt_preco = $conexao->prepare("SELECT preco FROM Ingresso WHERE id_ingresso = ?");
            $stmt_preco->execute([$id_ingresso]);
            $preco_unitario = $stmt_preco->fetch(PDO::FETCH_ASSOC)['preco'];
            
            // Adicionar item ao pedido
            $stmt_item->execute([$id_pedido, $id_ingresso, $quantidade, $preco_unitario]);
            
            // Atualizar quantidade disponível
            $stmt_atualizar_quantidade->execute([$quantidade, $id_ingresso]);
            
            // Gerar códigos únicos e criar registros na tabela IngressoUsuario
            for ($i = 0; $i < $quantidade; $i++) {
                // Gerar código único para o ingresso (pelo menos 8 caracteres alfanuméricos)
                $codigo = 'ING-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
                
                // Criar registro na tabela IngressoUsuario
                $stmt_ingresso_usuario->execute([$id_ingresso, $id_cliente, $id_evento, $codigo]);
            }
        }

        // Confirmar transação
        $conexao->commit();

        // Salvar informações na sessão para a página de confirmação
        $_SESSION['pedido'] = [
            'id_pedido' => $id_pedido,
            'valor_total' => $valor_total,
            'quantidade_total' => $quantidade_total,
            'ingressos' => $ingressos_para_compra,
            'id_evento' => $id_evento
        ];

        // Redirecionar para a página de confirmação (sem pagamento por enquanto)
        $_SESSION['mensagem'] = "Ingressos adquiridos com sucesso!";
        $_SESSION['mensagem_tipo'] = "success";
        header("Location: ../meus_ingressos.php");
        exit;

    } catch (Exception $e) {
        // Reverter transação em caso de erro
        $conexao->rollback();
        
        $_SESSION['mensagem'] = "Erro ao processar a compra: " . $e->getMessage();
        $_SESSION['mensagem_tipo'] = "danger";
        header("Location: detalhes_evento.php?id=" . $id_evento);
        exit;
    }
} else {
    // Se não for POST, redirecionar para a página de eventos
    header("Location: listar_eventos.php");
    exit;
}

// Fechar a conexão
$conexao->close();
?>
