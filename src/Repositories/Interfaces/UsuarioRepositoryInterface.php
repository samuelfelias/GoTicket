<?php

namespace GoTicket\Repositories\Interfaces;

use GoTicket\Entities\Usuario;

interface UsuarioRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?Usuario;
    public function findByCpf(string $cpf): ?Usuario;
    public function autenticar(string $email, string $senha): ?Usuario;
    public function create(Usuario $usuario): bool;
}