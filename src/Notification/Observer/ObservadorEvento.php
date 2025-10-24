<?php
namespace GoTicket\Notification\Observer;

/**
 * Interface ObservadorEvento - Define o contrato para observadores de eventos
 */
interface ObservadorEvento {
    /**
     * Método chamado quando um evento ocorre
     * 
     * @param string $tipoEvento Tipo do evento ocorrido
     * @param array $dados Dados relacionados ao evento
     * @return void
     */
    public function atualizar(string $tipoEvento, array $dados): void;
}