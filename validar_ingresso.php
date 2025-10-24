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

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Inicializar variáveis
$mensagem = "";
$mensagem_tipo = "";
$ingresso = null;

// Processar o formulário de validação
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo'])) {
    $codigo = trim($_POST['codigo']);
    
    if (empty($codigo)) {
        $mensagem = "Por favor, informe o código do ingresso.";
        $mensagem_tipo = "danger";
    } else {
        // Buscar informações do ingresso pelo código
        $stmt = $conexao->prepare("SELECT iu.id, iu.codigo, iu.status, iu.data_aquisicao, iu.id_evento,
                               i.tipo, i.preco, 
                               e.nome as evento_nome, e.data, e.horario, e.local, e.status as status_evento,
                               u.nome as nome_usuario, u.email as email_usuario
                               FROM IngressoUsuario iu
                               JOIN Ingresso i ON iu.ingresso_id = i.id_ingresso
                               JOIN Evento e ON iu.id_evento = e.id_evento
                               JOIN Usuario u ON iu.usuario_id = u.id_usuario
                               WHERE iu.codigo = ?");
        $stmt->execute([$codigo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resultado) {
            $mensagem = "Ingresso não encontrado. Verifique o código e tente novamente.";
            $mensagem_tipo = "danger";
        } else {
            $ingresso = $resultado;
            
            // Verificar status do ingresso
            if ($ingresso['status'] == 'USADO') {
                $mensagem = "Este ingresso já foi utilizado em " . date('d/m/Y H:i:s', strtotime($ingresso['data_uso'])) . ".";
                $mensagem_tipo = "warning";
            } elseif ($ingresso['status'] == 'CANCELADO') {
                $mensagem = "Este ingresso foi cancelado.";
                $mensagem_tipo = "danger";
            } else {
                $mensagem = "Ingresso válido!";
                $mensagem_tipo = "success";
            }
        }
    }
}

// Processar a marcação do ingresso como usado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marcar_usado']) && isset($_POST['ingresso_id'])) {
    $ingresso_id = $_POST['ingresso_id'];
    
    // Atualizar o status do ingresso para USADO
    $stmt = $conexao->prepare("UPDATE IngressoUsuario SET status = 'USADO', data_uso = NOW() WHERE id = ?");
    
    if ($stmt->execute([$ingresso_id])) {
        $mensagem = "Ingresso marcado como USADO com sucesso!";
        $mensagem_tipo = "success";
        
        // Recarregar as informações do ingresso
        if (isset($_POST['codigo'])) {
            $codigo = $_POST['codigo'];
            $stmt = $conexao->prepare("SELECT iu.id, iu.codigo, iu.status, iu.data_aquisicao, iu.data_uso, iu.id_evento,
                                   i.tipo, i.preco, 
                                   e.nome as evento_nome, e.data, e.horario, e.local, e.status as status_evento,
                                   u.nome as nome_usuario, u.email as email_usuario
                                   FROM IngressoUsuario iu
                                   JOIN Ingresso i ON iu.ingresso_id = i.id_ingresso
                                   JOIN Evento e ON iu.id_evento = e.id_evento
                                   JOIN Usuario u ON iu.usuario_id = u.id_usuario
                                   WHERE iu.codigo = ?");
            $stmt->execute([$codigo]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $ingresso = $resultado;
            }
        }
    } else {
        $mensagem = "Erro ao marcar o ingresso como usado: " . $conexao->errorInfo()[2];
        $mensagem_tipo = "danger";
    }
}
?>

<?php
// Título da página
$titulo = "Validar Ingressos";

// Incluir cabeçalho
include 'includes/header.php';
?>
    <style>
        .validacao-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-validacao {
            margin-bottom: 30px;
        }
        
        .resultado-validacao {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        
        .resultado-valido {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .resultado-invalido {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .resultado-atencao {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        
        .ingresso-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .ingresso-info h3 {
            margin-top: 0;
            color: #333;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-valido {
            background-color: #28a745;
            color: white;
        }
        
        .status-usado {
            background-color: #6c757d;
            color: white;
        }
        
        .status-cancelado {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-marcar-usado {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-marcar-usado:hover {
            background-color: #0069d9;
        }
        
        .btn-marcar-usado:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        .codigo-input {
            font-size: 18px;
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .btn-validar {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        
        .btn-validar:hover {
            background-color: #218838;
        }
        
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    

    <div class="container">
        <h2>Validar Ingressos</h2>
        
        <div class="validacao-container">
            <div class="form-validacao">
                <h3>Digite ou escaneie o código do ingresso</h3>
                <form action="validar_ingresso.php" method="post">
                    <input type="text" name="codigo" class="codigo-input" placeholder="Digite o código do ingresso" autofocus>
                    <button type="submit" class="btn-validar">Validar Ingresso</button>
                </form>
            </div>
            
            <?php if (!empty($mensagem)): ?>
                <div class="resultado-validacao resultado-<?php echo $mensagem_tipo == 'success' ? 'valido' : ($mensagem_tipo == 'warning' ? 'atencao' : 'invalido'); ?>">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($ingresso): ?>
                <div class="ingresso-info">
                    <h3>Informações do Ingresso</h3>
                    
                    <div class="info-row">
                        <div class="info-label">Código:</div>
                        <div><?php echo htmlspecialchars($ingresso['codigo']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div>
                            <span class="status-badge status-<?php echo strtolower($ingresso['status']) == 'ativo' ? 'valido' : (strtolower($ingresso['status']) == 'usado' ? 'usado' : 'cancelado'); ?>">
                                <?php echo $ingresso['status']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Evento:</div>
                        <div><?php echo htmlspecialchars($ingresso['evento_nome']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Data:</div>
                        <div><?php echo date('d/m/Y', strtotime($ingresso['data'])); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Horário:</div>
                        <div><?php echo $ingresso['horario']; ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Local:</div>
                        <div><?php echo htmlspecialchars($ingresso['local']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Tipo de Ingresso:</div>
                        <div><?php echo htmlspecialchars($ingresso['tipo']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Valor:</div>
                        <div>R$ <?php echo number_format($ingresso['preco'], 2, ',', '.'); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Nome do Cliente:</div>
                        <div><?php echo htmlspecialchars($ingresso['nome_usuario']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Email do Cliente:</div>
                        <div><?php echo htmlspecialchars($ingresso['email_usuario']); ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Data de Aquisição:</div>
                        <div><?php echo date('d/m/Y H:i:s', strtotime($ingresso['data_aquisicao'])); ?></div>
                    </div>
                    
                    <?php if ($ingresso['status'] == 'USADO' && isset($ingresso['data_uso'])): ?>
                    <div class="info-row">
                        <div class="info-label">Data de Uso:</div>
                        <div><?php echo date('d/m/Y H:i:s', strtotime($ingresso['data_uso'])); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($ingresso['status'] == 'ATIVO'): ?>
                    <form action="validar_ingresso.php" method="post" style="margin-top: 20px;">
                        <input type="hidden" name="ingresso_id" value="<?php echo $ingresso['id']; ?>">
                        <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($ingresso['codigo']); ?>">
                        <button type="submit" name="marcar_usado" class="btn-marcar-usado">Marcar como USADO</button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Focar no campo de código ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('input[name="codigo"]').focus();
        });
    </script>
</body>
</html>
