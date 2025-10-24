-- Script de otimização de índices para melhorar performance
-- Execute este script no seu banco PostgreSQL/Supabase

-- Índices para tabela evento (consultas mais frequentes)
CREATE INDEX IF NOT EXISTS idx_evento_status ON evento(status);
CREATE INDEX IF NOT EXISTS idx_evento_data ON evento(data);
CREATE INDEX IF NOT EXISTS idx_evento_organizador ON evento(id_organizador);
CREATE INDEX IF NOT EXISTS idx_evento_status_data ON evento(status, data);
CREATE INDEX IF NOT EXISTS idx_evento_cidade ON evento(cidade);
CREATE INDEX IF NOT EXISTS idx_evento_nome ON evento(nome);

-- Índices para tabela usuario
CREATE INDEX IF NOT EXISTS idx_usuario_email ON usuario(email);
CREATE INDEX IF NOT EXISTS idx_usuario_tipo ON usuario(tipo);

-- Índices para tabela ingresso
CREATE INDEX IF NOT EXISTS idx_ingresso_evento ON ingresso(id_evento);
CREATE INDEX IF NOT EXISTS idx_ingresso_status ON ingresso(status);
CREATE INDEX IF NOT EXISTS idx_ingresso_evento_status ON ingresso(id_evento, status);

-- Índices para tabela ingressousuario
CREATE INDEX IF NOT EXISTS idx_ingressousuario_usuario ON ingressousuario(usuario_id);
CREATE INDEX IF NOT EXISTS idx_ingressousuario_ingresso ON ingressousuario(ingresso_id);
CREATE INDEX IF NOT EXISTS idx_ingressousuario_status ON ingressousuario(status);

-- Índices para tabela pedido
CREATE INDEX IF NOT EXISTS idx_pedido_usuario ON pedido(id_usuario);
CREATE INDEX IF NOT EXISTS idx_pedido_data ON pedido(data_pedido);
CREATE INDEX IF NOT EXISTS idx_pedido_status ON pedido(status);

-- Índices para tabela itempedido
CREATE INDEX IF NOT EXISTS idx_itempedido_pedido ON itempedido(id_pedido);
CREATE INDEX IF NOT EXISTS idx_itempedido_ingresso ON itempedido(id_ingresso);

-- Índices compostos para consultas complexas
CREATE INDEX IF NOT EXISTS idx_evento_organizador_status ON evento(id_organizador, status);
CREATE INDEX IF NOT EXISTS idx_ingressousuario_usuario_status ON ingressousuario(usuario_id, status);

-- Estatísticas para otimizador de consultas
ANALYZE evento;
ANALYZE usuario;
ANALYZE ingresso;
ANALYZE ingressousuario;
ANALYZE pedido;
ANALYZE itempedido;
