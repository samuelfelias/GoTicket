-- Criação do banco de dados
-- CREATE DATABASE sistema_ingressos;
-- \c sistema_ingressos;

-- Extensão para UUID (opcional, se necessário para códigos únicos)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Tabela Usuario
CREATE TABLE IF NOT EXISTS usuario (
  id_usuario SERIAL PRIMARY KEY,
  cpf VARCHAR(11) UNIQUE NOT NULL,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('ADMIN','CLIENTE','ORGANIZADOR')),
  senha VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  plano VARCHAR(20) NOT NULL DEFAULT 'NORMAL' CHECK (plano IN ('NORMAL','GOLD')),
  foto_perfil VARCHAR(255)
);

-- Tabela Evento
CREATE TABLE IF NOT EXISTS evento (
  id_evento SERIAL PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  data DATE NOT NULL,
  horario TIME NOT NULL,
  local VARCHAR(200) NOT NULL,
  id_organizador INT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'ATIVO' CHECK (status IN ('ATIVO','CANCELADO','ADIADO')),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cidade VARCHAR(100) NOT NULL DEFAULT '',
  bairro VARCHAR(100) NOT NULL DEFAULT '',
  rua VARCHAR(200) NOT NULL DEFAULT '',
  numero VARCHAR(20) NOT NULL DEFAULT '',
  imagem_url VARCHAR(255),
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela Ingresso
CREATE TABLE IF NOT EXISTS ingresso (
  id_ingresso SERIAL PRIMARY KEY,
  id_evento INT NOT NULL,
  tipo VARCHAR(50) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  descricao TEXT,
  quantidade_disponivel INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tabela Pedido
CREATE TABLE IF NOT EXISTS pedido (
  id_pedido SERIAL PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_evento INT,
  data_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(20) NOT NULL DEFAULT 'PENDENTE' CHECK (status IN ('PENDENTE','CONFIRMADO','CANCELADO')),
  valor_total DECIMAL(10,2) NOT NULL
);

-- Tabela ItemPedido
CREATE TABLE IF NOT EXISTS itempedido (
  id_pedido INT NOT NULL,
  id_ingresso INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id_pedido, id_ingresso)
);

-- Tabela Pagamento
CREATE TABLE IF NOT EXISTS pagamento (
  id_pagamento SERIAL PRIMARY KEY,
  id_pedido INT NOT NULL,
  data_pagamento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  valor DECIMAL(10,2) NOT NULL,
  metodo_pagamento VARCHAR(20) NOT NULL CHECK (metodo_pagamento IN ('CARTAO','DEBITO','PIX','BOLETO')),
  status_pagamento VARCHAR(20) NOT NULL DEFAULT 'PENDENTE' CHECK (status_pagamento IN ('PENDENTE','APROVADO','REJEITADO'))
);

-- Tabela IngressoUsuario
CREATE TABLE IF NOT EXISTS ingressousuario (
  id SERIAL PRIMARY KEY,
  ingresso_id INT NOT NULL,
  usuario_id INT NOT NULL,
  id_evento INT,
  codigo VARCHAR(50) UNIQUE NOT NULL DEFAULT uuid_generate_v4()::VARCHAR(50), -- Usando UUID para código único
  status VARCHAR(20) DEFAULT 'ATIVO' CHECK (status IN ('ATIVO','CANCELADO','USADO')),
  data_uso TIMESTAMP,
  data_aquisicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela Alerta
CREATE TABLE IF NOT EXISTS alerta (
  id_alerta SERIAL PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_evento INT NOT NULL,
  ativo BOOLEAN NOT NULL DEFAULT TRUE,
  UNIQUE (id_usuario, id_evento)
);

-- Tabela Avaliacao
CREATE TABLE IF NOT EXISTS avaliacao (
  id_avaliacao SERIAL PRIMARY KEY,
  id_evento INT NOT NULL,
  id_usuario INT NOT NULL,
  nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
  comentario TEXT,
  data_avaliacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (id_usuario, id_evento)
);

-- Tabela Notificacao
CREATE TABLE IF NOT EXISTS notificacao (
  id_notificacao SERIAL PRIMARY KEY,
  id_usuario INT NOT NULL,
  mensagem TEXT NOT NULL,
  tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('INFO','SUCESSO','ALERTA','ERRO')),
  lida BOOLEAN NOT NULL DEFAULT FALSE,
  data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tabela TipoEvento
CREATE TABLE IF NOT EXISTS tipoevento (
  id_tipo_evento SERIAL PRIMARY KEY,
  nome VARCHAR(100) UNIQUE NOT NULL,
  descricao TEXT
);

-- Tabela PreferenciaUsuario
CREATE TABLE IF NOT EXISTS preferenciausuario (
  id_usuario INT NOT NULL,
  id_tipo_evento INT NOT NULL,
  data_adicao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario, id_tipo_evento)
);

-- Tabela RecuperacaoSenha
CREATE TABLE IF NOT EXISTS recuperacaosenha (
  id_recuperacao SERIAL PRIMARY KEY,
  id_usuario INT NOT NULL,
  token VARCHAR(255) UNIQUE NOT NULL,
  expira_em TIMESTAMP NOT NULL,
  utilizado BOOLEAN NOT NULL DEFAULT FALSE
);

-- Tabela RedefinicaoSenha
CREATE TABLE IF NOT EXISTS redefinicaosenha (
  id SERIAL PRIMARY KEY,
  id_usuario INT NOT NULL,
  token VARCHAR(64) NOT NULL,
  data_expiracao TIMESTAMP NOT NULL,
  utilizado BOOLEAN NOT NULL DEFAULT FALSE,
  data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Foreign Keys
ALTER TABLE evento ADD CONSTRAINT fk_evento_usuario FOREIGN KEY (id_organizador) REFERENCES usuario (id_usuario);
ALTER TABLE ingresso ADD CONSTRAINT fk_ingresso_evento FOREIGN KEY (id_evento) REFERENCES evento (id_evento);
ALTER TABLE pedido ADD CONSTRAINT fk_pedido_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);
ALTER TABLE pedido ADD CONSTRAINT fk_pedido_evento FOREIGN KEY (id_evento) REFERENCES evento (id_evento);
ALTER TABLE itempedido ADD CONSTRAINT fk_itempedido_pedido FOREIGN KEY (id_pedido) REFERENCES pedido (id_pedido) ON DELETE CASCADE;
ALTER TABLE itempedido ADD CONSTRAINT fk_itempedido_ingresso FOREIGN KEY (id_ingresso) REFERENCES ingresso (id_ingresso);
ALTER TABLE pagamento ADD CONSTRAINT fk_pagamento_pedido FOREIGN KEY (id_pedido) REFERENCES pedido (id_pedido) ON DELETE CASCADE;
ALTER TABLE ingressousuario ADD CONSTRAINT fk_ingressousuario_ingresso FOREIGN KEY (ingresso_id) REFERENCES ingresso (id_ingresso);
ALTER TABLE ingressousuario ADD CONSTRAINT fk_ingressousuario_usuario FOREIGN KEY (usuario_id) REFERENCES usuario (id_usuario);
ALTER TABLE ingressousuario ADD CONSTRAINT fk_ingressousuario_evento FOREIGN KEY (id_evento) REFERENCES evento (id_evento);
ALTER TABLE alerta ADD CONSTRAINT fk_alerta_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);
ALTER TABLE alerta ADD CONSTRAINT fk_alerta_evento FOREIGN KEY (id_evento) REFERENCES evento (id_evento);
ALTER TABLE avaliacao ADD CONSTRAINT fk_avaliacao_evento FOREIGN KEY (id_evento) REFERENCES evento (id_evento);
ALTER TABLE avaliacao ADD CONSTRAINT fk_avaliacao_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);
ALTER TABLE notificacao ADD CONSTRAINT fk_notificacao_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);
ALTER TABLE preferenciausuario ADD CONSTRAINT fk_pref_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario) ON DELETE CASCADE;
ALTER TABLE preferenciausuario ADD CONSTRAINT fk_pref_tipoevento FOREIGN KEY (id_tipo_evento) REFERENCES tipoevento (id_tipo_evento) ON DELETE CASCADE;
ALTER TABLE recuperacaosenha ADD CONSTRAINT fk_recuperacao_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);
ALTER TABLE redefinicaosenha ADD CONSTRAINT fk_redefinicao_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario);

-- Inserir tipos de eventos
INSERT INTO tipoevento (nome) VALUES
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