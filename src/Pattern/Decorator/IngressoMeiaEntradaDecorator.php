<?php

namespace GoTicket\Pattern\Decorator;

/**
 * Decorador para ingresso meia-entrada
 */
class IngressoMeiaEntradaDecorator extends IngressoDecorator
{
    /**
     * Retorna a descrição do ingresso
     * 
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->ingressoComponent->getDescricao() . " (Meia-Entrada)";
    }
    
    /**
     * Retorna o preço do ingresso
     * 
     * @return float
     */
    public function getPreco(): float
    {
        // Aplica 50% de desconto no preço base
        return $this->ingressoComponent->getPreco() * 0.5;
    }
}