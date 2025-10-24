-- Adicionar tabela para tipos de eventos
CREATE TABLE IF NOT EXISTS TipoEvento (
    id_tipo_evento INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT
);

-- Adicionar tabela para preferências de usuários
CREATE TABLE IF NOT EXISTS PreferenciaUsuario (
    id_usuario INT NOT NULL,
    id_tipo_evento INT NOT NULL,
    data_adicao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_tipo_evento),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_tipo_evento) REFERENCES TipoEvento(id_tipo_evento) ON DELETE CASCADE
);

-- Adicionar campo foto_perfil à tabela Usuario
ALTER TABLE Usuario ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL;

-- Inserir os tipos de eventos
INSERT INTO TipoEvento (nome) VALUES
('Show Musical'),
('Festival de Música'),
('Festa / Balada'),
('Encontro Social'),
('Aniversário'),
('Casamento'),
('Formatura'),
('Festival de Gastronomia'),
('Feira de Artesanato'),
('Evento Corporativo'),
('Palestra / Workshop'),
('Encontro Profissional'),
('Exposição de Arte'),
('Teatro'),
('Evento Esportivo'),
('Encontro Familiar'),
('Feira de Games'),
('Churrasco'),
('Evento Religioso'),
('Feira de Negócios'),
('Lançamento de Produto'),
('Festa Junina'),
('Karaokê'),
('Encontro de Carros');