-- Adicionar campo horario_encerramento na tabela evento
ALTER TABLE evento ADD COLUMN horario_encerramento TIME;

-- Renomear o campo horario para horario_inicio para maior clareza
ALTER TABLE evento RENAME COLUMN horario TO horario_inicio;