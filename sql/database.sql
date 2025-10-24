-- Script SQL para criação do banco de dados GoTicket no Supabase (PostgreSQL)
-- Execute este script no SQL Editor do Supabase

-- Criar schema sistema_ingressos (se ainda não existir)
CREATE SCHEMA IF NOT EXISTS sistema_ingressos;

-- Definir o schema padrão
SET search_path TO sistema_ingressos;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trigger para atualizar updated_at automaticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_usuarios_updated_at
    BEFORE UPDATE ON usuarios
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Tabela de Eventos
CREATE TABLE IF NOT EXISTS eventos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    data TIMESTAMP NOT NULL,
    local VARCHAR(255) NOT NULL,
    capacidade INTEGER NOT NULL,
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('show', 'palestra', 'teatro')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para eventos
CREATE INDEX IF NOT EXISTS idx_eventos_tipo ON eventos(tipo);
CREATE INDEX IF NOT EXISTS idx_eventos_data ON eventos(data);

CREATE TRIGGER update_eventos_updated_at
    BEFORE UPDATE ON eventos
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Tabela de Ingressos
CREATE TABLE IF NOT EXISTS ingressos (
    id SERIAL PRIMARY KEY,
    evento_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL,
    preco_base DECIMAL(10, 2) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'disponivel' CHECK (status IN ('disponivel', 'vendido', 'ativo', 'usado', 'cancelado')),
    codigo_validacao VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Índices para ingressos
CREATE INDEX IF NOT EXISTS idx_ingressos_evento ON ingressos(evento_id);
CREATE INDEX IF NOT EXISTS idx_ingressos_usuario ON ingressos(usuario_id);
CREATE INDEX IF NOT EXISTS idx_ingressos_status ON ingressos(status);
CREATE INDEX IF NOT EXISTS idx_ingressos_codigo ON ingressos(codigo_validacao);

CREATE TRIGGER update_ingressos_updated_at
    BEFORE UPDATE ON ingressos
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Dados de exemplo
INSERT INTO usuarios (nome, email, telefone) VALUES
('João Silva', 'joao@email.com', '(11) 98765-4321'),
('Maria Santos', 'maria@email.com', '(11) 91234-5678'),
('Pedro Oliveira', 'pedro@email.com', '(21) 99876-5432')
ON CONFLICT (email) DO NOTHING;

INSERT INTO eventos (nome, descricao, data, local, capacidade, tipo) VALUES
('Show da Banda XYZ', 'Show de rock ao vivo', '2025-12-15 20:00:00', 'Estádio Municipal', 5000, 'show'),
('Palestra Tech 2025', 'Palestra sobre inovação tecnológica', '2025-11-20 14:00:00', 'Centro de Convenções', 500, 'palestra'),
('Peça: Hamlet', 'Clássico de Shakespeare', '2025-11-30 19:00:00', 'Teatro Nacional', 300, 'teatro');

-- Confirmar que tudo foi criado
SELECT 'Tabelas criadas com sucesso no schema sistema_ingressos!' as status;
