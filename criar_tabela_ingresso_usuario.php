<?php
require_once 'config/database.php';

try {
    // Conectar ao banco de dados usando as constantes definidas
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    $conexao = new PDO($dsn, DB_USER, DB_PASS, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    
    echo "<h1>Criando tabela ingresso_usuario no PostgreSQL</h1>";
    
    // Verificar se o schema existe e definir como padrão
    $conexao->exec("CREATE SCHEMA IF NOT EXISTS sistema_ingressos");
    $conexao->exec("SET search_path TO sistema_ingressos");
    
    // SQL para criar a tabela ingresso_usuario
    $sql = "CREATE TABLE IF NOT EXISTS ingresso_usuario (
        id SERIAL PRIMARY KEY,
        id_ingresso INT NOT NULL,
        id_usuario INT NOT NULL,
        id_evento INT NOT NULL,
        codigo VARCHAR(50) NOT NULL UNIQUE,
        status VARCHAR(20) DEFAULT 'ATIVO',
        data_aquisicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_uso TIMESTAMP NULL,
        FOREIGN KEY (id_ingresso) REFERENCES ingresso(id_ingresso),
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
        FOREIGN KEY (id_evento) REFERENCES evento(id_evento)
    )";
    
    // Executar a consulta
    $conexao->exec($sql);
    
    echo "<p style='color: green'>Tabela ingresso_usuario criada com sucesso!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red'>Erro ao criar tabela: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
?>