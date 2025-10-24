-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS sistema_ingressos;

USE sistema_ingressos;

-- Tabela Usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `cpf` varchar(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tipo` enum('ADMIN','CLIENTE','ORGANIZADOR') NOT NULL,
  `senha` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `plano` enum('NORMAL','GOLD') NOT NULL DEFAULT 'NORMAL',
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela Evento
CREATE TABLE IF NOT EXISTS `evento` (
  `id_evento` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text,
  `data` date NOT NULL,
  `horario` time NOT NULL,
  `local` varchar(200) NOT NULL,
  `id_organizador` int NOT NULL,
  `status` enum('ATIVO','CANCELADO','ADIADO') NOT NULL DEFAULT 'ATIVO',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cidade` varchar(100) NOT NULL DEFAULT '',
  `bairro` varchar(100) NOT NULL DEFAULT '',
  `rua` varchar(200) NOT NULL DEFAULT '',
  `numero` varchar(20) NOT NULL DEFAULT '',
  `imagem_url` varchar(255) DEFAULT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evento`),
  KEY `id_organizador` (`id_organizador`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela Ingresso
CREATE TABLE IF NOT EXISTS `ingresso` (
  `id_ingresso` int NOT NULL AUTO_INCREMENT,
  `id_evento` int NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `descricao` text,
  `quantidade_disponivel` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ingresso`),
  KEY `id_evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela Pedido
CREATE TABLE IF NOT EXISTS `pedido` (
  `id_pedido` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_evento` int DEFAULT NULL,
  `data_pedido` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('PENDENTE','CONFIRMADO','CANCELADO') NOT NULL DEFAULT 'PENDENTE',
  `valor_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela ItemPedido
CREATE TABLE IF NOT EXISTS `itempedido` (
  `id_pedido` int NOT NULL,
  `id_ingresso` int NOT NULL,
  `quantidade` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_pedido`,`id_ingresso`),
  KEY `id_ingresso` (`id_ingresso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela Pagamento
CREATE TABLE IF NOT EXISTS `pagamento` (
  `id_pagamento` int NOT NULL AUTO_INCREMENT,
  `id_pedido` int NOT NULL,
  `data_pagamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valor` decimal(10,2) NOT NULL,
  `metodo_pagamento` enum('CARTAO','DEBITO','PIX','BOLETO') NOT NULL,
  `status_pagamento` enum('PENDENTE','APROVADO','REJEITADO') NOT NULL DEFAULT 'PENDENTE',
  PRIMARY KEY (`id_pagamento`),
  KEY `id_pedido` (`id_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela IngressoUsuario
CREATE TABLE IF NOT EXISTS `ingressousuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ingresso_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `id_evento` int DEFAULT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ATIVO','CANCELADO','USADO') COLLATE utf8mb4_unicode_ci DEFAULT 'ATIVO',
  `data_uso` datetime DEFAULT NULL,
  `data_aquisicao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `ingresso_id` (`ingresso_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `id_evento` (`id_evento`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela Alerta
CREATE TABLE IF NOT EXISTS `alerta` (
  `id_alerta` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_evento` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_alerta`),
  UNIQUE KEY `unico_usuario_evento` (`id_usuario`,`id_evento`),
  KEY `id_evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela Avaliacao
CREATE TABLE IF NOT EXISTS `avaliacao` (
  `id_avaliacao` int NOT NULL AUTO_INCREMENT,
  `id_evento` int NOT NULL,
  `id_usuario` int NOT NULL,
  `nota` int NOT NULL,
  `comentario` text,
  `data_avaliacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_avaliacao`),
  UNIQUE KEY `unico_usuario_evento` (`id_usuario`,`id_evento`),
  KEY `id_evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela Notificacao
CREATE TABLE IF NOT EXISTS `notificacao` (
  `id_notificacao` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `mensagem` text NOT NULL,
  `tipo` enum('INFO','SUCESSO','ALERTA','ERRO') NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notificacao`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela PreferenciaUsuario
CREATE TABLE IF NOT EXISTS `preferenciausuario` (
  `id_usuario` int NOT NULL,
  `id_tipo_evento` int NOT NULL,
  `data_adicao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`,`id_tipo_evento`),
  KEY `id_tipo_evento` (`id_tipo_evento`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela RecuperacaoSenha
CREATE TABLE IF NOT EXISTS `recuperacaosenha` (
  `id_recuperacao` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expira_em` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_recuperacao`),
  UNIQUE KEY `token` (`token`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabela RedefinicaoSenha
CREATE TABLE IF NOT EXISTS `redefinicaosenha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT '0',
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `idx_token` (`token`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela TipoEvento
CREATE TABLE IF NOT EXISTS `tipoevento` (
  `id_tipo_evento` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_tipo_evento`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar Foreign Keys
ALTER TABLE `alerta` ADD CONSTRAINT `alerta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`), ADD CONSTRAINT `alerta_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`);
ALTER TABLE `avaliacao` ADD CONSTRAINT `avaliacao_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`), ADD CONSTRAINT `avaliacao_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
ALTER TABLE `evento` ADD CONSTRAINT `evento_ibfk_1` FOREIGN KEY (`id_organizador`) REFERENCES `usuario` (`id_usuario`);
ALTER TABLE `ingresso` ADD CONSTRAINT `ingresso_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`);
ALTER TABLE `itempedido` ADD CONSTRAINT `itempedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE, ADD CONSTRAINT `itempedido_ibfk_2` FOREIGN KEY (`id_ingresso`) REFERENCES `ingresso` (`id_ingresso`);
ALTER TABLE `notificacao` ADD CONSTRAINT `notificacao_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
ALTER TABLE `pagamento` ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE;
ALTER TABLE `pedido` ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`), ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`);
ALTER TABLE `recuperacaosenha` ADD CONSTRAINT `recuperacaosenha_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);