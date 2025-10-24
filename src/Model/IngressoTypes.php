<?php

namespace App\Model;

class IngressoVIP extends Ingresso
{
    public function __construct(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase,
        string $status = 'disponivel',
        ?string $codigoValidacao = null
    ) {
        parent::__construct($id, $eventoId, $usuarioId, $precoBase, 'VIP', $status, $codigoValidacao);
    }

    public function getTipoIngresso(): string
    {
        return 'Ingresso VIP - Acesso Premium';
    }
}

class IngressoMeia extends Ingresso
{
    public function __construct(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase,
        string $status = 'disponivel',
        ?string $codigoValidacao = null
    ) {
        parent::__construct($id, $eventoId, $usuarioId, $precoBase, 'Meia-Entrada', $status, $codigoValidacao);
    }

    public function getTipoIngresso(): string
    {
        return 'Meia-Entrada - Estudante/Idoso';
    }
}

class IngressoNormal extends Ingresso
{
    public function __construct(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase,
        string $status = 'disponivel',
        ?string $codigoValidacao = null
    ) {
        parent::__construct($id, $eventoId, $usuarioId, $precoBase, 'Normal', $status, $codigoValidacao);
    }

    public function getTipoIngresso(): string
    {
        return 'Ingresso Normal';
    }
}
