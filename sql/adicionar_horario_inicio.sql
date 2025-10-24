-- Adicionar coluna horario_inicio na tabela evento
ALTER TABLE evento ADD COLUMN horario_inicio TIME;

-- Se a tabela já tiver uma coluna 'horario', copiar os valores para horario_inicio
UPDATE evento SET horario_inicio = horario WHERE horario_inicio IS NULL AND horario IS NOT NULL;