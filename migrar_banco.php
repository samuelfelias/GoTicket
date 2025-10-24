<?php
/**
 * Script de migração para atualizar o banco de dados
 * Execute este script para aplicar as mudanças do esquema
 */

require_once 'config/database.php';

echo "<h2>Migração do Banco de Dados - GoTicket</h2>";

try {
    $conexao = conectarBD();
    echo "<p>✓ Conexão com o banco estabelecida</p>";
    
    // 1. Adicionar campos faltantes na tabela usuario
    echo "<h3>1. Atualizando tabela usuario...</h3>";
    
    $campos_usuario = [
        "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS plano enum('NORMAL','GOLD') NOT NULL DEFAULT 'NORMAL'",
        "ALTER TABLE usuario ADD COLUMN IF NOT EXISTS foto_perfil varchar(255) DEFAULT NULL"
    ];
    
    foreach ($campos_usuario as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 2. Atualizar tabela evento
    echo "<h3>2. Atualizando tabela evento...</h3>";
    
    $campos_evento = [
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS cidade varchar(100) NOT NULL DEFAULT ''",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS bairro varchar(100) NOT NULL DEFAULT ''",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS rua varchar(200) NOT NULL DEFAULT ''",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS numero varchar(20) NOT NULL DEFAULT ''",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS imagem_url varchar(255) DEFAULT NULL",
        "ALTER TABLE evento ADD COLUMN IF NOT EXISTS data_criacao datetime DEFAULT CURRENT_TIMESTAMP"
    ];
    
    foreach ($campos_evento as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 3. Atualizar tabela ingresso
    echo "<h3>3. Atualizando tabela ingresso...</h3>";
    
    $campos_ingresso = [
        "ALTER TABLE ingresso ADD COLUMN IF NOT EXISTS descricao text",
        "ALTER TABLE ingresso ADD COLUMN IF NOT EXISTS created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP",
        "ALTER TABLE ingresso ADD COLUMN IF NOT EXISTS updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    foreach ($campos_ingresso as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 4. Atualizar tabela pedido
    echo "<h3>4. Atualizando tabela pedido...</h3>";
    
    $campos_pedido = [
        "ALTER TABLE pedido ADD COLUMN IF NOT EXISTS id_evento int DEFAULT NULL",
        "ALTER TABLE pedido ADD COLUMN IF NOT EXISTS valor_total decimal(10,2) NOT NULL DEFAULT 0"
    ];
    
    foreach ($campos_pedido as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 5. Atualizar tabela itempedido
    echo "<h3>5. Atualizando tabela itempedido...</h3>";
    
    $campos_itempedido = [
        "ALTER TABLE itempedido ADD COLUMN IF NOT EXISTS preco_unitario decimal(10,2) NOT NULL DEFAULT 0"
    ];
    
    foreach ($campos_itempedido as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 6. Atualizar tabela pagamento
    echo "<h3>6. Atualizando tabela pagamento...</h3>";
    
    $campos_pagamento = [
        "ALTER TABLE pagamento ADD COLUMN IF NOT EXISTS metodo_pagamento enum('CARTAO','DEBITO','PIX','BOLETO') NOT NULL DEFAULT 'CARTAO'",
        "ALTER TABLE pagamento ADD COLUMN IF NOT EXISTS status_pagamento enum('PENDENTE','APROVADO','REJEITADO') NOT NULL DEFAULT 'PENDENTE'"
    ];
    
    foreach ($campos_pagamento as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 7. Atualizar tabela ingressousuario
    echo "<h3>7. Atualizando tabela ingressousuario...</h3>";
    
    $campos_ingressousuario = [
        "ALTER TABLE ingressousuario ADD COLUMN IF NOT EXISTS id_evento int DEFAULT NULL",
        "ALTER TABLE ingressousuario ADD COLUMN IF NOT EXISTS data_uso datetime DEFAULT NULL"
    ];
    
    foreach ($campos_ingressousuario as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Campo adicionado: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Campo já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    // 8. Criar tabelas novas
    echo "<h3>8. Criando novas tabelas...</h3>";
    
    $novas_tabelas = [
        "CREATE TABLE IF NOT EXISTS alerta (
            id_alerta int NOT NULL AUTO_INCREMENT,
            id_usuario int NOT NULL,
            id_evento int NOT NULL,
            ativo tinyint(1) NOT NULL DEFAULT '1',
            PRIMARY KEY (id_alerta),
            UNIQUE KEY unico_usuario_evento (id_usuario,id_evento),
            KEY id_evento (id_evento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
        
        "CREATE TABLE IF NOT EXISTS avaliacao (
            id_avaliacao int NOT NULL AUTO_INCREMENT,
            id_evento int NOT NULL,
            id_usuario int NOT NULL,
            nota int NOT NULL,
            comentario text,
            data_avaliacao datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_avaliacao),
            UNIQUE KEY unico_usuario_evento (id_usuario,id_evento),
            KEY id_evento (id_evento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
        
        "CREATE TABLE IF NOT EXISTS notificacao (
            id_notificacao int NOT NULL AUTO_INCREMENT,
            id_usuario int NOT NULL,
            mensagem text NOT NULL,
            tipo enum('INFO','SUCESSO','ALERTA','ERRO') NOT NULL,
            lida tinyint(1) NOT NULL DEFAULT '0',
            data_criacao datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_notificacao),
            KEY id_usuario (id_usuario)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
        
        "CREATE TABLE IF NOT EXISTS preferenciausuario (
            id_usuario int NOT NULL,
            id_tipo_evento int NOT NULL,
            data_adicao datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_usuario,id_tipo_evento),
            KEY id_tipo_evento (id_tipo_evento)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS recuperacaosenha (
            id_recuperacao int NOT NULL AUTO_INCREMENT,
            id_usuario int NOT NULL,
            token varchar(255) NOT NULL,
            expira_em datetime NOT NULL,
            utilizado tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (id_recuperacao),
            UNIQUE KEY token (token),
            KEY id_usuario (id_usuario)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
        
        "CREATE TABLE IF NOT EXISTS redefinicaosenha (
            id int NOT NULL AUTO_INCREMENT,
            id_usuario int NOT NULL,
            token varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
            data_expiracao datetime NOT NULL,
            utilizado tinyint(1) NOT NULL DEFAULT '0',
            data_criacao datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY id_usuario (id_usuario),
            KEY idx_token (token)
        ) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS tipoevento (
            id_tipo_evento int NOT NULL AUTO_INCREMENT,
            nome varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
            descricao text COLLATE utf8mb4_unicode_ci,
            PRIMARY KEY (id_tipo_evento),
            UNIQUE KEY nome (nome)
        ) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($novas_tabelas as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Tabela criada/atualizada: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Erro ao criar tabela: " . $conexao->error . "</p>";
        }
    }
    
    // 9. Adicionar foreign keys
    echo "<h3>9. Adicionando foreign keys...</h3>";
    
    $foreign_keys = [
        "ALTER TABLE alerta ADD CONSTRAINT IF NOT EXISTS alerta_ibfk_1 FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)",
        "ALTER TABLE alerta ADD CONSTRAINT IF NOT EXISTS alerta_ibfk_2 FOREIGN KEY (id_evento) REFERENCES evento (id_evento)",
        "ALTER TABLE avaliacao ADD CONSTRAINT IF NOT EXISTS avaliacao_ibfk_1 FOREIGN KEY (id_evento) REFERENCES evento (id_evento)",
        "ALTER TABLE avaliacao ADD CONSTRAINT IF NOT EXISTS avaliacao_ibfk_2 FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)",
        "ALTER TABLE notificacao ADD CONSTRAINT IF NOT EXISTS notificacao_ibfk_1 FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)",
        "ALTER TABLE recuperacaosenha ADD CONSTRAINT IF NOT EXISTS recuperacaosenha_ibfk_1 FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)"
    ];
    
    foreach ($foreign_keys as $sql) {
        if ($conexao->query($sql)) {
            echo "<p>✓ Foreign key adicionada: " . substr($sql, 0, 50) . "...</p>";
        } else {
            echo "<p>⚠ Foreign key já existe ou erro: " . $conexao->error . "</p>";
        }
    }
    
    echo "<h3>✅ Migração concluída com sucesso!</h3>";
    echo "<p>O banco de dados foi atualizado com todas as mudanças necessárias.</p>";
    echo "<p><a href='index.php'>Voltar ao sistema</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Erro durante a migração:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}

$conexao->close();
?>
