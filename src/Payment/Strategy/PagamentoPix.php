<?php
namespace GoTicket\Payment\Strategy;

/**
 * Classe PagamentoPix - Implementação concreta do padrão Strategy para pagamento via PIX
 */
class PagamentoPix implements EstrategiaPagamento {
    /**
     * Processa um pagamento via PIX
     * 
     * @param float $valor Valor a ser pago
     * @param array $dadosPagamento Dados do pagamento
     * @return bool Retorna true se o pagamento foi bem-sucedido
     */
    public function processarPagamento(float $valor, array $dadosPagamento): bool {
        // Gera o código PIX
        $codigoPix = $this->gerarCodigoPix($valor, $dadosPagamento);
        
        // Em um ambiente real, aqui seria feita a integração com um PSP (Provedor de Serviços de Pagamento)
        
        // Simula uma transação bem-sucedida
        return !empty($codigoPix);
    }
    
    /**
     * Gera um código PIX para pagamento
     * 
     * @param float $valor Valor do pagamento
     * @param array $dadosPagamento Dados adicionais
     * @return string Código PIX gerado
     */
    private function gerarCodigoPix(float $valor, array $dadosPagamento): string {
        // Simulação de geração de código PIX
        // Em um ambiente real, seria utilizada uma biblioteca específica para gerar o código PIX
        
        $chave = $dadosPagamento['chave_pix'] ?? 'chave-pix-padrao@goticket.com.br';
        $identificador = uniqid('GTKT');
        
        // Formato simplificado para simulação
        return "00020126580014BR.GOV.BCB.PIX0136{$chave}5204000053039865802BR5913GoTicket LTDA6008Sao Paulo62090505{$identificador}6304" . rand(1000, 9999);
    }
}