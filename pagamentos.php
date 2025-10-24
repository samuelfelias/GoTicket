<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Obter dados do usuário
$id_usuario = $_SESSION['usuario_id'];
$sql_usuario = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt_usuario = $conexao->prepare($sql_usuario);
$stmt_usuario->execute([$id_usuario]);
$usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

// Processar alteração de plano (simulação)
$mensagem = "";

// Simulação de pagamento (PIX/cartão) indisponível
if (isset($_POST['pagar']) && isset($_POST['plano']) && isset($_POST['metodo'])) {
    $plano_pagamento = $_POST['plano'];
    $metodo = $_POST['metodo'];
    $nome_plano = $plano_pagamento === 'GOLD' ? 'Gold' : 'Normal';
    $nome_metodo = $metodo === 'PIX' ? 'PIX' : 'Cartão (crédito/débito)';
    $mensagem = "<div class='alert alert-warning'><i class='fas fa-info-circle'></i> Pagamento de Plano " . htmlspecialchars($nome_plano) . " via " . htmlspecialchars($nome_metodo) . " indisponível no momento.</div>";
}

// Alteração de plano (simulada, sem cobrança)
if (isset($_POST['alterar_plano']) && isset($_POST['plano'])) {
    $novo_plano = $_POST['plano'];
    if ($novo_plano === 'NORMAL' || $novo_plano === 'GOLD') {
        // Vincular tipo ao plano: NORMAL -> CLIENTE, GOLD -> ORGANIZADOR
        $novo_tipo = ($novo_plano === 'GOLD') ? 'ORGANIZADOR' : 'CLIENTE';
        $sql_atualizar = "UPDATE usuario SET plano = ?, tipo = ? WHERE id_usuario = ?";
        $stmt_atualizar = $conexao->prepare($sql_atualizar);
        if ($stmt_atualizar->execute([$novo_plano, $novo_tipo, $id_usuario])) {
            $mensagem = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Plano e permissões atualizados com sucesso! (Simulação)</div>";
            $usuario['plano'] = $novo_plano;
            $usuario['tipo'] = $novo_tipo;
            // Atualizar sessão para refletir imediatamente no menu e acessos
            $_SESSION['usuario_tipo'] = $novo_tipo;
        } else {
            $mensagem = "<div class='alert alert-danger'><i class='fas fa-times-circle'></i> Erro ao atualizar plano.</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Plano inválido.</div>";
    }
}

// Título da página
$titulo = "Pagamentos";

// Incluir cabeçalho
include 'includes/header.php';
?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Gerenciamento de Planos</h1>
    
    <?php echo $mensagem; ?>
    
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card panel-container">
                <div class="card-header bg-primary text-white" style="border-radius: 12px 12px 0 0;">
                    <h5 class="card-title mb-0">Escolha o Plano Ideal para Você</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <h4>Seu Plano Atual: 
                            <span class="badge <?php echo $usuario['plano'] == 'GOLD' ? 'bg-warning text-dark' : 'bg-secondary'; ?>" style="font-size: 1.1rem; padding: 8px 15px; border-radius: 20px;">
                                <?php echo htmlspecialchars($usuario['plano']); ?>
                            </span>
                        </h4>
                    </div>
                    
                    <div class="row mb-4">
                        <!-- Plano Normal -->
                        <div class="col-md-6">
                            <div class="card h-100" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                <div class="card-header bg-secondary text-white" style="border-radius: 15px 15px 0 0;">
                                    <h5 class="card-title mb-0 text-center">Plano Normal</h5>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-title pricing-card-title mb-4">R$ 0<small class="text-muted">/mês</small></h3>
                                    <ul class="list-unstyled mt-3 mb-4" style="text-align: left; padding-left: 20px;">
                                        <li style="margin-bottom: 10px;">✓ Acesso a todos os eventos</li>
                                        <li style="margin-bottom: 10px;">✓ Compra de ingressos</li>
                                        <li style="margin-bottom: 10px;">✓ Criação de 1 evento por semana</li>
                                        <li style="margin-bottom: 10px;">✗ Recursos avançados</li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center" style="background-color: transparent; border-top: none;">
                                    <form method="post">
                                        <input type="hidden" name="plano" value="NORMAL">
                                        <button type="submit" name="alterar_plano" class="btn btn-outline-secondary btn-lg" style="border-radius: 30px; padding: 10px 30px; width: 80%;" <?php echo $usuario['plano'] == 'NORMAL' ? 'disabled' : ''; ?>>
                                            <?php echo $usuario['plano'] == 'NORMAL' ? 'Plano Atual' : 'Selecionar Plano'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Plano Gold -->
                        <div class="col-md-6">
                            <div class="card h-100" style="border: 2px solid #ffc107; border-radius: 15px; box-shadow: 0 8px 20px rgba(255, 193, 7, 0.2); transform: translateY(-10px); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                <div class="card-header bg-warning text-dark" style="border-radius: 15px 15px 0 0;">
                                    <h5 class="card-title mb-0 text-center">Plano Gold</h5>
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-title pricing-card-title mb-4">R$ 29,90<small class="text-muted">/mês</small></h3>
                                    <ul class="list-unstyled mt-3 mb-4" style="text-align: left; padding-left: 20px;">
                                        <li style="margin-bottom: 10px;">✓ Acesso a todos os eventos</li>
                                        <li style="margin-bottom: 10px;">✓ Compra de ingressos</li>
                                        <li style="margin-bottom: 10px;">✓ Criação de 3 eventos por semana</li>
                                        <li style="margin-bottom: 10px;">✓ Destaque nos eventos</li>
                                        <li style="margin-bottom: 10px;">✓ Suporte prioritário</li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center" style="background-color: transparent; border-top: none; display: grid; gap: 10px;">
                                    <form method="post">
                                        <input type="hidden" name="plano" value="GOLD">
                                        <button type="submit" name="alterar_plano" class="btn btn-warning btn-lg" style="border-radius: 30px; padding: 10px 30px; width: 80%; font-weight: bold; box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);" <?php echo $usuario['plano'] == 'GOLD' ? 'disabled' : ''; ?>>
                                            <?php echo $usuario['plano'] == 'GOLD' ? 'Plano Atual' : 'Assinar Plano Gold'; ?>
                                        </button>
                                    </form>
                                    <div class="btn-group" style="justify-content:center;">
                                        <form method="post">
                                            <input type="hidden" name="plano" value="GOLD">
                                            <input type="hidden" name="metodo" value="PIX">
                                            <button type="submit" name="pagar" class="btn btn-info btn-sm btn-icon" <?php echo $usuario['plano'] == 'GOLD' ? 'disabled' : ''; ?>>
                                                <i class="fas fa-qrcode"></i> Pagar com PIX
                                            </button>
                                        </form>
                                        <form method="post">
                                            <input type="hidden" name="plano" value="GOLD">
                                            <input type="hidden" name="metodo" value="CARTAO">
                                            <button type="submit" name="pagar" class="btn btn-primary btn-sm btn-icon" <?php echo $usuario['plano'] == 'GOLD' ? 'disabled' : ''; ?>>
                                                <i class="fas fa-credit-card"></i> Cartão
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" style="border-radius: 10px; background-color: #e3f2fd; border-color: #90caf9;">
                        <p class="mb-0"><strong>Nota:</strong> Esta é uma página de demonstração. Em um ambiente de produção, o usuário seria redirecionado para um gateway de pagamento seguro para processar a transação.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir rodapé
include 'includes/footer.php';
?>
