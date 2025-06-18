CREATE DATABASE IF NOT EXISTS sistema_ingressos;
USE sistema_ingressos;

-- Tabela Usuario
CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(11) NOT NULL UNIQUE, 
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE, 
    tipo ENUM('ADMIN', 'CLIENTE', 'ORGANIZADOR') NOT NULL,
    senha VARCHAR(255) NOT NULL 
);

-- Tabela Evento
CREATE TABLE Evento (
    id_evento INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    data DATE NOT NULL,
    horario TIME NOT NULL,
    local VARCHAR(200) NOT NULL,
    id_organizador INT NOT NULL,
    status ENUM('ATIVO', 'CANCELADO', 'ADIADO') NOT NULL DEFAULT 'ATIVO',
    FOREIGN KEY (id_organizador) REFERENCES Usuario(id_usuario)
);

-- Tabela Ingresso
CREATE TABLE Ingresso (
    id_ingresso INT AUTO_INCREMENT PRIMARY KEY,
    id_evento INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    preco DECIMAL(10,2) NOT NULL CHECK (preco >= 0),
    quantidade_disponivel INT NOT NULL CHECK (quantidade_disponivel >= 0),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (id_evento) REFERENCES Evento(id_evento)
);

-- Tabela Pedido
CREATE TABLE Pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    status ENUM('PENDENTE', 'CONFIRMADO', 'CANCELADO') NOT NULL,
    valor_total DECIMAL(10,2),	
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

-- Tabela ItemPedido
CREATE TABLE ItemPedido (
    id_pedido INT NOT NULL,
    id_ingresso INT NOT NULL,
    quantidade INT NOT NULL CHECK (quantidade > 0),
    PRIMARY KEY (id_pedido, id_ingresso),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido),
    FOREIGN KEY (id_ingresso) REFERENCES Ingresso(id_ingresso)
);

-- Tabela Pagamento
CREATE TABLE Pagamento (
    id_pagamento INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    data_pagamento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valor DECIMAL(10,2) NOT NULL CHECK (valor > 0),
    metodo_pagamento ENUM('CARTAO', 'PIX', 'BOLETO') NOT NULL,
    status_pagamento ENUM('PENDENTE', 'APROVADO', 'REJEITADO') NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
);