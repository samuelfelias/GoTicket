<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar se o usuário é do tipo ORGANIZADOR
if ($_SESSION['usuario_tipo'] != 'ORGANIZADOR') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Organizador - GoTicket</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="panel-container">
            <h2 class="panel-title">Painel do Organizador</h2>
            
            <div class="welcome-message">
                <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
            </div>
            
            <div class="panel-content">
                <div class="dashboard-menu">
                    <div class="dashboard-item">
                        <h3>Meus Eventos</h3>
                        <p>Gerencie seus eventos e ingressos.</p>
                        <a href="eventos/gerenciar_eventos.php" class="btn">Gerenciar Eventos</a>
                    </div>
                    
                    <div class="dashboard-item">
                        <h3>Monitoramento de Vendas</h3>
                        <p>Acompanhe as vendas de ingressos e estatísticas dos seus eventos.</p>
                        <a href="organizador/monitoramento_vendas.php" class="btn">Ver Relatório de Vendas</a>
                    </div>
                    
                    <div class="dashboard-item">
                        <h3>Criar Novo Evento</h3>
                        <p>Adicione um novo evento à plataforma.</p>
                        <a href="eventos/criar_evento.php" class="btn">Criar Evento</a>
                    </div>
                </div>
                
                <style>
                    .dashboard-menu {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                        gap: 20px;
                        margin-top: 20px;
                    }
                    
                    .dashboard-item {
                        background-color: #fff;
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                        text-align: center;
                        transition: transform 0.3s ease;
                    }
                    
                    .dark-theme .dashboard-item {
                        background-color: #2c3e50;
                    }
                    
                    .dashboard-item:hover {
                        transform: translateY(-5px);
                    }
                    
                    .dashboard-item h3 {
                        margin-top: 0;
                        color: #007bff;
                    }
                    
                    .dark-theme .dashboard-item h3 {
                        color: #0d6efd;
                    }
                    
                    .dashboard-item p {
                        margin-bottom: 20px;
                        color: #6c757d;
                    }
                    
                    .dark-theme .dashboard-item p {
                        color: #adb5bd;
                    }
                    
                    .dashboard-item .btn {
                        display: inline-block;
                        width: 100%;
                    }
                </style>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
