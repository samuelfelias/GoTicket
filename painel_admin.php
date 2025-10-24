<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado e é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'ADMIN') {
    $_SESSION['mensagem'] = "Acesso restrito. Você precisa ser um administrador para acessar esta página.";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: login.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Buscar estatísticas gerais
// Total de usuários
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM usuario");
$stmt->execute();
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de eventos
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM evento");
$stmt->execute();
$total_eventos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de ingressos vendidos
$stmt = $conexao->prepare("SELECT COUNT(*) as total FROM ingressousuario");
$stmt->execute();
$total_ingressos_vendidos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Eventos recentes
$stmt = $conexao->prepare("
    SELECT e.*, u.nome as organizador_nome 
    FROM evento e
    JOIN usuario u ON e.id_organizador = u.id_usuario
    ORDER BY e.data_criacao DESC
    LIMIT 5
");
$stmt->execute();
$eventos_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Usuários recentes
$stmt = $conexao->prepare("
    SELECT * FROM usuario
    ORDER BY created_at DESC
    LIMIT 5
");
$stmt->execute();
$usuarios_recentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .card-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .card-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .admin-menu-item {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .admin-menu-item:hover {
            background-color: #e9ecef;
            transform: translateY(-3px);
        }
        .admin-menu-item a {
            text-decoration: none;
            color: #343a40;
            font-weight: bold;
            display: block;
        }
        .admin-menu-item i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #007bff;
        }
        .recent-items {
            margin-top: 20px;
        }
        .recent-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .recent-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            padding: 3px 8px;
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
        .row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .col-md-6 {
            width: 100%;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">GoTicket</div>
            <ul class="nav-menu">
                <li><a href="painel_admin.php" class="active">Início</a></li>
                <li><a href="admin/gerenciar_usuarios.php">Usuários</a></li>
                <li><a href="eventos/gerenciar_eventos.php">Eventos</a></li>
                <li><a href="admin/relatorios.php">Relatórios</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="container">
        <h1>Painel Administrativo</h1>
        
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
        
        <div class="welcome-message">
            <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
        </div>
        
        <div class="dashboard">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Total de Usuários</h3>
                </div>
                <div class="card-value"><?php echo $total_usuarios; ?></div>
                <a href="admin/gerenciar_usuarios.php" class="btn" style="margin-top: 10px;">Ver Todos</a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Total de Eventos</h3>
                </div>
                <div class="card-value"><?php echo $total_eventos; ?></div>
                <a href="eventos/gerenciar_eventos.php" class="btn" style="margin-top: 10px;">Ver Todos</a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ingressos Vendidos</h3>
                </div>
                <div class="card-value"><?php echo $total_ingressos_vendidos; ?></div>
                <a href="admin/relatorios.php" class="btn" style="margin-top: 10px;">Ver Relatórios</a>
            </div>
        </div>
        
        <div class="admin-menu">
            <div class="admin-menu-item">
                <a href="admin/gerenciar_usuarios.php">
                    <span>Gerenciar Usuários</span>
                </a>
            </div>
            
            <div class="admin-menu-item">
                <a href="eventos/gerenciar_eventos.php">
                    <span>Gerenciar Eventos</span>
                </a>
            </div>
            
            <div class="admin-menu-item">
                <a href="admin/relatorios.php">
                    <span>Relatórios</span>
                </a>
            </div>
            
            <div class="admin-menu-item">
                <a href="admin/configuracoes.php">
                    <span>Configurações</span>
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Eventos Recentes</h3>
                    </div>
                    <div class="recent-items">
                        <?php if (empty($eventos_recentes)): ?>
                            <p>Nenhum evento cadastrado.</p>
                        <?php else: ?>
                            <?php foreach ($eventos_recentes as $evento): ?>
                                <div class="recent-item">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($evento['nome']); ?></strong>
                                            <div>Organizador: <?php echo htmlspecialchars($evento['organizador_nome']); ?></div>
                                            <div>Data: <?php echo date('d/m/Y', strtotime($evento['data'])); ?></div>
                                        </div>
                                        <span class="status-badge status-<?php echo strtolower($evento['status']); ?>">
                                            <?php echo $evento['status']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Usuários Recentes</h3>
                    </div>
                    <div class="recent-items">
                        <?php if (empty($usuarios_recentes)): ?>
                            <p>Nenhum usuário cadastrado.</p>
                        <?php else: ?>
                            <?php foreach ($usuarios_recentes as $usuario): ?>
                                <div class="recent-item">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>
                                            <div>Email: <?php echo htmlspecialchars($usuario['email']); ?></div>
                                            <div>Tipo: <?php echo $usuario['tipo']; ?></div>
                                        </div>
                                        <span class="status-badge status-ativo">
                                            ATIVO
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
