<?php
namespace GoTicket\Payment\Strategy;

/**
 * Interface EstrategiaPagamento - Padrão Strategy para diferentes métodos de pagamento
 */
interface EstrategiaPagamento {
    /**
     * Processa um pagamento
     * 
     * @param float $valor Valor a ser pago
     * @param array $dadosPagamento Dados do pagamento
     * @return bool Retorna true se o pagamento foi bem-sucedido
     */
    public function processarPagamento(float $valor, array $dadosPagamento): bool;
}