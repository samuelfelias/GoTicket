-- O campo data_criacao já foi adicionado à tabela Evento
-- ALTER TABLE Evento ADD COLUMN data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Atualizar eventos existentes para a data atual
UPDATE Evento SET data_criacao = CURRENT_TIMESTAMP WHERE data_criacao IS NULL;