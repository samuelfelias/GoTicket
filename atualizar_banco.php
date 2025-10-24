<?php
// Script para executar as alterações no banco de dados

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Verificar conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

echo "<h2>Atualizando a estrutura da tabela Evento...</h2>";

// Adicionar as novas colunas
$queries = [
    "ALTER TABLE Evento ADD COLUMN cidade VARCHAR(100) NOT NULL DEFAULT '';",
    "ALTER TABLE Evento ADD COLUMN bairro VARCHAR(100) NOT NULL DEFAULT '';",
    "ALTER TABLE Evento ADD COLUMN rua VARCHAR(200) NOT NULL DEFAULT '';",
    "ALTER TABLE Evento ADD COLUMN numero VARCHAR(20) NOT NULL DEFAULT '';",
    "ALTER TABLE Evento ADD COLUMN imagem_url VARCHAR(255) NULL;"
];

// Executar as queries
$sucesso = true;
foreach ($queries as $query) {
    echo "Executando: $query<br>";
    if (!$conexao->query($query)) {
        echo "<p style='color: red'>Erro: " . $conexao->error . "</p>";
        $sucesso = false;
    } else {
        echo "<p style='color: green'>Sucesso!</p>";
    }
}

// Verificar se há dados existentes para migrar
$result = $conexao->query("SELECT COUNT(*) as total FROM Evento WHERE local IS NOT NULL");
$row = $result->fetch_assoc();
$total = $row['total'];

if ($total > 0) {
    echo "<h3>Encontrados $total eventos com endereço no formato antigo.</h3>";
    echo "<p>Você pode migrar os dados do campo 'local' para os novos campos estruturados.</p>";
    
    // Formulário para confirmar a migração
    echo "<form method='post'>";
    echo "<input type='hidden' name='migrar' value='1'>";
    echo "<button type='submit'>Migrar dados</button>";
    echo "</form>";
    
    // Processar a migração se confirmada
    if (isset($_POST['migrar'])) {
        $update = "UPDATE Evento SET cidade = 'Cidade não especificada', bairro = 'Bairro não especificado', rua = local, numero = 'S/N' WHERE local IS NOT NULL";
        
        if ($conexao->query($update)) {
            echo "<p style='color: green'>Dados migrados com sucesso!</p>";
            
            // Perguntar se deseja remover a coluna antiga
            echo "<h3>Deseja remover a coluna 'local'?</h3>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='remover' value='1'>";
            echo "<button type='submit'>Remover coluna 'local'</button>";
            echo "</form>";
        } else {
            echo "<p style='color: red'>Erro ao migrar dados: " . $conexao->error . "</p>";
        }
    }
}

// Processar a remoção da coluna se confirmada
if (isset($_POST['remover'])) {
    $drop = "ALTER TABLE Evento DROP COLUMN local";
    
    if ($conexao->query($drop)) {
        echo "<p style='color: green'>Coluna 'local' removida com sucesso!</p>";
    } else {
        echo "<p style='color: red'>Erro ao remover coluna: " . $conexao->error . "</p>";
    }
}

// Criar diretório para uploads se não existir
$diretorio = "uploads/eventos/";
if (!is_dir($diretorio)) {
    if (mkdir($diretorio, 0755, true)) {
        echo "<p style='color: green'>Diretório para uploads criado com sucesso!</p>";
    } else {
        echo "<p style='color: red'>Erro ao criar diretório para uploads.</p>";
    }
} else {
    echo "<p>Diretório para uploads já existe.</p>";
}

echo "<h2>Processo de atualização concluído!</h2>";
echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";

// Fechar conexão
$conexao->close();
?>
