<?php

namespace GoTicket\Repository;

use GoTicket\Database\Database;
use GoTicket\Entities\Evento;
use GoTicket\Pattern\EventoFactory;
use PDO;

/**
 * Repository Pattern - Encapsula toda lógica de persistência de Evento
 */
class EventoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(Evento $evento): Evento
    {
        if ($evento->getId() === null) {
            return $this->insert($evento);
        }
        return $this->update($evento);
    }

    private function insert(Evento $evento): Evento
    {
        $sql = "INSERT INTO eventos (nome, descricao, data, local, capacidade, tipo) 
                VALUES (:nome, :descricao, :data, :local, :capacidade, :tipo)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome' => $evento->getNome(),
            'descricao' => $evento->getDescricao(),
            'data' => $evento->getData(),
            'local' => $evento->getLocal(),
            'capacidade' => $evento->getCapacidade(),
            'tipo' => $evento->getTipo()
        ]);

        $evento->setId((int)$this->db->lastInsertId());
        return $evento;
    }

    private function update(Evento $evento): Evento
    {
        $sql = "UPDATE eventos 
                SET nome = :nome, descricao = :descricao, data = :data, 
                    local = :local, capacidade = :capacidade, tipo = :tipo
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $evento->getId(),
            'nome' => $evento->getNome(),
            'descricao' => $evento->getDescricao(),
            'data' => $evento->getData(),
            'local' => $evento->getLocal(),
            'capacidade' => $evento->getCapacidade(),
            'tipo' => $evento->getTipo()
        ]);

        return $evento;
    }

    public function findById(int $id): ?Evento
    {
        $sql = "SELECT * FROM eventos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $data = $stmt->fetch();
        
        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM eventos ORDER BY data DESC";
        $stmt = $this->db->query($sql);
        
        $eventos = [];
        while ($data = $stmt->fetch()) {
            $eventos[] = $this->hydrate($data);
        }

        return $eventos;
    }

    public function findByTipo(string $tipo): array
    {
        $sql = "SELECT * FROM eventos WHERE tipo = :tipo ORDER BY data DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tipo' => $tipo]);
        
        $eventos = [];
        while ($data = $stmt->fetch()) {
            $eventos[] = $this->hydrate($data);
        }

        return $eventos;
    }

    public function findByData(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT * FROM eventos WHERE data BETWEEN :inicio AND :fim ORDER BY data ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'inicio' => $dataInicio,
            'fim' => $dataFim
        ]);
        
        $eventos = [];
        while ($data = $stmt->fetch()) {
            $eventos[] = $this->hydrate($data);
        }

        return $eventos;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM eventos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function hydrate(array $data): Evento
    {
        $factory = EventoFactory::getFactory($data['tipo']);
        
        return $factory->criarEvento(
            (int)$data['id'],
            $data['nome'],
            $data['descricao'],
            $data['data'],
            $data['local'],
            (int)$data['capacidade']
        );
    }
}
