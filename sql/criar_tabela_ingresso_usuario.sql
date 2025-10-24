-- Script para criar a tabela IngressoUsuario
CREATE TABLE IF NOT EXISTS IngressoUsuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingresso_id INT NOT NULL,
    usuario_id INT NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('ATIVO', 'CANCELADO', 'USADO') DEFAULT 'ATIVO',
    data_aquisicao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ingresso_id) REFERENCES Ingresso(id_ingresso),
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id_usuario)
);