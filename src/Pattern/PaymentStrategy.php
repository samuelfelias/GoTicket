<?php

namespace App\Pattern;

/**
 * Strategy Pattern - Interface para estratégias de pagamento
 */
interface PaymentStrategy
{
    public function processar(float $valor): array;
    public function getNome(): string;
}

/**
 * Estratégia concreta - Pagamento com Cartão de Crédito
 */
class CartaoCreditoStrategy implements PaymentStrategy
{
    private int $parcelas;

    public function __construct(int $parcelas = 1)
    {
        $this->parcelas = $parcelas;
    }

    public function processar(float $valor): array
    {
        $valorParcela = $valor / $this->parcelas;
        
        return [
            'status' => 'aprovado',
            'metodo' => 'Cartão de Crédito',
            'valor_total' => $valor,
            'parcelas' => $this->parcelas,
            'valor_parcela' => $valorParcela,
            'transacao_id' => 'CC-' . uniqid(),
            'mensagem' => "Pagamento aprovado em {$this->parcelas}x de R$ " . 
                         number_format($valorParcela, 2, ',', '.')
        ];
    }

    public function getNome(): string
    {
        return "Cartão de Crédito ({$this->parcelas}x)";
    }
}

/**
 * Estratégia concreta - Pagamento com PIX
 */
class PixStrategy implements PaymentStrategy
{
    public function processar(float $valor): array
    {
        // Aplica 5% de desconto para PIX
        $valorComDesconto = $valor * 0.95;
        
        return [
            'status' => 'aprovado',
            'metodo' => 'PIX',
            'valor_original' => $valor,
            'desconto' => $valor - $valorComDesconto,
            'valor_total' => $valorComDesconto,
            'transacao_id' => 'PIX-' . uniqid(),
            'chave_pix' => '00020126580014br.gov.bcb.pix...',
            'mensagem' => "Pagamento via PIX com 5% de desconto. Total: R$ " . 
                         number_format($valorComDesconto, 2, ',', '.')
        ];
    }

    public function getNome(): string
    {
        return "PIX (5% desconto)";
    }
}

/**
 * Estratégia concreta - Pagamento com Boleto
 */
class BoletoStrategy implements PaymentStrategy
{
    public function processar(float $valor): array
    {
        $dataVencimento = date('d/m/Y', strtotime('+3 days'));
        
        return [
            'status' => 'pendente',
            'metodo' => 'Boleto Bancário',
            'valor_total' => $valor,
            'codigo_barras' => '23793.38128 60000.000001 00000.000000 1 00000000' . str_pad((int)($valor * 100), 8, '0', STR_PAD_LEFT),
            'data_vencimento' => $dataVencimento,
            'transacao_id' => 'BOL-' . uniqid(),
            'mensagem' => "Boleto gerado com vencimento para {$dataVencimento}"
        ];
    }

    public function getNome(): string
    {
        return "Boleto Bancário";
    }
}

/**
 * Estratégia concreta - Pagamento com Carteira Digital (PayPal, PagSeguro, etc.)
 */
class CarteiraDigitalStrategy implements PaymentStrategy
{
    private string $provedor;

    public function __construct(string $provedor = 'PayPal')
    {
        $this->provedor = $provedor;
    }

    public function processar(float $valor): array
    {
        return [
            'status' => 'aprovado',
            'metodo' => "Carteira Digital - {$this->provedor}",
            'valor_total' => $valor,
            'transacao_id' => strtoupper(substr($this->provedor, 0, 3)) . '-' . uniqid(),
            'mensagem' => "Pagamento aprovado via {$this->provedor}"
        ];
    }

    public function getNome(): string
    {
        return "Carteira Digital ({$this->provedor})";
    }
}
