<?php

namespace GoTicket\Repositories\Interfaces;

use GoTicket\Entities\Evento;

interface EventoRepositoryInterface extends RepositoryInterface
{
    public function findByOrganizador(int $organizadorId): array;
    public function findByCategoria(string $categoria): array;
    public function buscarEventos(string $termo): array;
    public function create(Evento $evento): bool;
}