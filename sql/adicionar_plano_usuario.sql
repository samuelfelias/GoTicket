-- Adicionar campo plano à tabela Usuario
ALTER TABLE Usuario ADD COLUMN plano ENUM('NORMAL', 'GOLD') NOT NULL DEFAULT 'NORMAL';

-- Atualizar usuários existentes para plano NORMAL
UPDATE Usuario SET plano = 'NORMAL' WHERE plano IS NULL;