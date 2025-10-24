<?php

namespace GoTicket\Pattern\Decorator;

/**
 * Classe base para decoradores de ingresso
 */
abstract class IngressoDecorator implements IngressoComponent
{
    protected IngressoComponent $ingressoComponent;
    
    /**
     * Construtor
     * 
     * @param IngressoComponent $ingressoComponent
     */
    public function __construct(IngressoComponent $ingressoComponent)
    {
        $this->ingressoComponent = $ingressoComponent;
    }
    
    /**
     * Retorna a descrição do ingresso
     * 
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->ingressoComponent->getDescricao();
    }
    
    /**
     * Retorna o preço do ingresso
     * 
     * @return float
     */
    public function getPreco(): float
    {
        return $this->ingressoComponent->getPreco();
    }
}