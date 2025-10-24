<?php

namespace App\Repository;

use App\Database\Database;
use App\Model\Ingresso;
use App\Pattern\IngressoFactory;
use PDO;

/**
 * Repository Pattern - Encapsula toda lógica de persistência de Ingresso
 */
class IngressoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(Ingresso $ingresso): Ingresso
    {
        if ($ingresso->getId() === null) {
            return $this->insert($ingresso);
        }
        return $this->update($ingresso);
    }

    private function insert(Ingresso $ingresso): Ingresso
    {
        $sql = "INSERT INTO ingressos (evento_id, usuario_id, preco_base, tipo, status, codigo_validacao) 
                VALUES (:evento_id, :usuario_id, :preco_base, :tipo, :status, :codigo_validacao)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'evento_id' => $ingresso->getEventoId(),
            'usuario_id' => $ingresso->getUsuarioId(),
            'preco_base' => $ingresso->getPrecoBase(),
            'tipo' => $ingresso->getTipo(),
            'status' => $ingresso->getStatus(),
            'codigo_validacao' => $ingresso->getCodigoValidacao()
        ]);

        $ingresso->setId((int)$this->db->lastInsertId());
        return $ingresso;
    }

    private function update(Ingresso $ingresso): Ingresso
    {
        $sql = "UPDATE ingressos 
                SET evento_id = :evento_id, usuario_id = :usuario_id, preco_base = :preco_base,
                    tipo = :tipo, status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $ingresso->getId(),
            'evento_id' => $ingresso->getEventoId(),
            'usuario_id' => $ingresso->getUsuarioId(),
            'preco_base' => $ingresso->getPrecoBase(),
            'tipo' => $ingresso->getTipo(),
            'status' => $ingresso->getStatus()
        ]);

        return $ingresso;
    }

    public function findById(int $id): ?Ingresso
    {
        $sql = "SELECT * FROM ingressos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findByEvento(int $eventoId): array
    {
        $sql = "SELECT * FROM ingressos WHERE evento_id = :evento_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['evento_id' => $eventoId]);
        
        $ingressos = [];
        while ($data = $stmt->fetch()) {
            $ingressos[] = $this->hydrate($data);
        }

        return $ingressos;
    }

    public function findByUsuario(int $usuarioId): array
    {
        $sql = "SELECT * FROM ingressos WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        
        $ingressos = [];
        while ($data = $stmt->fetch()) {
            $ingressos[] = $this->hydrate($data);
        }

        return $ingressos;
    }

    public function findByTipo(string $tipo): array
    {
        $sql = "SELECT * FROM ingressos WHERE tipo = :tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipo]);
        
        $ingressos = [];
        while ($data = $stmt->fetch()) {
            $ingressos[] = $this->hydrate($data);
        }

        return $ingressos;
    }

    public function findByStatus(string $status): array
    {
        $sql = "SELECT * FROM ingressos WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['status' => $status]);
        
        $ingressos = [];
        while ($data = $stmt->fetch()) {
            $ingressos[] = $this->hydrate($data);
        }

        return $ingressos;
    }

    public function findByCodigoValidacao(string $codigo): ?Ingresso
    {
        $sql = "SELECT * FROM ingressos WHERE codigo_validacao = :codigo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM ingressos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $data): Ingresso
    {
        // Normaliza o tipo para a factory
        $tipoNormalizado = strtolower(str_replace(['Meia-Entrada', ' '], ['meia', ''], $data['tipo']));
        
        $factory = IngressoFactory::getFactory($tipoNormalizado);
        
        $ingresso = $factory->criarIngresso(
            (int)$data['id'],
            (int)$data['evento_id'],
            (int)$data['usuario_id'],
            (float)$data['preco_base']
        );

        $ingresso->setStatus($data['status']);
        
        return $ingresso;
    }
}
