<?php

namespace App\Model;

abstract class Evento
{
    protected ?int $id;
    protected string $nome;
    protected string $descricao;
    protected string $data;
    protected string $local;
    protected int $capacidade;
    protected string $tipo;

    public function __construct(
        ?int $id,
        string $nome,
        string $descricao,
        string $data,
        string $local,
        int $capacidade,
        string $tipo
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->data = $data;
        $this->local = $local;
        $this->capacidade = $capacidade;
        $this->tipo = $tipo;
    }

    abstract public function getTipoEvento(): string;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getLocal(): string
    {
        return $this->local;
    }

    public function getCapacidade(): int
    {
        return $this->capacidade;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }
}
