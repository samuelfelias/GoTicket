<?php
namespace GoTicket\Notification;

use GoTicket\Notification\Observer\ObservadorEvento;

/**
 * Classe GerenciadorEventos - Implementação do Subject no padrão Observer
 * Gerencia os observadores e notifica-os quando eventos ocorrem
 */
class GerenciadorEventos {
    /**
     * @var array Lista de observadores registrados
     */
    private $observadores = [];
    
    /**
     * Registra um observador
     * 
     * @param ObservadorEvento $observador Observador a ser registrado
     * @return void
     */
    public function anexar(ObservadorEvento $observador): void {
        $id = spl_object_hash($observador);
        $this->observadores[$id] = $observador;
    }
    
    /**
     * Remove um observador
     * 
     * @param ObservadorEvento $observador Observador a ser removido
     * @return void
     */
    public function desanexar(ObservadorEvento $observador): void {
        $id = spl_object_hash($observador);
        unset($this->observadores[$id]);
    }
    
    /**
     * Notifica todos os observadores registrados sobre um evento
     * 
     * @param string $tipoEvento Tipo do evento
     * @param array $dadosEvento Dados do evento
     * @return void
     */
    public function notificar(string $tipoEvento, array $dadosEvento): void {
        foreach ($this->observadores as $observador) {
            $observador->atualizar($tipoEvento, $dadosEvento);
        }
    }
}