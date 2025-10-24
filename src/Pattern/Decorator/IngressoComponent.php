<?php

namespace GoTicket\Pattern\Decorator;

/**
 * Interface base para o componente Ingresso no padrão Decorator
 */
interface IngressoComponent
{
    /**
     * Retorna a descrição do ingresso
     * 
     * @return string
     */
    public function getDescricao(): string;
    
    /**
     * Retorna o preço do ingresso
     * 
     * @return float
     */
    public function getPreco(): float;
}