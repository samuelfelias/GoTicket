-- Alteração da tabela IngressoUsuario para adicionar a coluna id_evento
ALTER TABLE IngressoUsuario ADD COLUMN id_evento INT AFTER usuario_id;
ALTER TABLE IngressoUsuario ADD FOREIGN KEY (id_evento) REFERENCES Evento(id_evento);