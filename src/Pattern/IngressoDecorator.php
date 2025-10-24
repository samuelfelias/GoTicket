<?php

namespace App\Pattern;

use App\Model\IngressoComponent;

/**
 * Decorator Pattern - Base para decoradores de ingresso
 * Permite aplicar descontos e promoções cumulativas sem alterar o objeto base
 */
abstract class IngressoDecorator implements IngressoComponent
{
    protected IngressoComponent $ingresso;

    public function __construct(IngressoComponent $ingresso)
    {
        $this->ingresso = $ingresso;
    }

    public function getPreco(): float
    {
        return $this->ingresso->getPreco();
    }

    public function getDescricao(): string
    {
        return $this->ingresso->getDescricao();
    }

    public function getQuantidade(): int
    {
        return $this->ingresso->getQuantidade();
    }
}

/**
 * Decorator concreto - Desconto percentual
 */
class DescontoPercentual extends IngressoDecorator
{
    private float $percentual;
    private string $motivo;

    public function __construct(IngressoComponent $ingresso, float $percentual, string $motivo = "Promoção")
    {
        parent::__construct($ingresso);
        $this->percentual = $percentual;
        $this->motivo = $motivo;
    }

    public function getPreco(): float
    {
        return $this->ingresso->getPreco() * (1 - $this->percentual / 100);
    }

    public function getDescricao(): string
    {
        $desconto = number_format($this->percentual, 0);
        return $this->ingresso->getDescricao() . "\n  Desconto {$this->motivo}: -{$desconto}%";
    }
}

/**
 * Decorator concreto - Desconto fixo em reais
 */
class DescontoFixo extends IngressoDecorator
{
    private float $valorDesconto;
    private string $motivo;

    public function __construct(IngressoComponent $ingresso, float $valorDesconto, string $motivo = "Cupom")
    {
        parent::__construct($ingresso);
        $this->valorDesconto = $valorDesconto;
        $this->motivo = $motivo;
    }

    public function getPreco(): float
    {
        $precoComDesconto = $this->ingresso->getPreco() - $this->valorDesconto;
        return max($precoComDesconto, 0); // Não permite preço negativo
    }

    public function getDescricao(): string
    {
        $desconto = number_format($this->valorDesconto, 2, ',', '.');
        return $this->ingresso->getDescricao() . "\n  {$this->motivo}: -R$ {$desconto}";
    }
}

/**
 * Decorator concreto - Promoção de Black Friday
 */
class PromocaoBlackFriday extends IngressoDecorator
{
    public function getPreco(): float
    {
        return $this->ingresso->getPreco() * 0.5; // 50% de desconto
    }

    public function getDescricao(): string
    {
        return $this->ingresso->getDescricao() . "\n  🎉 BLACK FRIDAY: -50%";
    }
}

/**
 * Decorator concreto - Desconto para grupos
 */
class DescontoGrupo extends IngressoDecorator
{
    private int $quantidade;

    public function __construct(IngressoComponent $ingresso, int $quantidade)
    {
        parent::__construct($ingresso);
        $this->quantidade = $quantidade;
    }

    public function getPreco(): float
    {
        // 10% de desconto para grupos de 5+ pessoas
        if ($this->quantidade >= 5) {
            return $this->ingresso->getPreco() * 0.9;
        }
        return $this->ingresso->getPreco();
    }

    public function getDescricao(): string
    {
        if ($this->quantidade >= 5) {
            return $this->ingresso->getDescricao() . "\n  Desconto Grupo ({$this->quantidade} pessoas): -10%";
        }
        return $this->ingresso->getDescricao();
    }
}
