<?php
// Script para criar a tabela de redefinição de senha

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Verificar conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

echo "<h2>Criando tabela de redefinição de senha...</h2>";

// SQL para criar a tabela
$sql = "CREATE TABLE IF NOT EXISTS RedefinicaoSenha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    data_expiracao DATETIME NOT NULL,
    utilizado BOOLEAN NOT NULL DEFAULT 0,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
)";

// Executar a query
if ($conexao->query($sql) === TRUE) {
    echo "<p style='color: green'>Tabela RedefinicaoSenha criada com sucesso!</p>";
    
    // Criar índice para busca rápida por token
    $sql_indice = "CREATE INDEX idx_token ON RedefinicaoSenha(token)";
    if ($conexao->query($sql_indice) === TRUE) {
        echo "<p style='color: green'>Índice para token criado com sucesso!</p>";
    } else {
        echo "<p style='color: red'>Erro ao criar índice: " . $conexao->error . "</p>";
    }
} else {
    echo "<p style='color: red'>Erro ao criar tabela: " . $conexao->error . "</p>";
}

$conexao->close();

echo "<p><a href='login.php'>Voltar para a página de login</a></p>";
?>
