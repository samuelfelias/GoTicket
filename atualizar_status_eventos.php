<?php
// Script para atualizar o status de eventos expirados
require_once 'config/database.php';

try {
    // Conectar ao banco de dados
    $conexao = conectarBD();
    
    echo "<h1>Atualizando status de eventos expirados</h1>";
    
    // Definir o schema para PostgreSQL
    $conexao->exec("SET search_path TO sistema_ingressos");
    
    // Data atual
    $hoje = date('Y-m-d');
    
    // Consulta para encontrar eventos expirados que ainda estão ativos
    $sql = "UPDATE evento 
            SET status = 'FINALIZADO' 
            WHERE status = 'ATIVO' 
            AND (data < ?) 
            RETURNING id_evento, nome, data";
    
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$hoje]);
    
    $eventos_atualizados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_atualizados = count($eventos_atualizados);
    
    if ($total_atualizados > 0) {
        echo "<p style='color: green'>$total_atualizados eventos foram atualizados para FINALIZADO:</p>";
        echo "<ul>";
        foreach ($eventos_atualizados as $evento) {
            echo "<li>ID: {$evento['id_evento']} - {$evento['nome']} (Data: {$evento['data']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nenhum evento expirado encontrado.</p>";
    }
    
    // Adicionar código para executar automaticamente
    echo "<h2>Configuração para execução automática</h2>";
    echo "<p>Para garantir que os eventos sejam atualizados automaticamente, você pode:</p>";
    echo "<ol>";
    echo "<li>Configurar uma tarefa cron para executar este script diariamente</li>";
    echo "<li>Incluir este código no arquivo index.php ou em outro arquivo frequentemente acessado</li>";
    echo "</ol>";
    
    // Código para incluir em outros arquivos
    echo "<h3>Código para incluir em outros arquivos:</h3>";
    echo "<pre>";
    echo htmlspecialchars('<?php
// Atualizar status de eventos expirados
function atualizarStatusEventosExpirados($conexao) {
    try {
        // Definir o schema para PostgreSQL
        $conexao->exec("SET search_path TO sistema_ingressos");
        
        // Data atual
        $hoje = date("Y-m-d");
        
        // Atualizar eventos expirados
        $sql = "UPDATE evento 
                SET status = \'FINALIZADO\' 
                WHERE status = \'ATIVO\' 
                AND (data < ?)";
        
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$hoje]);
        
        return $stmt->rowCount();
    } catch (Exception $e) {
        error_log("Erro ao atualizar status de eventos: " . $e->getMessage());
        return 0;
    }
}

// Executar a função se a conexão estiver disponível
if (isset($conexao)) {
    atualizarStatusEventosExpirados($conexao);
}
?>');
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<p style='color: red'>Erro ao atualizar eventos: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
?>