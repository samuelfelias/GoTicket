<?php

namespace GoTicket\Entities;

class Evento
{
    private ?int $id;
    private string $nome;
    private string $descricao;
    private string $data;
    private string $hora;
    private string $local;
    private string $endereco;
    private string $cidade;
    private string $estado;
    private string $cep;
    private float $preco;
    private int $capacidade;
    private int $organizadorId;
    private string $imagem;
    private string $categoria;
    private \DateTime $dataCriacao;
    private \DateTime $dataAtualizacao;

    public function __construct(
        string $nome,
        string $descricao,
        string $data,
        string $hora,
        string $local,
        string $endereco,
        string $cidade,
        string $estado,
        string $cep,
        float $preco,
        int $capacidade,
        int $organizadorId,
        string $categoria,
        string $imagem = '',
        ?int $id = null,
        ?\DateTime $dataCriacao = null,
        ?\DateTime $dataAtualizacao = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->data = $data;
        $this->hora = $hora;
        $this->local = $local;
        $this->endereco = $endereco;
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->cep = $cep;
        $this->preco = $preco;
        $this->capacidade = $capacidade;
        $this->organizadorId = $organizadorId;
        $this->categoria = $categoria;
        $this->imagem = $imagem;
        $this->dataCriacao = $dataCriacao ?? new \DateTime();
        $this->dataAtualizacao = $dataAtualizacao ?? new \DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getHora(): string
    {
        return $this->hora;
    }

    public function getLocal(): string
    {
        return $this->local;
    }

    public function getEndereco(): string
    {
        return $this->endereco;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getCep(): string
    {
        return $this->cep;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function getCapacidade(): int
    {
        return $this->capacidade;
    }

    public function getOrganizadorId(): int
    {
        return $this->organizadorId;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function getImagem(): string
    {
        return $this->imagem;
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

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function setHora(string $hora): void
    {
        $this->hora = $hora;
    }

    public function setLocal(string $local): void
    {
        $this->local = $local;
    }

    public function setEndereco(string $endereco): void
    {
        $this->endereco = $endereco;
    }

    public function setCidade(string $cidade): void
    {
        $this->cidade = $cidade;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function setCep(string $cep): void
    {
        $this->cep = $cep;
    }

    public function setPreco(float $preco): void
    {
        $this->preco = $preco;
    }

    public function setCapacidade(int $capacidade): void
    {
        $this->capacidade = $capacidade;
    }

    public function setOrganizadorId(int $organizadorId): void
    {
        $this->organizadorId = $organizadorId;
    }

    public function setCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function setImagem(string $imagem): void
    {
        $this->imagem = $imagem;
    }

    public function setDataAtualizacao(\DateTime $dataAtualizacao): void
    {
        $this->dataAtualizacao = $dataAtualizacao;
    }
}