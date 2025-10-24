<?php

namespace GoTicket\Repositories\Interfaces;

use GoTicket\Entities\Ingresso;

interface IngressoRepositoryInterface extends RepositoryInterface
{
    public function findByEvento(int $eventoId): array;
    public function findByUsuario(int $usuarioId): array;
    public function findByCodigo(string $codigo): ?Ingresso;
    public function validarIngresso(string $codigo): bool;
    public function comprarIngresso(int $eventoId, int $usuarioId): ?Ingresso;
    public function create(Ingresso $ingresso): bool;
}