-- Script para atualizar a tabela Evento
-- Remover a coluna 'local' e adicionar as novas colunas estruturadas

USE sistema_ingressos;

-- Adicionar as novas colunas
ALTER TABLE Evento ADD COLUMN cidade VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE Evento ADD COLUMN bairro VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE Evento ADD COLUMN rua VARCHAR(200) NOT NULL DEFAULT '';
ALTER TABLE Evento ADD COLUMN numero VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE Evento ADD COLUMN imagem_url VARCHAR(255) NULL;

-- Atualizar os registros existentes (opcional)
-- Este comando divide o valor atual da coluna 'local' e o distribui nas novas colunas
-- Assumindo que o formato atual seja "Rua, Número, Bairro, Cidade"
-- UPDATE Evento SET cidade = 'Cidade padrão', bairro = 'Bairro padrão', rua = local, numero = 'S/N';

-- Remover a coluna 'local' após a migração dos dados
ALTER TABLE Evento DROP COLUMN local;