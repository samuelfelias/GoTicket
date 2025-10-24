<?php

namespace App\Pattern;

use App\Model\Ingresso;

/**
 * Factory Method Pattern - Cria diferentes tipos de ingressos
 */
abstract class IngressoFactory
{
    abstract public function criarIngresso(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase
    ): Ingresso;

    public static function getFactory(string $tipo): IngressoFactory
    {
        return match (strtolower($tipo)) {
            'vip' => new IngressoVIPFactory(),
            'meia' => new IngressoMeiaFactory(),
            'normal' => new IngressoNormalFactory(),
            default => throw new \InvalidArgumentException("Tipo de ingresso inválido: {$tipo}")
        };
    }
}

class IngressoVIPFactory extends IngressoFactory
{
    public function criarIngresso(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase
    ): Ingresso {
        return new \App\Model\IngressoVIP($id, $eventoId, $usuarioId, $precoBase * 2);
    }
}

class IngressoMeiaFactory extends IngressoFactory
{
    public function criarIngresso(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase
    ): Ingresso {
        return new \App\Model\IngressoMeia($id, $eventoId, $usuarioId, $precoBase * 0.5);
    }
}

class IngressoNormalFactory extends IngressoFactory
{
    public function criarIngresso(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase
    ): Ingresso {
        return new \App\Model\IngressoNormal($id, $eventoId, $usuarioId, $precoBase);
    }
}
