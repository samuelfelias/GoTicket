<?php
require_once 'config/database.php';

try {
    // Conectar ao banco de dados usando as constantes definidas
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    $conexao = new PDO($dsn, DB_USER, DB_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    
    echo "<h1>Adicionando coluna data_compra à tabela ingresso_usuario</h1>";
    
    // Definir o schema
    $conexao->exec("SET search_path TO sistema_ingressos");
    
    // SQL para adicionar a coluna data_compra
    $sql = "ALTER TABLE ingresso_usuario ADD COLUMN IF NOT EXISTS data_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    
    // Executar a consulta
    $conexao->exec($sql);
    
    // Atualizar os registros existentes para usar data_aquisicao como data_compra
    $sql_update = "UPDATE ingresso_usuario SET data_compra = data_aquisicao WHERE data_compra IS NULL";
    $conexao->exec($sql_update);
    
    echo "<p style='color: green'>Coluna data_compra adicionada com sucesso!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red'>Erro ao adicionar coluna: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
?>