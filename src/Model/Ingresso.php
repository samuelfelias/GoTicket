<?php

namespace App\Model;

/**
 * Leaf do Composite Pattern - representa um ingresso individual
 */
abstract class Ingresso implements IngressoComponent
{
    protected ?int $id;
    protected int $eventoId;
    protected int $usuarioId;
    protected float $precoBase;
    protected string $tipo;
    protected string $status;
    protected string $codigoValidacao;

    public function __construct(
        ?int $id,
        int $eventoId,
        int $usuarioId,
        float $precoBase,
        string $tipo,
        string $status = 'disponivel',
        ?string $codigoValidacao = null
    ) {
        $this->id = $id;
        $this->eventoId = $eventoId;
        $this->usuarioId = $usuarioId;
        $this->precoBase = $precoBase;
        $this->tipo = $tipo;
        $this->status = $status;
        $this->codigoValidacao = $codigoValidacao ?? $this->gerarCodigoValidacao();
    }

    abstract public function getTipoIngresso(): string;

    public function getPreco(): float
    {
        return $this->precoBase;
    }

    public function getDescricao(): string
    {
        return "Ingresso {$this->tipo} - R$ " . number_format($this->precoBase, 2, ',', '.');
    }

    public function getQuantidade(): int
    {
        return 1;
    }

    private function gerarCodigoValidacao(): string
    {
        return strtoupper(bin2hex(random_bytes(8)));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEventoId(): int
    {
        return $this->eventoId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getPrecoBase(): float
    {
        return $this->precoBase;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCodigoValidacao(): string
    {
        return $this->codigoValidacao;
    }
}
