<?php

namespace GoTicket\Entities;

class Ingresso
{
    private ?int $id;
    private int $eventoId;
    private ?int $usuarioId;
    private string $codigo;
    private string $status;
    private ?string $dataUso;
    private \DateTime $dataCriacao;
    private \DateTime $dataAtualizacao;

    public function __construct(
        int $eventoId,
        string $codigo,
        string $status = 'disponivel',
        ?int $usuarioId = null,
        ?string $dataUso = null,
        ?int $id = null,
        ?\DateTime $dataCriacao = null,
        ?\DateTime $dataAtualizacao = null
    ) {
        $this->id = $id;
        $this->eventoId = $eventoId;
        $this->usuarioId = $usuarioId;
        $this->codigo = $codigo;
        $this->status = $status;
        $this->dataUso = $dataUso;
        $this->dataCriacao = $dataCriacao ?? new \DateTime();
        $this->dataAtualizacao = $dataAtualizacao ?? new \DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventoId(): int
    {
        return $this->eventoId;
    }

    public function getUsuarioId(): ?int
    {
        return $this->usuarioId;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDataUso(): ?string
    {
        return $this->dataUso;
    }

    public function getDataCriacao(): \DateTime
    {
        return $this->dataCriacao;
    }

    public function getDataAtualizacao(): \DateTime
    {
        return $this->dataAtualizacao;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setEventoId(int $eventoId): void
    {
        $this->eventoId = $eventoId;
    }

    public function setUsuarioId(?int $usuarioId): void
    {
        $this->usuarioId = $usuarioId;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setDataUso(?string $dataUso): void
    {
        $this->dataUso = $dataUso;
    }

    public function setDataAtualizacao(\DateTime $dataAtualizacao): void
    {
        $this->dataAtualizacao = $dataAtualizacao;
    }
}