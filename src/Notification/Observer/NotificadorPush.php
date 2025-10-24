<?php
namespace GoTicket\Notification\Observer;

/**
 * Classe NotificadorPush - Implementação concreta do padrão Observer para notificações push
 */
class NotificadorPush implements ObservadorEvento {
    /**
     * Atualiza o observador quando um evento ocorre
     * 
     * @param string $tipoEvento Tipo do evento ocorrido
     * @param array $dados Dados relacionados ao evento
     * @return void
     */
    public function atualizar(string $tipoEvento, array $dados): void {
        switch ($tipoEvento) {
            case 'compra_ingresso':
                $this->enviarNotificacaoCompra($dados);
                break;
            case 'evento_proximo':
                $this->enviarNotificacaoEventoProximo($dados);
                break;
            default:
                // Tipo de evento não suportado para notificação push
                break;
        }
    }
    
    /**
     * Envia notificação push de confirmação de compra
     * 
     * @param array $dados
     * @return void
     */
    private function enviarNotificacaoCompra(array $dados): void {
        // Simulação de envio de notificação push
        // Em um ambiente real, aqui seria feita a integração com um serviço de push
        
        $tokenDispositivo = $dados['token_dispositivo'] ?? '';
        if (empty($tokenDispositivo)) {
            return;
        }
        
        $titulo = 'Compra Confirmada!';
        $mensagem = "Sua compra para {$dados['evento']} foi confirmada.";
        $dadosAdicionais = [
            'evento_id' => $dados['evento_id'] ?? '',
            'ingresso_id' => $dados['ingresso_id'] ?? '',
            'tipo_notificacao' => 'compra'
        ];
        
        // Simula o envio da notificação push
        $this->enviarPush($tokenDispositivo, $titulo, $mensagem, $dadosAdicionais);
    }
    
    /**
     * Envia notificação push de evento próximo
     * 
     * @param array $dados
     * @return void
     */
    private function enviarNotificacaoEventoProximo(array $dados): void {
        // Simulação de envio de notificação push para usuários com ingressos
        $tokensDispositivos = $dados['tokens_dispositivos'] ?? [];
        
        if (empty($tokensDispositivos)) {
            return;
        }
        
        $titulo = 'Evento se Aproximando!';
        $mensagem = "O evento {$dados['nome']} acontecerá em {$dados['dias_restantes']} dias.";
        $dadosAdicionais = [
            'evento_id' => $dados['evento_id'] ?? '',
            'data_evento' => $dados['data'] ?? '',
            'local' => $dados['local'] ?? '',
            'tipo_notificacao' => 'lembrete'
        ];
        
        // Simula o envio da notificação push para cada dispositivo
        foreach ($tokensDispositivos as $tokenDispositivo) {
            $this->enviarPush($tokenDispositivo, $titulo, $mensagem, $dadosAdicionais);
        }
    }
    
    /**
     * Método auxiliar para enviar notificação push
     * 
     * @param string $tokenDispositivo
     * @param string $titulo
     * @param string $mensagem
     * @param array $dadosAdicionais
     * @return void
     */
    private function enviarPush(string $tokenDispositivo, string $titulo, string $mensagem, array $dadosAdicionais = []): void {
        // Simulação de envio de notificação push
        // Em um ambiente real, aqui seria feita a integração com FCM, OneSignal, etc.
        
        // Log para debug
        error_log("Notificação push enviada para {$tokenDispositivo}: {$titulo} - {$mensagem}");
    }
}