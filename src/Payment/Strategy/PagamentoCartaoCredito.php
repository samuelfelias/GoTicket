<?php
namespace GoTicket\Payment\Strategy;

/**
 * Classe PagamentoCartaoCredito - Implementação concreta do padrão Strategy para pagamento com cartão de crédito
 */
class PagamentoCartaoCredito implements EstrategiaPagamento {
    /**
     * Processa um pagamento com cartão de crédito
     * 
     * @param float $valor Valor a ser pago
     * @param array $dadosPagamento Dados do pagamento (número do cartão, validade, CVV, etc.)
     * @return bool Retorna true se o pagamento foi bem-sucedido
     */
    public function processarPagamento(float $valor, array $dadosPagamento): bool {
        // Validação dos dados do cartão
        if (!$this->validarDadosCartao($dadosPagamento)) {
            return false;
        }
        
        // Simulação de processamento de pagamento com gateway
        // Em um ambiente real, aqui seria feita a integração com um gateway de pagamento
        
        // Simula uma transação bem-sucedida
        return true;
    }
    
    /**
     * Valida os dados do cartão de crédito
     * 
     * @param array $dadosPagamento Dados do cartão
     * @return bool
     */
    private function validarDadosCartao(array $dadosPagamento): bool {
        // Verifica se todos os campos necessários estão presentes
        $camposObrigatorios = ['numero_cartao', 'data_validade', 'cvv', 'nome_titular'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($dadosPagamento[$campo])) {
                return false;
            }
        }
        
        // Validação básica do número do cartão (simplificada)
        $numeroCartao = preg_replace('/\D/', '', $dadosPagamento['numero_cartao']);
        if (strlen($numeroCartao) < 13 || strlen($numeroCartao) > 16) {
            return false;
        }
        
        return true;
    }
}