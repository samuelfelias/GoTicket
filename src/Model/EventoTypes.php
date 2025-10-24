<?php

namespace App\Model;

class Show extends Evento
{
    public function __construct(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ) {
        parent::__construct($id, $nome, $descricao, $data, $local, $capacidade, 'show');
    }

    public function getTipoEvento(): string
    {
        return 'Show Musical';
    }
}

class Palestra extends Evento
{
    public function __construct(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ) {
        parent::__construct($id, $nome, $descricao, $data, $local, $capacidade, 'palestra');
    }

    public function getTipoEvento(): string
    {
        return 'Palestra';
    }
}

class Teatro extends Evento
{
    public function __construct(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ) {
        parent::__construct($id, $nome, $descricao, $data, $local, $capacidade, 'teatro');
    }

    public function getTipoEvento(): string
    {
        return 'Teatro';
    }
}
