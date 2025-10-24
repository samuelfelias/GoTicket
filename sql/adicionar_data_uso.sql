-- Adicionar coluna data_uso à tabela IngressoUsuario
ALTER TABLE IngressoUsuario ADD COLUMN data_uso DATETIME NULL AFTER data_aquisicao;