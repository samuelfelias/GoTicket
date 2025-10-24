<?php

namespace GoTicket\Pattern\Strategy;

/**
 * Implementação concreta da estratégia de pagamento com cartão
 */
class PagamentoCartaoStrategy implements PagamentoStrategy
{
    /**
     * Processa um pagamento com cartão
     * 
     * @param float $valor
     * @param array $dadosPagamento
     * @return bool
     */
    public function processar(float $valor, array $dadosPagamento): bool
    {
        // Implementação do processamento de pagamento com cartão
        // Aqui seria integrado com um gateway de pagamento real
        
        // Simulação de processamento
        if (!isset($dadosPagamento['numero_cartao']) || !isset($dadosPagamento['cvv'])) {
            return false;
        }
        
        // Gera um ID de transação simulado
        $idTransacao = 'CARD_' . uniqid();
        
        // Armazena o ID da transação para consulta posterior
        $_SESSION['transacao_cartao'][$idTransacao] = [
            'valor' => $valor,
            'status' => 'aprovado',
            'data' => date('Y-m-d H:i:s')
        ];
        
        return true;
    }
    
    /**
     * Verifica se o pagamento foi aprovado
     * 
     * @param string $idTransacao
     * @return bool
     */
    public function verificarAprovacao(string $idTransacao): bool
    {
        // Verifica se a transação existe e está aprovada
        return isset($_SESSION['transacao_cartao'][$idTransacao]) && 
               $_SESSION['transacao_cartao'][$idTransacao]['status'] === 'aprovado';
    }
    
    /**
     * Realiza o estorno de um pagamento
     * 
     * @param string $idTransacao
     * @return bool
     */
    public function estornar(string $idTransacao): bool
    {
        // Verifica se a transação existe
        if (!isset($_SESSION['transacao_cartao'][$idTransacao])) {
            return false;
        }
        
        // Atualiza o status para estornado
        $_SESSION['transacao_cartao'][$idTransacao]['status'] = 'estornado';
        
        return true;
    }
}