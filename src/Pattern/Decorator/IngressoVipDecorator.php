<?php

namespace GoTicket\Pattern\Decorator;

/**
 * Decorador para ingresso VIP
 */
class IngressoVipDecorator extends IngressoDecorator
{
    /**
     * Retorna a descrição do ingresso
     * 
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->ingressoComponent->getDescricao() . " (VIP)";
    }
    
    /**
     * Retorna o preço do ingresso
     * 
     * @return float
     */
    public function getPreco(): float
    {
        // Adiciona 50% ao preço base
        return $this->ingressoComponent->getPreco() * 1.5;
    }
}