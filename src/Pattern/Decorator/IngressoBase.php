<?php

namespace GoTicket\Pattern\Decorator;

use GoTicket\Entities\Ingresso;

/**
 * Implementação base do componente Ingresso
 */
class IngressoBase implements IngressoComponent
{
    protected Ingresso $ingresso;
    
    /**
     * Construtor
     * 
     * @param Ingresso $ingresso
     */
    public function __construct(Ingresso $ingresso)
    {
        $this->ingresso = $ingresso;
    }
    
    /**
     * Retorna a descrição do ingresso
     * 
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->ingresso->getTipo();
    }
    
    /**
     * Retorna o preço do ingresso
     * 
     * @return float
     */
    public function getPreco(): float
    {
        return $this->ingresso->getPreco();
    }
}