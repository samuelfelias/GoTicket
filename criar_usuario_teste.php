<?php
// criar_usuario_teste.php

// Incluir conexão com o banco
require_once '../config/database.php';

// Conectar ao banco
$conn = conectarBD();

// Definir a senha em texto puro (o que o usuário vai digitar no login)
$senha_texto_puro = 'senha_padrao'; // <-- A senha que você quer usar

// Gerar o hash seguro da senha (isso que vai salvar no banco)
$senha_hash = password_hash($senha_texto_puro, PASSWORD_DEFAULT);

echo "Senha em texto puro: " . $senha_texto_puro . "\n";
echo "Hash gerado: " . $senha_hash . "\n\n";

try {
    // Inserir o usuário no banco com o hash
    $stmt = $conn->prepare("
        INSERT INTO usuario (cpf, nome, email, tipo, senha, plano)
        VALUES (?, ?, ?, ?, ?, ?)
        RETURNING id_usuario
    ");

    $stmt->execute([
        '12345678901',           // CPF
        'Organizador Teste',      // Nome
        'organizador@teste.com',  // Email
        'ORGANIZADOR',            // Tipo
        $senha_hash,              // <-- AQUI está o hash gerado!
        'NORMAL'                  // Plano
    ]);

    $id_usuario = $stmt->fetchColumn();
    echo "✅ Usuário criado com sucesso! ID: " . $id_usuario . "\n";

} catch (Exception $e) {
    echo "❌ Erro ao criar usuário: " . $e->getMessage() . "\n";
}