<?php

namespace App\Repository;

use App\Database\Database;
use App\Model\Usuario;
use PDO;

/**
 * Repository Pattern - Encapsula toda lógica de persistência de Usuario
 */
class UsuarioRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(Usuario $usuario): Usuario
    {
        if ($usuario->getId() === null) {
            return $this->insert($usuario);
        }
        return $this->update($usuario);
    }

    private function insert(Usuario $usuario): Usuario
    {
        $sql = "INSERT INTO usuarios (nome, email, telefone) 
                VALUES (:nome, :email, :telefone)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'telefone' => $usuario->getTelefone()
        ]);

        $usuario->setId((int)$this->db->lastInsertId());
        return $usuario;
    }

    private function update(Usuario $usuario): Usuario
    {
        $sql = "UPDATE usuarios 
                SET nome = :nome, email = :email, telefone = :telefone
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $usuario->getId(),
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'telefone' => $usuario->getTelefone()
        ]);

        return $usuario;
    }

    public function findById(int $id): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findByEmail(string $email): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM usuarios ORDER BY nome ASC";
        $stmt = $this->db->query($sql);
        
        $usuarios = [];
        while ($data = $stmt->fetch()) {
            $usuarios[] = $this->hydrate($data);
        }

        return $usuarios;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $data): Usuario
    {
        return new Usuario(
            (int)$data['id'],
            $data['nome'],
            $data['email'],
            $data['telefone']
        );
    }
}
