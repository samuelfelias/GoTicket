<?php

namespace GoTicket\Pattern\Observer;

use GoTicket\Entities\Evento;

/**
 * Interface para observadores de eventos
 * Implementa o padrão Observer
 */
interface EventoObserver
{
    /**
     * Método chamado quando um evento é criado
     * 
     * @param Evento $evento
     * @return void
     */
    public function onEventoCriado(Evento $evento): void;
    
    /**
     * Método chamado quando um evento é atualizado
     * 
     * @param Evento $evento
     * @return void
     */
    public function onEventoAtualizado(Evento $evento): void;
    
    /**
     * Método chamado quando um evento é cancelado
     * 
     * @param Evento $evento
     * @return void
     */
    public function onEventoCancelado(Evento $evento): void;
}