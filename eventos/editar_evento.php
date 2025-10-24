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
$stmt->bind_param("i", $id_evento);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    $_SESSION['mensagem'] = "Evento não encontrado";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

$evento = $resultado->fetch_assoc();

// Verificar se o evento ainda usa o campo 'local' antigo
$cidade = isset($evento['cidade']) ? $evento['cidade'] : '';
$bairro = isset($evento['bairro']) ? $evento['bairro'] : '';
$rua = isset($evento['rua']) ? $evento['rua'] : '';
$numero = isset($evento['numero']) ? $evento['numero'] : '';
$imagem_url = isset($evento['imagem_url']) ? $evento['imagem_url'] : '';

// Verificar se o usuário é o organizador do evento ou um administrador
if ($evento['id_organizador'] != $id_usuario && $tipo_usuario != 'ADMIN') {
    $_SESSION['mensagem'] = "Você não tem permissão para editar este evento";
    $_SESSION['mensagem_tipo'] = "danger";
    header("Location: gerenciar_eventos.php");
    exit;
}

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter os dados do formulário
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $cidade = trim($_POST['cidade']);
    $bairro = trim($_POST['bairro']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $status = $_POST['status'];
    
    // Processar o upload da imagem
    $imagem_url = null;
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_tamanho = $_FILES['imagem']['size'];
        $imagem_tipo = $_FILES['imagem']['type'];
        
        // Verificar o tipo de arquivo
        $permitidos = array('image/jpeg', 'image/jpg', 'image/png');
        if(!in_array($imagem_tipo, $permitidos)) {
            $erros[] = "Tipo de arquivo não permitido. Apenas JPG e PNG são aceitos.";
        }
        
        // Verificar o tamanho do arquivo (2MB = 2097152 bytes)
        if($imagem_tamanho > 2097152) {
            $erros[] = "O arquivo é muito grande. Tamanho máximo permitido: 2MB.";
        }
        
        // Se não houver erros, fazer o upload
        if(empty($erros)) {
            $diretorio = "../uploads/eventos/";
            
            // Criar o diretório se não existir
            if(!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            // Gerar um nome único para o arquivo
            $extensao = pathinfo($imagem_nome, PATHINFO_EXTENSION);
            $novo_nome = uniqid() . '.' . $extensao;
            $destino = $diretorio . $novo_nome;
            
            if(move_uploaded_file($imagem_tmp, $destino)) {
                $imagem_url = "uploads/eventos/" . $novo_nome;
            } else {
                $erros[] = "Erro ao fazer upload da imagem.";
            }
        }
    }
    
    // Validar os dados
    $erros = [];
    
    if (empty($nome)) {
        $erros[] = "Nome do evento é obrigatório";
    }
    
    if (empty($data)) {
        $erros[] = "Data do evento é obrigatória";
    }
    
    if (empty($horario)) {
        $erros[] = "Horário do evento é obrigatório";
    }
    
    if (empty($horario_encerramento)) {
        $erros[] = "Horário de encerramento é obrigatório";
    }
    
    // Validar que o horário de encerramento é posterior ao horário de início
    if (!empty($horario) && !empty($horario_encerramento) && strtotime($horario_encerramento) <= strtotime($horario)) {
        $erros[] = "O horário de encerramento deve ser posterior ao horário de início";
    }
    
    if (empty($cidade)) {
        $erros[] = "Cidade é obrigatória";
    }
    
    if (empty($bairro)) {
        $erros[] = "Bairro é obrigatório";
    }
    
    if (empty($rua)) {
        $erros[] = "Rua é obrigatória";
    }
    
    if (empty($numero)) {
        $erros[] = "Número é obrigatório";
    }
    
    // Se não houver erros, prosseguir com a atualização
    if (empty($erros)) {
        // Verificar se há uma nova imagem para atualizar
        $sql_imagem = "";
        $tipos = "ssssssssi"; // tipos para bind_param sem imagem
        $params = [$nome, $descricao, $data, $horario, $cidade, $bairro, $rua, $numero, $id_evento];
        
        if ($imagem_url !== null) {
            $sql_imagem = ", imagem_url = ?";
            $tipos = "sssssssssi"; // tipos para bind_param com imagem
            $params = [$nome, $descricao, $data, $horario, $cidade, $bairro, $rua, $numero, $imagem_url, $id_evento];
        }
        
        // Atualizar o evento no banco de dados
        $stmt = $conexao->prepare("UPDATE Evento SET nome = ?, descricao = ?, data = ?, horario = ?, cidade = ?, bairro = ?, rua = ?, numero = ?{$sql_imagem} WHERE id_evento = ?");
        $stmt->bind_param($tipos, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Evento atualizado com sucesso!";
            $_SESSION['mensagem_tipo'] = "success";
            header("Location: gerenciar_eventos.php");
            exit;
        } else {
            $_SESSION['mensagem'] = "Erro ao atualizar evento: " . $conexao->error;
            $_SESSION['mensagem_tipo'] = "danger";
        }
        
        $stmt->close();
    } else {
        // Exibir erros de validação
        $_SESSION['mensagem'] = implode("<br>", $erros);
        $_SESSION['mensagem_tipo'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="form-container" style="max-width: 700px;">
            <h2 class="form-title">Editar Evento</h2>
            
            <?php
            // Verificar se existe mensagem de erro
            if (isset($_SESSION['mensagem'])) {
                $tipo = $_SESSION['mensagem_tipo'];
                echo '<div class="alert alert-' . $tipo . '">' . $_SESSION['mensagem'] . '</div>';
                // Limpar as mensagens da sessão
                unset($_SESSION['mensagem']);
                unset($_SESSION['mensagem_tipo']);
            }
            ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_evento; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome do Evento:</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($evento['nome']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($evento['descricao']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" class="form-control" value="<?php echo $evento['data']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="horario">Horário:</label>
                    <input type="time" id="horario" name="horario" class="form-control" value="<?php echo $evento['horario']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="cidade">Cidade:</label>
                    <input type="text" id="cidade" name="cidade" class="form-control" value="<?php echo htmlspecialchars($cidade); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="bairro">Bairro:</label>
                    <input type="text" id="bairro" name="bairro" class="form-control" value="<?php echo htmlspecialchars($bairro); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="rua">Rua:</label>
                    <input type="text" id="rua" name="rua" class="form-control" value="<?php echo htmlspecialchars($rua); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="numero" class="form-control" value="<?php echo htmlspecialchars($numero); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="imagem">Imagem do Local (opcional):</label>
                    <input type="file" id="imagem" name="imagem" class="form-control" accept="image/jpeg,image/png">
                    <small class="form-text">Formatos aceitos: JPG e PNG. Tamanho máximo: 2MB.</small>
                    <?php if(!empty($imagem_url)): ?>
                        <div class="mt-2">
                            <p>Imagem atual:</p>
                            <img src="../<?php echo htmlspecialchars($imagem_url); ?>" alt="Imagem do local" style="max-width: 200px; max-height: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="ATIVO" <?php echo ($evento['status'] == 'ATIVO') ? 'selected' : ''; ?>>Ativo</option>
                        <option value="ADIADO" <?php echo ($evento['status'] == 'ADIADO') ? 'selected' : ''; ?>>Adiado</option>
                        <option value="CANCELADO" <?php echo ($evento['status'] == 'CANCELADO') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">Salvar Alterações</button>
                    <a href="gerenciar_eventos.php" class="btn" style="background-color: #95a5a6;">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Script para validação do formulário -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const imagemInput = document.getElementById('imagem');
            
            form.addEventListener('submit', function(event) {
                // Validar tamanho da imagem
                if (imagemInput.files.length > 0) {
                    const fileSize = imagemInput.files[0].size;
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    
                    if (fileSize > maxSize) {
                        event.preventDefault();
                        alert('O arquivo é muito grande. Tamanho máximo permitido: 2MB.');
                        return false;
                    }
                    
                    // Validar tipo de arquivo
                    const fileType = imagemInput.files[0].type;
                    if (fileType !== 'image/jpeg' && fileType !== 'image/png') {
                        event.preventDefault();
                        alert('Tipo de arquivo não permitido. Apenas JPG e PNG são aceitos.');
                        return false;
                    }
                }
            });
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
// Fechar a conexão
$conexao->close();
?>
