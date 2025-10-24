<?php

namespace App\Pattern;

use App\Model\Evento;

/**
 * Factory Method Pattern - Cria diferentes tipos de eventos
 */
abstract class EventoFactory
{
    abstract public function criarEvento(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ): Evento;

    public static function getFactory(string $tipo): EventoFactory
    {
        return match (strtolower($tipo)) {
            'show' => new ShowFactory(),
            'palestra' => new PalestraFactory(),
            'teatro' => new TeatroFactory(),
            default => throw new \InvalidArgumentException("Tipo de evento inválido: {$tipo}")
        };
    }
}

class ShowFactory extends EventoFactory
{
    public function criarEvento(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ): Evento {
        return new \App\Model\Show($id, $nome, $descricao, $data, $local, $capacidade);
    }
}

class PalestraFactory extends EventoFactory
{
    public function criarEvento(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ): Evento {
        return new \App\Model\Palestra($id, $nome, $descricao, $data, $local, $capacidade);
    }
}

class TeatroFactory extends EventoFactory
{
    public function criarEvento(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade
    ): Evento {
        return new \App\Model\Teatro($id, $nome, $descricao, $data, $local, $capacidade);
    }
}
