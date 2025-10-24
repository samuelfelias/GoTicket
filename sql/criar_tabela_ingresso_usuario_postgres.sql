-- Script para criar a tabela ingresso_usuario para PostgreSQL
CREATE TABLE IF NOT EXISTS ingresso_usuario (
    id SERIAL PRIMARY KEY,
    id_ingresso INT NOT NULL,
    id_usuario INT NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    status VARCHAR(20) DEFAULT 'ATIVO',
    data_aquisicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_uso TIMESTAMP NULL,
    FOREIGN KEY (id_ingresso) REFERENCES ingresso(id_ingresso),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);