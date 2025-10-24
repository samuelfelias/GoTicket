<?php

namespace App\Pattern;

use App\Model\IngressoComponent;

/**
 * Composite Pattern - Permite tratar múltiplos ingressos como um único componente
 * Útil para pacotes promocionais, combos familiares, etc.
 */
class PacoteIngresso implements IngressoComponent
{
    private string $nome;
    private array $ingressos = [];

    public function __construct(string $nome)
    {
        $this->nome = $nome;
    }

    public function adicionarIngresso(IngressoComponent $ingresso): void
    {
        $this->ingressos[] = $ingresso;
    }

    public function removerIngresso(IngressoComponent $ingresso): void
    {
        $this->ingressos = array_filter(
            $this->ingressos,
            fn($item) => $item !== $ingresso
        );
    }

    public function getIngressos(): array
    {
        return $this->ingressos;
    }

    public function getPreco(): float
    {
        $total = 0;
        foreach ($this->ingressos as $ingresso) {
            $total += $ingresso->getPreco();
        }
        return $total;
    }

    public function getDescricao(): string
    {
        $descricao = "Pacote: {$this->nome}\n";
        foreach ($this->ingressos as $ingresso) {
            $descricao .= "  - " . $ingresso->getDescricao() . "\n";
        }
        $descricao .= "Total: R$ " . number_format($this->getPreco(), 2, ',', '.');
        return $descricao;
    }

    public function getQuantidade(): int
    {
        $total = 0;
        foreach ($this->ingressos as $ingresso) {
            $total += $ingresso->getQuantidade();
        }
        return $total;
    }

    public function getNome(): string
    {
        return $this->nome;
    }
}
