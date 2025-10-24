<?php

namespace GoTicket\Repositories\Implementations;

use GoTicket\Entities\Usuario;
use GoTicket\Repositories\Interfaces\UsuarioRepositoryInterface;
use PDO;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM usuarios ORDER BY id");
        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = $this->mapRowToEntity($row);
        }
        return $usuarios;
    }

    public function findById(int $id): ?Usuario
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function findByEmail(string $email): ?Usuario
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function findByCpf(string $cpf): ?Usuario
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE cpf = :cpf");
        $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    /**
     * Cria um novo usuário no banco de dados
     * 
     * @param Usuario $usuario
     * @return bool
     */
    public function create(Usuario $usuario): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO usuarios (
                cpf, nome, email, senha, tipo, plano, foto_perfil, created_at, updated_at
            ) VALUES (
                :cpf, :nome, :email, :senha, :tipo, :plano, :foto_perfil, :created_at, :updated_at
            )
        ");

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $stmt->bindValue(':cpf', $usuario->getCpf(), PDO::PARAM_STR);
        $stmt->bindValue(':nome', $usuario->getNome(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $usuario->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':senha', $usuario->getSenha(), PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $usuario->getTipo(), PDO::PARAM_STR);
        $stmt->bindValue(':plano', $usuario->getPlano() ?? 'NORMAL', PDO::PARAM_STR);
        $stmt->bindValue(':foto_perfil', $usuario->getFotoPerfil() ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);

        $result = $stmt->execute();
        if ($result) {
            $usuario->setId((int) $this->conn->lastInsertId());
        }
        return $result;
    }

    public function update(Usuario $usuario): bool
    {
        if (!$usuario->getId()) {
            throw new \InvalidArgumentException('Usuário deve ter um ID para ser atualizado.');
        }

        $stmt = $this->conn->prepare("
            UPDATE usuarios SET 
                cpf = :cpf,
                nome = :nome,
                email = :email,
                senha = :senha,
                tipo = :tipo,
                plano = :plano,
                foto_perfil = :foto_perfil,
                updated_at = :updated_at
            WHERE id = :id
        ");

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':cpf', $usuario->getCpf(), PDO::PARAM_STR);
        $stmt->bindValue(':nome', $usuario->getNome(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $usuario->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':senha', $usuario->getSenha(), PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $usuario->getTipo(), PDO::PARAM_STR);
        $stmt->bindValue(':plano', $usuario->getPlano() ?? 'NORMAL', PDO::PARAM_STR);
        $stmt->bindValue(':foto_perfil', $usuario->getFotoPerfil() ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function autenticar(string $email, string $senha): ?Usuario
    {
        $usuario = $this->findByEmail($email);
        if (!$usuario) {
            return null;
        }

        // Verifica senha com hash (recomendado)
        if (password_verify($senha, $usuario->getSenha())) {
            return $usuario;
        }

        // ⚠️ Remova a linha abaixo em produção!
        // Aceitar senha em texto puro é inseguro.
        // if ($senha === $usuario->getSenha()) {
        //     return $usuario;
        // }

        return null;
    }

    /**
     * Método auxiliar para mapear linha do banco para entidade
     */
    private function mapRowToEntity(array $row): Usuario
    {
        $createdAt = isset($row['created_at']) 
            ? new \DateTime($row['created_at']) 
            : new \DateTime();

        $updatedAt = isset($row['updated_at']) 
            ? new \DateTime($row['updated_at']) 
            : new \DateTime();

        return new Usuario(
            $row['cpf'],
            $row['nome'],
            $row['email'],
            $row['tipo'],
            $row['senha'],
            $row['plano'] ?? 'NORMAL',
            $row['foto_perfil'] ?? null,
            (int) $row['id'],
            $createdAt,
            $updatedAt
        );
    }
}