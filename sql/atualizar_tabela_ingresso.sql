-- Script para atualizar a tabela Ingresso

USE sistema_ingressos;

-- Verificar se a coluna quantidade_disponivel já existe
SET @existe_coluna = 0;
SELECT COUNT(*) INTO @existe_coluna FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'sistema_ingressos' AND TABLE_NAME = 'Ingresso' AND COLUMN_NAME = 'quantidade_disponivel';

-- Adicionar a coluna quantidade_disponivel se não existir
SET @sql = IF(@existe_coluna = 0, 
    'ALTER TABLE Ingresso ADD COLUMN quantidade_disponivel INT NOT NULL DEFAULT 0 CHECK (quantidade_disponivel >= 0)',
    'SELECT "Coluna quantidade_disponivel já existe"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar se a coluna status existe e removê-la se necessário
SET @existe_coluna = 0;
SELECT COUNT(*) INTO @existe_coluna FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'sistema_ingressos' AND TABLE_NAME = 'Ingresso' AND COLUMN_NAME = 'status';

-- Remover a coluna status se existir
SET @sql = IF(@existe_coluna > 0, 
    'ALTER TABLE Ingresso DROP COLUMN status',
    'SELECT "Coluna status não existe"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Atualizar a estrutura da tabela IngressoUsuario se necessário
SET @existe_coluna = 0;
SELECT COUNT(*) INTO @existe_coluna FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'sistema_ingressos' AND TABLE_NAME = 'IngressoUsuario' AND COLUMN_NAME = 'id_evento';

-- Adicionar a coluna id_evento se não existir
SET @sql = IF(@existe_coluna = 0, 
    'ALTER TABLE IngressoUsuario ADD COLUMN id_evento INT AFTER usuario_id',
    'SELECT "Coluna id_evento já existe"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar a coluna data_uso se não existir
SET @existe_coluna = 0;
SELECT COUNT(*) INTO @existe_coluna FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'sistema_ingressos' AND TABLE_NAME = 'IngressoUsuario' AND COLUMN_NAME = 'data_uso';

SET @sql = IF(@existe_coluna = 0, 
    'ALTER TABLE IngressoUsuario ADD COLUMN data_uso DATETIME NULL AFTER data_aquisicao',
    'SELECT "Coluna data_uso já existe"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Atualizar a quantidade_disponivel para todos os ingressos
UPDATE Ingresso SET quantidade_disponivel = (
    SELECT COUNT(*) FROM IngressoUsuario WHERE ingresso_id = Ingresso.id_ingresso
);

SELECT 'Atualização da tabela Ingresso concluída com sucesso!' AS Mensagem;