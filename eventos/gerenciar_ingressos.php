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

// Verificar se o ID do evento foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do evento inválido";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

$id_evento = $_GET['id'];
$id_usuario = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['usuario_tipo'];

// Buscar informações do evento
$stmt = $conexao->prepare("SELECT * FROM Evento WHERE id_evento = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    $_SESSION['mensagem'] = "Evento não encontrado";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

// Verificar se o usuário é o organizador do evento ou um administrador
if ($evento['id_organizador'] != $id_usuario && $tipo_usuario != 'ADMIN') {
    $_SESSION['mensagem'] = "Você não tem permissão para gerenciar ingressos deste evento";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

// Processar o formulário de adição de ingressos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adicionar_ingressos'])) {
    $tipo = trim($_POST['tipo']);
    $preco = str_replace(',', '.', $_POST['preco']); // Converter vírgula para ponto
    $quantidade = intval($_POST['quantidade']);
    
    // Validar os dados
    $erros = [];
    
    if (empty($tipo)) {
        $erros[] = "Tipo de ingresso é obrigatório";
    }
    
    if (!is_numeric($preco) || $preco <= 0) {
        $erros[] = "Preço deve ser um valor numérico positivo";
    }
    
    if ($quantidade <= 0) {
        $erros[] = "Quantidade deve ser maior que zero";
    }
    
    // Se não houver erros, prosseguir com a inserção
    if (empty($erros)) {
        // Inserir o ingresso no banco de dados com a quantidade disponível
        $stmt = $conexao->prepare("INSERT INTO Ingresso (id_evento, tipo, preco, quantidade_disponivel) VALUES (?, ?, ?, ?)");
        
        $sucesso = $stmt->execute([$id_evento, $tipo, $preco, $quantidade]);
        
        if ($sucesso) {
            $_SESSION['mensagem'] = "Ingressos adicionados com sucesso!";
            $_SESSION['mensagem_tipo'] = "success";
        } else {
            $_SESSION['mensagem'] = "Erro ao adicionar ingressos: " . $conexao->errorInfo()[2];
            $_SESSION['mensagem_tipo'] = "danger";
        }
        
        // Redirecionar para a mesma página para evitar reenvio do formulário
        header("Location: gerenciar_ingressos.php?id=" . $id_evento);
        exit;
    } else {
        // Exibir erros de validação
        $_SESSION['mensagem'] = implode("<br>", $erros);
        $_SESSION['mensagem_tipo'] = "danger";
    }
}

// Processar a exclusão de ingressos não vendidos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_tipo'])) {
    $tipo_excluir = $_POST['excluir_tipo'];
    
    // Excluir apenas ingressos que não foram vendidos (não estão na tabela IngressoUsuario)
    $stmt = $conexao->prepare("DELETE FROM Ingresso WHERE id_evento = ? AND tipo = ? AND id_ingresso NOT IN (SELECT ingresso_id FROM IngressoUsuario)");
    
    if ($stmt->execute([$id_evento, $tipo_excluir])) {
        $_SESSION['mensagem'] = "Ingressos disponíveis do tipo '" . $tipo_excluir . "' foram excluídos com sucesso!";
        $_SESSION['mensagem_tipo'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir ingressos: " . $conexao->errorInfo()[2];
        $_SESSION['mensagem_tipo'] = "danger";
    }
    
    // Redirecionar para a mesma página para evitar reenvio do formulário
    header("Location: gerenciar_ingressos.php?id=" . $id_evento);
    exit;
}

// Buscar os tipos de ingressos e suas quantidades
$sql = "SELECT tipo, preco, 
        SUM(quantidade_disponivel) as disponiveis,
        SUM((SELECT COUNT(*) FROM IngressoUsuario iu WHERE iu.ingresso_id = i.id_ingresso)) as vendidos,
        SUM(quantidade_disponivel) + SUM((SELECT COUNT(*) FROM IngressoUsuario iu WHERE iu.ingresso_id = i.id_ingresso)) as total
        FROM Ingresso i
        WHERE id_evento = ? 
        GROUP BY tipo, preco";

$stmt = $conexao->prepare($sql);
$stmt->execute([$id_evento]);
$ingressos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Ingressos - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .ingressos-container {
            margin-top: 20px;
        }
        .ingresso-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            color: var(--text-color);
            box-shadow: 0 2px 5px var(--shadow-color);
        }
        .ingresso-card h4 {
            margin-top: 0;
            color: var(--text-color);
        }
        .ingresso-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .stat-item {
            text-align: center;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .stat-disponivel {
            background-color: #d4edda;
            color: #155724;
        }
        .stat-vendido {
            background-color: #cce5ff;
            color: #004085;
        }
        .stat-total {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .form-inline {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .form-inline button {
            margin-left: 10px;
            background-color: #dc3545;
            color: white;
        }
        .two-columns {
            display: flex;
            gap: 20px;
        }
        .column {
            flex: 1;
        }
        @media (max-width: 768px) {
            .two-columns {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h2>Gerenciar Ingressos - <?php echo htmlspecialchars($evento['nome']); ?></h2>
        <p>
            <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data'])); ?> | 
            <strong>Horário:</strong> <?php echo $evento['horario_inicio']; ?> | 
            <strong>Local:</strong> <?php echo htmlspecialchars($evento['local']); ?> | 
            <strong>Status:</strong> <?php echo $evento['status']; ?>
        </p>
        
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
        
        <div class="two-columns">
            <div class="column">
                <div class="form-container">
                    <h3 class="form-title">Adicionar Ingressos</h3>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_evento; ?>" method="post">
                        <div class="form-group">
                            <label for="tipo">Tipo de Ingresso:</label>
                            <input type="text" id="tipo" name="tipo" class="form-control" required placeholder="Ex: VIP, Meia-entrada, Inteira">
                        </div>
                        
                        <div class="form-group">
                            <label for="preco">Preço (R$):</label>
                            <input type="text" id="preco" name="preco" class="form-control" required placeholder="Ex: 50,00">
                        </div>
                        
                        <div class="form-group">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" id="quantidade" name="quantidade" class="form-control" required min="1" value="1">
                        </div>
                        

                        
                        <div class="form-actions">
                            <button type="submit" name="adicionar_ingressos" class="btn">Adicionar Ingressos</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="column">
                <div class="ingressos-container">
                    <h3>Ingressos Disponíveis</h3>
                    
                    <?php if (empty($ingressos)): ?>
                        <p>Nenhum ingresso cadastrado para este evento.</p>
                    <?php else: ?>
                        <?php foreach ($ingressos as $ingresso): ?>
                            <div class="ingresso-card">
                                <h4><?php echo htmlspecialchars($ingresso['tipo']); ?></h4>
                                <p><strong>Preço:</strong> R$ <?php echo number_format($ingresso['preco'], 2, ',', '.'); ?></p>

                                
                                <div class="ingresso-stats">
                                    <div class="stat-item stat-disponivel">
                                        <strong>Disponíveis:</strong> <?php echo $ingresso['disponiveis']; ?>
                                    </div>
                                    <div class="stat-item stat-vendido">
                                        <strong>Vendidos:</strong> <?php echo $ingresso['vendidos']; ?>
                                    </div>
                                    <div class="stat-item stat-total">
                                        <strong>Total:</strong> <?php echo $ingresso['total']; ?>
                                    </div>
                                </div>
                                
                                <?php if ($ingresso['disponiveis'] > 0): ?>
                                    <form class="form-inline" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_evento; ?>" method="post" onsubmit="return confirm('Tem certeza que deseja excluir todos os ingressos disponíveis do tipo <?php echo htmlspecialchars($ingresso['tipo']); ?>?');">
                                        <input type="hidden" name="excluir_tipo" value="<?php echo htmlspecialchars($ingresso['tipo']); ?>">
                                        <button type="submit" class="btn">Excluir Disponíveis</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="form-actions" style="margin-top: 20px;">
            <a href="gerenciar_eventos.php" class="btn" style="background-color: #6c757d;">Voltar para Eventos</a>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
// Fechar a conexão
$conexao->close();
?>
