<?php

namespace GoTicket\Entities;

class Usuario
{
    private ?int $id;
    private string $cpf;
    private string $nome;
    private string $email;
    private string $tipo;
    private string $senha;
    private string $plano;
    private ?string $fotoPerfil;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        string $cpf,
        string $nome,
        string $email,
        string $tipo,
        string $senha,
        string $plano = 'NORMAL',
        ?string $fotoPerfil = null,
        ?int $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->cpf = $cpf;
        $this->nome = $nome;
        $this->email = $email;
        $this->tipo = $tipo;
        $this->senha = $senha;
        $this->plano = $plano;
        $this->fotoPerfil = $fotoPerfil;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt ?? new \DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getPlano(): string
    {
        return $this->plano;
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
    }

    public function setPlano(string $plano): void
    {
        $this->plano = $plano;
    }

    public function setFotoPerfil(?string $fotoPerfil): void
    {
        $this->fotoPerfil = $fotoPerfil;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Métodos de negócio
    public function isAdmin(): bool
    {
        return $this->tipo === 'ADMIN';
    }

    public function isOrganizador(): bool
    {
        return $this->tipo === 'ORGANIZADOR';
    }

    public function isCliente(): bool
    {
        return $this->tipo === 'CLIENTE';
    }

    public function isGold(): bool
    {
        return $this->plano === 'GOLD';
    }

    public function verificarSenha(string $senha): bool
    {
        return password_verify($senha, $this->senha);
    }

    public function hashSenha(string $senha): string
    {
        return password_hash($senha, PASSWORD_DEFAULT);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cpf' => $this->cpf,
            'nome' => $this->nome,
            'email' => $this->email,
            'tipo' => $this->tipo,
            'plano' => $this->plano,
            'foto_perfil' => $this->fotoPerfil,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cpf'],
            $data['nome'],
            $data['email'],
            $data['tipo'],
            $data['senha'],
            $data['plano'] ?? 'NORMAL',
            $data['foto_perfil'] ?? null,
            $data['id'] ?? null,
            isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            isset($data['updated_at']) ? new \DateTime($data['updated_at']) : null
        );
    }
}

