<?php

namespace GoTicket\Pattern\Strategy;

/**
 * Implementação concreta da estratégia de pagamento com PIX
 */
class PagamentoPixStrategy implements PagamentoStrategy
{
    /**
     * Processa um pagamento com PIX
     * 
     * @param float $valor
     * @param array $dadosPagamento
     * @return bool
     */
    public function processar(float $valor, array $dadosPagamento): bool
    {
        // Implementação do processamento de pagamento com PIX
        // Aqui seria integrado com um gateway de pagamento real
        
        // Gera um ID de transação simulado
        $idTransacao = 'PIX_' . uniqid();
        
        // Armazena o ID da transação para consulta posterior
        $_SESSION['transacao_pix'][$idTransacao] = [
            'valor' => $valor,
            'status' => 'pendente',
            'data' => date('Y-m-d H:i:s'),
            'qrcode' => 'pix-qrcode-' . md5($idTransacao . $valor)
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
        // Verifica se a transação existe
        if (!isset($_SESSION['transacao_pix'][$idTransacao])) {
            return false;
        }
        
        // Simulação de verificação de pagamento
        // Em um cenário real, consultaria a API do banco
        
        // Se o status ainda estiver pendente, simula uma chance de aprovação
        if ($_SESSION['transacao_pix'][$idTransacao]['status'] === 'pendente') {
            // Simula 80% de chance de aprovação após alguns segundos
            if (time() - strtotime($_SESSION['transacao_pix'][$idTransacao]['data']) > 30) {
                $_SESSION['transacao_pix'][$idTransacao]['status'] = (rand(1, 100) <= 80) ? 'aprovado' : 'pendente';
            }
        }
        
        return $_SESSION['transacao_pix'][$idTransacao]['status'] === 'aprovado';
    }
    
    /**
     * Realiza o estorno de um pagamento
     * 
     * @param string $idTransacao
     * @return bool
     */
    public function estornar(string $idTransacao): bool
    {
        // Verifica se a transação existe e está aprovada
        if (!isset($_SESSION['transacao_pix'][$idTransacao]) || 
            $_SESSION['transacao_pix'][$idTransacao]['status'] !== 'aprovado') {
            return false;
        }
        
        // Atualiza o status para estornado
        $_SESSION['transacao_pix'][$idTransacao]['status'] = 'estornado';
        
        return true;
    }
}