-- Script para corrigir a coluna horario_encerramento na tabela evento

-- Verificar se estamos no esquema correto (sistema_ingressos ou public)
DO $$
DECLARE
    esquema_correto TEXT;
BEGIN
    -- Verificar qual esquema contém a tabela evento
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'sistema_ingressos' AND table_name = 'evento') THEN
        esquema_correto := 'sistema_ingressos';
    ELSE
        esquema_correto := 'public';
    END IF;
    
    -- Renomear a coluna horario para horario_inicio se necessário
    IF EXISTS (
        SELECT 1 
        FROM information_schema.columns 
        WHERE table_schema = esquema_correto
        AND table_name = 'evento' 
        AND column_name = 'horario'
    ) THEN
        EXECUTE 'ALTER TABLE ' || esquema_correto || '.evento RENAME COLUMN horario TO horario_inicio';
        RAISE NOTICE 'Coluna horario renomeada para horario_inicio no esquema %', esquema_correto;
    END IF;
    
    -- Adicionar coluna horario_encerramento se não existir
    IF NOT EXISTS (
        SELECT 1 
        FROM information_schema.columns 
        WHERE table_schema = esquema_correto
        AND table_name = 'evento' 
        AND column_name = 'horario_encerramento'
    ) THEN
        EXECUTE 'ALTER TABLE ' || esquema_correto || '.evento ADD COLUMN horario_encerramento TIME';
        RAISE NOTICE 'Coluna horario_encerramento adicionada no esquema %', esquema_correto;
    END IF;
    
    -- Garantir que as colunas sejam do tipo TIME
    EXECUTE 'ALTER TABLE ' || esquema_correto || '.evento ALTER COLUMN horario_inicio TYPE TIME USING horario_inicio::TIME';
    EXECUTE 'ALTER TABLE ' || esquema_correto || '.evento ALTER COLUMN horario_encerramento TYPE TIME USING horario_encerramento::TIME';
    
    -- Atualizar valores NULL de horario_encerramento
    EXECUTE 'UPDATE ' || esquema_correto || '.evento SET horario_encerramento = horario_inicio + INTERVAL ''2 hours'' WHERE horario_encerramento IS NULL';
    
    -- Garantir que os valores estejam no formato correto
    EXECUTE 'UPDATE ' || esquema_correto || '.evento SET 
        horario_inicio = horario_inicio::TIME,
        horario_encerramento = horario_encerramento::TIME
        WHERE horario_inicio IS NOT NULL OR horario_encerramento IS NOT NULL';
    
    RAISE NOTICE 'Script executado com sucesso no esquema %', esquema_correto;
END $$;