<?php
// Função para atualizar automaticamente o status de eventos expirados
function atualizarEventosExpirados($conexao) {
    try {
        // Definir o schema para PostgreSQL
        $conexao->exec("SET search_path TO sistema_ingressos");
        
        // Data e hora atual
        $hoje = date('Y-m-d');
        $agora = date('H:i:s');
        
        // Atualizar eventos expirados que ainda estão ativos (data passada)
        $sql = "UPDATE evento 
                SET status = 'FINALIZADO' 
                WHERE status = 'ATIVO' 
                AND data < ?";
        
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$hoje]);
        
        // Atualizar eventos expirados que ainda estão ativos (mesma data, horário de encerramento passado)
        $sql2 = "UPDATE evento 
                SET status = 'FINALIZADO' 
                WHERE status = 'ATIVO' 
                AND data = ? 
                AND horario_encerramento < ?";
        
        $stmt2 = $conexao->prepare($sql2);
        $stmt2->execute([$hoje, $agora]);
        
        $eventos_atualizados = $stmt->rowCount() + $stmt2->rowCount();
        if ($eventos_atualizados > 0) {
            error_log("$eventos_atualizados eventos foram atualizados para FINALIZADO");
        }
        
        return $eventos_atualizados;
    } catch (Exception $e) {
        error_log("Erro ao atualizar status de eventos expirados: " . $e->getMessage());
        return 0;
    }
}

// Função para deletar automaticamente eventos que já passaram do horário de encerramento
function deletarEventosExpirados($conexao) {
    try {
        // Definir o schema para PostgreSQL
        $conexao->exec("SET search_path TO sistema_ingressos");
        
        // Data e hora atual
        $hoje = date('Y-m-d');
        $agora = date('H:i:s');
        
        // Primeiro, obter IDs dos eventos que serão excluídos para registrar
        $sqlSelect = "SELECT id_evento, nome FROM evento 
                      WHERE (data < ? OR (data = ? AND horario_encerramento < ?))";
        
        $stmtSelect = $conexao->prepare($sqlSelect);
        $stmtSelect->execute([$hoje, $hoje, $agora]);
        $eventosParaDeletar = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);
        
        // Deletar eventos expirados (data passada ou mesma data com horário de encerramento passado)
        $sql = "DELETE FROM evento 
                WHERE (data < ? OR (data = ? AND horario_encerramento < ?))";
        
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$hoje, $hoje, $agora]);
        
        $eventos_deletados = $stmt->rowCount();
        if ($eventos_deletados > 0) {
            error_log("$eventos_deletados eventos foram deletados automaticamente:");
            foreach ($eventosParaDeletar as $evento) {
                error_log("ID: {$evento['id_evento']} - Nome: {$evento['nome']}");
            }
        }
        
        return $eventos_deletados;
    } catch (Exception $e) {
        error_log("Erro ao deletar eventos expirados: " . $e->getMessage());
        return 0;
    }
}
?>