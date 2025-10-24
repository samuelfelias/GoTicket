<?php
namespace GoTicket\Payment;

use GoTicket\Pattern\Strategy\PagamentoStrategy;
use GoTicket\Pattern\Strategy\PagamentoCartaoStrategy;
use GoTicket\Pattern\Strategy\PagamentoPixStrategy;

/**
 * Classe ProcessadorPagamento - Contexto para o padrão Strategy de pagamento
 * Implementa o padrão Strategy para processar pagamentos com diferentes métodos
 */
class ProcessadorPagamento {
    /**
     * @var PagamentoStrategy
     */
    private PagamentoStrategy $estrategia;
    
    /**
     * Define a estratégia de pagamento a ser utilizada
     * 
     * @param PagamentoStrategy $estrategia
     */
    public function definirEstrategia(PagamentoStrategy $estrategia): void {
        $this->estrategia = $estrategia;
    }
    
    /**
     * Define a estratégia de pagamento pelo tipo
     * 
     * @param string $metodoPagamento
     * @return void
     * @throws \InvalidArgumentException Se o método de pagamento não for suportado
     */
    public function definirEstrategiaPorTipo(string $metodoPagamento): void {
        switch ($metodoPagamento) {
            case 'cartao':
                $this->estrategia = new PagamentoCartaoStrategy();
                break;
            case 'pix':
                $this->estrategia = new PagamentoPixStrategy();
                break;
            default:
                throw new \InvalidArgumentException("Método de pagamento não suportado: {$metodoPagamento}");
        }
    }
    
    /**
     * Processa o pagamento utilizando a estratégia definida
     * 
     * @param float $valor
     * @param array $dadosPagamento
     * @return bool
     * @throws \Exception Se nenhuma estratégia foi definida
     */
    public function processarPagamento(float $valor, array $dadosPagamento): bool {
        if (!isset($this->estrategia)) {
            throw new \Exception("Nenhuma estratégia de pagamento foi definida");
        }
        
        return $this->estrategia->processar($valor, $dadosPagamento);
    }
    
    /**
     * Verifica se o pagamento foi aprovado
     * 
     * @param string $idTransacao
     * @return bool
     * @throws \Exception Se nenhuma estratégia foi definida
     */
    public function verificarAprovacao(string $idTransacao): bool {
        if (!isset($this->estrategia)) {
            throw new \Exception("Nenhuma estratégia de pagamento foi definida");
        }
        
        return $this->estrategia->verificarAprovacao($idTransacao);
    }
    
    /**
     * Realiza o estorno de um pagamento
     * 
     * @param string $idTransacao
     * @return bool
     * @throws \Exception Se nenhuma estratégia foi definida
     */
    public function estornar(string $idTransacao): bool {
        if (!isset($this->estrategia)) {
            throw new \Exception("Nenhuma estratégia de pagamento foi definida");
        }
        
        return $this->estrategia->estornar($idTransacao);
    }
}