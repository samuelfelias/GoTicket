<?php
namespace GoTicket\Notification\Observer;

/**
 * Classe NotificadorEmail - Implementação concreta do padrão Observer para notificações por email
 */
class NotificadorEmail implements ObservadorEvento {
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
                $this->enviarEmailCompraIngresso($dados);
                break;
            case 'evento_criado':
                $this->enviarEmailEventoCriado($dados);
                break;
            case 'evento_alterado':
                $this->enviarEmailEventoAlterado($dados);
                break;
            default:
                // Tipo de evento não suportado para notificação por email
                break;
        }
    }
    
    /**
     * Envia email de confirmação de compra de ingresso
     * 
     * @param array $dados
     * @return void
     */
    private function enviarEmailCompraIngresso(array $dados): void {
        // Simulação de envio de email
        // Em um ambiente real, aqui seria feita a integração com um serviço de email
        
        $destinatario = $dados['email'] ?? 'usuario@exemplo.com';
        $assunto = 'Confirmação de Compra - GoTicket';
        $mensagem = "Olá {$dados['nome']},\n\n";
        $mensagem .= "Sua compra foi confirmada com sucesso!\n";
        $mensagem .= "Evento: {$dados['evento']}\n";
        $mensagem .= "Data: {$dados['data']}\n";
        $mensagem .= "Quantidade: {$dados['quantidade']}\n";
        $mensagem .= "Valor total: R$ {$dados['valor_total']}\n\n";
        $mensagem .= "Obrigado por comprar com a GoTicket!";
        
        // Simula o envio do email
        // mail($destinatario, $assunto, $mensagem);
        
        // Log para debug
        error_log("Email enviado para {$destinatario}: {$assunto}");
    }
    
    /**
     * Envia email de notificação de criação de evento
     * 
     * @param array $dados
     * @return void
     */
    private function enviarEmailEventoCriado(array $dados): void {
        // Simulação de envio de email para administradores
        $destinatario = 'admin@goticket.com.br';
        $assunto = 'Novo Evento Criado - GoTicket';
        $mensagem = "Um novo evento foi criado:\n\n";
        $mensagem .= "Nome: {$dados['nome']}\n";
        $mensagem .= "Data: {$dados['data']}\n";
        $mensagem .= "Local: {$dados['local']}\n";
        $mensagem .= "Capacidade: {$dados['capacidade']}\n";
        
        // Simula o envio do email
        // mail($destinatario, $assunto, $mensagem);
        
        // Log para debug
        error_log("Email enviado para {$destinatario}: {$assunto}");
    }
    
    /**
     * Envia email de notificação de alteração de evento
     * 
     * @param array $dados
     * @return void
     */
    private function enviarEmailEventoAlterado(array $dados): void {
        // Simulação de envio de email para inscritos no evento
        $destinatarios = $dados['inscritos'] ?? [];
        $assunto = 'Evento Atualizado - GoTicket';
        $mensagem = "O evento {$dados['nome']} foi atualizado:\n\n";
        
        if (isset($dados['alteracoes'])) {
            foreach ($dados['alteracoes'] as $campo => $valor) {
                $mensagem .= "{$campo}: {$valor}\n";
            }
        }
        
        // Simula o envio do email para cada inscrito
        foreach ($destinatarios as $destinatario) {
            // mail($destinatario, $assunto, $mensagem);
            
            // Log para debug
            error_log("Email enviado para {$destinatario}: {$assunto}");
        }
    }
}