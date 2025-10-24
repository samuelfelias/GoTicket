<?php
// Script para testar a funcionalidade de exclusão automática de eventos expirados

// Iniciar sessão se ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'includes/funcoes.php';
require_once 'includes/verificar_eventos_expirados.php';

// Conectar ao banco de dados
$conexao = conectarBD();

echo "<h1>Teste de Exclusão Automática de Eventos</h1>";

// Verificar eventos antes da exclusão
echo "<h2>Eventos antes da exclusão:</h2>";
$sql = "SELECT id_evento, nome, data, horario_inicio, horario_encerramento, status FROM evento";
$stmt = $conexao->query($sql);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Data</th>
        <th>Horário Início</th>
        <th>Horário Encerramento</th>
        <th>Status</th>
    </tr>";

foreach ($eventos as $evento) {
    echo "<tr>
        <td>{$evento['id_evento']}</td>
        <td>{$evento['nome']}</td>
        <td>{$evento['data']}</td>
        <td>{$evento['horario_inicio']}</td>
        <td>{$evento['horario_encerramento']}</td>
        <td>{$evento['status']}</td>
    </tr>";
}
echo "</table>";

// Executar a função de exclusão
echo "<h2>Executando exclusão automática...</h2>";
$eventos_deletados = deletarEventosExpirados($conexao);
echo "<p>Eventos deletados: $eventos_deletados</p>";

// Verificar eventos após a exclusão
echo "<h2>Eventos após a exclusão:</h2>";
$sql = "SELECT id_evento, nome, data, horario_inicio, horario_encerramento, status FROM evento";
$stmt = $conexao->query($sql);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Data</th>
        <th>Horário Início</th>
        <th>Horário Encerramento</th>
        <th>Status</th>
    </tr>";

foreach ($eventos as $evento) {
    echo "<tr>
        <td>{$evento['id_evento']}</td>
        <td>{$evento['nome']}</td>
        <td>{$evento['data']}</td>
        <td>{$evento['horario_inicio']}</td>
        <td>{$evento['horario_encerramento']}</td>
        <td>{$evento['status']}</td>
    </tr>";
}
echo "</table>";

// Fechar conexão
$conexao = null;
?>