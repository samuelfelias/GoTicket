<?php
// Script para atualizar o banco de dados com as alterações necessárias

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Verificar se a coluna plano já existe na tabela Usuario
$verificar_plano = $conexao->query("SHOW COLUMNS FROM usuario LIKE 'plano'");
if ($verificar_plano->num_rows == 0) {
    // Adicionar coluna plano à tabela Usuario
    $conexao->query("ALTER TABLE Usuario ADD COLUMN plano ENUM('NORMAL', 'GOLD') NOT NULL DEFAULT 'NORMAL'");
    echo "<p>Coluna 'plano' adicionada à tabela Usuario.</p>";
} else {
    echo "<p>Coluna 'plano' já existe na tabela Usuario.</p>";
}

// Verificar se a coluna data_criacao já existe na tabela Evento
$verificar_data_criacao = $conexao->query("SHOW COLUMNS FROM Evento LIKE 'data_criacao'");
if ($verificar_data_criacao->num_rows == 0) {
    // Adicionar coluna data_criacao à tabela Evento
    $conexao->query("ALTER TABLE Evento ADD COLUMN data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
    echo "<p>Coluna 'data_criacao' adicionada à tabela Evento.</p>";
    
    // Atualizar eventos existentes para a data atual
    $conexao->query("UPDATE Evento SET data_criacao = CURRENT_TIMESTAMP WHERE data_criacao IS NULL");
    echo "<p>Eventos existentes atualizados com data de criação.</p>";
} else {
    echo "<p>Coluna 'data_criacao' já existe na tabela Evento.</p>";
}

echo "<p>Atualização concluída com sucesso!</p>";
echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";

$conexao->close();
?>
