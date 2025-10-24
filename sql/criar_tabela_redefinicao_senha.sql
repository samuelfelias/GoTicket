-- Script para criar a tabela de tokens de redefinição de senha

USE sistema_ingressos;

-- Criar tabela para armazenar tokens de redefinição de senha
CREATE TABLE IF NOT EXISTS RedefinicaoSenha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    data_expiracao DATETIME NOT NULL,
    utilizado BOOLEAN NOT NULL DEFAULT 0,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

-- Índice para busca rápida por token
CREATE INDEX idx_token ON RedefinicaoSenha(token);