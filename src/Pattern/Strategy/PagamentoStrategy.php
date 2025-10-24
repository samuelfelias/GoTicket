<?php

namespace GoTicket\Pattern\Strategy;

/**
 * Interface para estratégias de pagamento
 * Implementa o padrão Strategy
 */
interface PagamentoStrategy
{
    /**
     * Processa um pagamento
     * 
     * @param float $valor
     * @param array $dadosPagamento
     * @return bool
     */
    public function processar(float $valor, array $dadosPagamento): bool;
    
    /**
     * Verifica se o pagamento foi aprovado
     * 
     * @param string $idTransacao
     * @return bool
     */
    public function verificarAprovacao(string $idTransacao): bool;
    
    /**
     * Realiza o estorno de um pagamento
     * 
     * @param string $idTransacao
     * @return bool
     */
    public function estornar(string $idTransacao): bool;
}