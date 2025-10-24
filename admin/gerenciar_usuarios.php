<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// Verificar se o usuário é do tipo ADMIN
if ($_SESSION['usuario_tipo'] != 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
$conexao = conectarBD();

// Processar ação de ativar/desativar usuário
if (isset($_GET['acao']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];
    $acao = $_GET['acao'];
    
    if ($acao == 'ativar' || $acao == 'desativar') {
        $status = ($acao == 'ativar') ? 'ATIVO' : 'INATIVO';
        
        $stmt = $conexao->prepare("UPDATE usuario SET status = ? WHERE id_usuario = ?");
        
        if ($stmt->execute([$status, $id_usuario])) {
            $_SESSION['mensagem'] = "Usuário " . ($acao == 'ativar' ? "ativado" : "desativado") . " com sucesso!";
            $_SESSION['mensagem_tipo'] = "success";
        } else {
            $_SESSION['mensagem'] = "Erro ao " . $acao . " usuário.";
            $_SESSION['mensagem_tipo'] = "danger";
        }
        
        header("Location: gerenciar_usuarios.php");
        exit;
    }
}

// Buscar usuários com filtro
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filtro_status = isset($_GET['status']) ? $_GET['status'] : '';
$filtro_busca = isset($_GET['busca']) ? $_GET['busca'] : '';

$sql = "SELECT * FROM usuario WHERE 1=1";
$params = [];
$tipos = "";

if (!empty($filtro_tipo)) {
    $sql .= " AND tipo = ?";
    $params[] = $filtro_tipo;
    $tipos .= "s";
}

if (!empty($filtro_status)) {
    $sql .= " AND status = ?";
    $params[] = $filtro_status;
    $tipos .= "s";
}

if (!empty($filtro_busca)) {
    $sql .= " AND (nome LIKE ? OR email LIKE ? OR cpf LIKE ?)";
    $busca = "%" . $filtro_busca . "%";
    $params[] = $busca;
    $params[] = $busca;
    $params[] = $busca;
    $tipos .= "sss";
}

$sql .= " ORDER BY nome ASC";

$stmt = $conexao->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .filtros {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filtros form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-end;
        }
        .filtros .form-group {
            margin-bottom: 0;
            flex: 1;
            min-width: 200px;
        }
        .filtros button {
            height: 38px;
        }
        .tabela-usuarios {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .tabela-usuarios th, .tabela-usuarios td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tabela-usuarios th {
            background-color: #f2f2f2;
        }
        .tabela-usuarios tr:hover {
            background-color: #f5f5f5;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .acoes {
            display: flex;
            gap: 5px;
        }
        .btn-sm {
            padding: 3px 8px;
            font-size: 12px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">GoTicket</div>
            <ul class="nav-menu">
                <li><a href="../painel_admin.php">Início</a></li>
                <li><a href="gerenciar_usuarios.php" class="active">Usuários</a></li>
                <li><a href="../eventos/gerenciar_eventos.php">Eventos</a></li>
                <li><a href="relatorios.php">Relatórios</a></li>
                <li><a href="../logout.php">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="container">
        <h2>Gerenciar Usuários</h2>
        
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
        
        <div class="filtros">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" class="form-control">
                        <option value="">Todos</option>
                        <option value="CLIENTE" <?php echo ($filtro_tipo == 'CLIENTE') ? 'selected' : ''; ?>>Cliente</option>
                        <option value="ORGANIZADOR" <?php echo ($filtro_tipo == 'ORGANIZADOR') ? 'selected' : ''; ?>>Organizador</option>
                        <option value="ADMIN" <?php echo ($filtro_tipo == 'ADMIN') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="ATIVO" <?php echo ($filtro_status == 'ATIVO') ? 'selected' : ''; ?>>Ativo</option>
                        <option value="INATIVO" <?php echo ($filtro_status == 'INATIVO') ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="busca">Buscar:</label>
                    <input type="text" id="busca" name="busca" class="form-control" value="<?php echo htmlspecialchars($filtro_busca); ?>" placeholder="Nome, email ou CPF">
                </div>
                
                <button type="submit" class="btn">Filtrar</button>
                <a href="gerenciar_usuarios.php" class="btn" style="background-color: #6c757d;">Limpar</a>
            </form>
        </div>
        
        <table class="tabela-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">Nenhum usuário encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                            <td>
                                <?php 
                                switch ($usuario['tipo']) {
                                    case 'CLIENTE':
                                        echo '<span class="badge badge-primary">Cliente</span>';
                                        break;
                                    case 'ORGANIZADOR':
                                        echo '<span class="badge badge-warning">Organizador</span>';
                                        break;
                                    case 'ADMIN':
                                        echo '<span class="badge badge-secondary">Administrador</span>';
                                        break;
                                    default:
                                        echo $usuario['tipo'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($usuario['status'] == 'ATIVO'): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($usuario['created_at'])); ?></td>
                            <td class="acoes">
                                <?php if ($usuario['id_usuario'] != $_SESSION['usuario_id']): ?>
                                    <?php if ($usuario['status'] == 'ATIVO'): ?>
                                        <a href="?acao=desativar&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja desativar este usuário?')">Desativar</a>
                                    <?php else: ?>
                                        <a href="?acao=ativar&id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-success">Ativar</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Usuário atual</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
