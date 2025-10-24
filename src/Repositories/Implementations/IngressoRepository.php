<?php

namespace GoTicket\Repositories\Implementations;

use GoTicket\Entities\Ingresso;
use GoTicket\Repositories\Interfaces\IngressoRepositoryInterface;
use PDO;

class IngressoRepository implements IngressoRepositoryInterface
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM ingressos ORDER BY id DESC");
        $ingressos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ingressos[] = $this->mapRowToEntity($row);
        }
        return $ingressos;
    }

    public function findById(int $id): ?Ingresso
    {
        $stmt = $this->conn->prepare("SELECT * FROM ingressos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function findByEvento(int $eventoId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM ingressos WHERE evento_id = :evento_id ORDER BY id");
        $stmt->bindParam(':evento_id', $eventoId, PDO::PARAM_INT);
        $stmt->execute();
        $ingressos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ingressos[] = $this->mapRowToEntity($row);
        }
        return $ingressos;
    }

    public function findByUsuario(int $usuarioId): array
    {
        $stmt = $this->conn->prepare("
            SELECT i.*, e.nome as evento_nome, e.data as evento_data, e.hora as evento_hora, e.local as evento_local 
            FROM ingressos i
            JOIN eventos e ON i.evento_id = e.id
            WHERE i.usuario_id = :usuario_id
            ORDER BY e.data DESC
        ");
        $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $ingressos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ingressos[] = $this->mapRowToEntity($row);
        }
        return $ingressos;
    }

    public function findByCodigo(string $codigo): ?Ingresso
    {
        $stmt = $this->conn->prepare("SELECT * FROM ingressos WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function create(Ingresso $ingresso): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO ingressos (
                evento_id, usuario_id, codigo, status, data_uso, data_criacao, data_atualizacao
            ) VALUES (
                :evento_id, :usuario_id, :codigo, :status, :data_uso, :data_criacao, :data_atualizacao
            )
        ");

        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $dataUso = $ingresso->getDataUso() ? $ingresso->getDataUso()->format('Y-m-d H:i:s') : null;

        $stmt->bindValue(':evento_id', $ingresso->getEventoId(), PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id', $ingresso->getUsuarioId(), $ingresso->getUsuarioId() ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':codigo', $ingresso->getCodigo(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $ingresso->getStatus() ?? 'disponivel', PDO::PARAM_STR);
        $stmt->bindValue(':data_uso', $dataUso, $dataUso ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':data_criacao', $now, PDO::PARAM_STR);
        $stmt->bindValue(':data_atualizacao', $now, PDO::PARAM_STR);

        $result = $stmt->execute();
        if ($result) {
            $ingresso->setId((int) $this->conn->lastInsertId());
        }
        return $result;
    }

    public function update(Ingresso $ingresso): bool
    {
        if (!$ingresso->getId()) {
            throw new \InvalidArgumentException('Ingresso deve ter um ID para ser atualizado.');
        }

        $stmt = $this->conn->prepare("
            UPDATE ingressos SET 
                evento_id = :evento_id,
                usuario_id = :usuario_id,
                codigo = :codigo,
                status = :status,
                data_uso = :data_uso,
                data_atualizacao = :data_atualizacao
            WHERE id = :id
        ");

        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $dataUso = $ingresso->getDataUso() ? $ingresso->getDataUso()->format('Y-m-d H:i:s') : null;

        $stmt->bindValue(':id', $ingresso->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':evento_id', $ingresso->getEventoId(), PDO::PARAM_INT);
        $stmt->bindValue(':usuario_id', $ingresso->getUsuarioId(), $ingresso->getUsuarioId() ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':codigo', $ingresso->getCodigo(), PDO::PARAM_STR);
        $stmt->bindValue(':status', $ingresso->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':data_uso', $dataUso, $dataUso ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':data_atualizacao', $now, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM ingressos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function validarIngresso(string $codigo): bool
    {
        $ingresso = $this->findByCodigo($codigo);
        if (!$ingresso || $ingresso->getStatus() === 'usado') {
            return false;
        }

        $stmt = $this->conn->prepare("
            UPDATE ingressos 
            SET status = 'usado', data_uso = NOW(), data_atualizacao = NOW() 
            WHERE codigo = :codigo
        ");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function comprarIngresso(int $eventoId, int $usuarioId): ?Ingresso
    {
        // Busca um ingresso disponível (sem usuário e com status 'disponivel')
        $stmt = $this->conn->prepare("
            SELECT id FROM ingressos 
            WHERE evento_id = :evento_id 
              AND usuario_id IS NULL 
              AND status = 'disponivel'
            LIMIT 1
        ");
        $stmt->bindParam(':evento_id', $eventoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $ingressoId = (int) $row['id'];

        // Associa ao usuário
        $stmt = $this->conn->prepare("
            UPDATE ingressos 
            SET usuario_id = :usuario_id, status = 'vendido', data_atualizacao = NOW() 
            WHERE id = :id
        ");
        $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $ingressoId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return null;
        }

        return $this->findById($ingressoId);
    }

    // Método auxiliar para mapear linha do banco para entidade
    private function mapRowToEntity(array $row): Ingresso
    {
        $dataCriacao = isset($row['data_criacao']) 
            ? new \DateTime($row['data_criacao']) 
            : new \DateTime();

        $dataAtualizacao = isset($row['data_atualizacao']) 
            ? new \DateTime($row['data_atualizacao']) 
            : new \DateTime();

        $dataUso = isset($row['data_uso']) && $row['data_uso'] 
            ? new \DateTime($row['data_uso']) 
            : null;

        return new Ingresso(
            (int) $row['evento_id'],
            $row['codigo'],
            $row['status'] ?? 'disponivel',
            isset($row['usuario_id']) ? (int) $row['usuario_id'] : null,
            $dataUso,
            (int) $row['id'],
            $dataCriacao,
            $dataAtualizacao
        );
    }
}