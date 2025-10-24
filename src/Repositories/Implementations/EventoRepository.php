<?php

namespace GoTicket\Repositories\Implementations;

use GoTicket\Entities\Evento;
use GoTicket\Repositories\Interfaces\EventoRepositoryInterface;
use PDO;

class EventoRepository implements EventoRepositoryInterface
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM eventos ORDER BY data DESC");
        $eventos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = $this->mapRowToEntity($row);
        }
        
        return $eventos;
    }

    public function findById(int $id): ?Evento
    {
        $stmt = $this->conn->prepare("SELECT * FROM eventos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function findByOrganizador(int $organizadorId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM eventos WHERE organizador_id = :organizador_id ORDER BY data DESC");
        $stmt->bindParam(':organizador_id', $organizadorId, PDO::PARAM_INT);
        $stmt->execute();
        
        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = $this->mapRowToEntity($row);
        }
        return $eventos;
    }

    public function findByCategoria(string $categoria): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM eventos WHERE categoria = :categoria ORDER BY data DESC");
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        $stmt->execute();
        
        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = $this->mapRowToEntity($row);
        }
        return $eventos;
    }

    public function buscarEventos(string $termo): array
    {
        $termo = "%{$termo}%";
        $stmt = $this->conn->prepare("
            SELECT * FROM eventos 
            WHERE nome LIKE :termo 
               OR descricao LIKE :termo 
               OR local LIKE :termo 
               OR cidade LIKE :termo 
            ORDER BY data DESC
        ");
        $stmt->bindParam(':termo', $termo, PDO::PARAM_STR);
        $stmt->execute();
        
        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = $this->mapRowToEntity($row);
        }
        return $eventos;
    }

    public function create(Evento $evento): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO eventos (
                nome, descricao, data, hora, local, endereco, cidade, estado, 
                cep, preco, capacidade, organizador_id, categoria, imagem, 
                status, data_criacao, data_atualizacao
            ) VALUES (
                :nome, :descricao, :data, :hora, :local, :endereco, :cidade, :estado, 
                :cep, :preco, :capacidade, :organizador_id, :categoria, :imagem, 
                :status, :data_criacao, :data_atualizacao
            )
        ");

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $stmt->bindValue(':nome', $evento->getNome(), PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $evento->getDescricao(), PDO::PARAM_STR);
        $stmt->bindValue(':data', $evento->getData(), PDO::PARAM_STR);
        $stmt->bindValue(':hora', $evento->getHora(), PDO::PARAM_STR);
        $stmt->bindValue(':local', $evento->getLocal(), PDO::PARAM_STR);
        $stmt->bindValue(':endereco', $evento->getEndereco(), PDO::PARAM_STR);
        $stmt->bindValue(':cidade', $evento->getCidade(), PDO::PARAM_STR);
        $stmt->bindValue(':estado', $evento->getEstado(), PDO::PARAM_STR);
        $stmt->bindValue(':cep', $evento->getCep(), PDO::PARAM_STR);
        $stmt->bindValue(':preco', $evento->getPreco(), PDO::PARAM_STR);
        $stmt->bindValue(':capacidade', $evento->getCapacidade(), PDO::PARAM_INT);
        $stmt->bindValue(':organizador_id', $evento->getOrganizadorId(), PDO::PARAM_INT);
        $stmt->bindValue(':categoria', $evento->getCategoria(), PDO::PARAM_STR);
        $stmt->bindValue(':imagem', $evento->getImagem() ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':status', $evento->getStatus() ?? 'ativo', PDO::PARAM_STR);
        $stmt->bindValue(':data_criacao', $now, PDO::PARAM_STR);
        $stmt->bindValue(':data_atualizacao', $now, PDO::PARAM_STR);

        $result = $stmt->execute();
        if ($result) {
            $evento->setId((int) $this->conn->lastInsertId());
        }
        return $result;
    }

    public function update(Evento $evento): bool
    {
        if (!$evento->getId()) {
            throw new \InvalidArgumentException('Evento deve ter um ID para ser atualizado.');
        }

        $stmt = $this->conn->prepare("
            UPDATE eventos SET 
                nome = :nome, 
                descricao = :descricao, 
                data = :data, 
                hora = :hora, 
                local = :local, 
                endereco = :endereco, 
                cidade = :cidade, 
                estado = :estado, 
                cep = :cep, 
                preco = :preco, 
                capacidade = :capacidade, 
                organizador_id = :organizador_id, 
                categoria = :categoria, 
                imagem = :imagem, 
                status = :status,
                data_atualizacao = :data_atualizacao
            WHERE id = :id
        ");

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $stmt->bindValue(':id', $evento->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':nome', $evento->getNome(), PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $evento->getDescricao(), PDO::PARAM_STR);
        $stmt->bindValue(':data', $evento->getData(), PDO::PARAM_STR);
        $stmt->bindValue(':hora', $evento->getHora(), PDO::PARAM_STR);
        $stmt->bindValue(':local', $evento->getLocal(), PDO::PARAM_STR);
        $stmt->bindValue(':endereco', $evento->getEndereco(), PDO::PARAM_STR);
        $stmt->bindValue(':cidade', $evento->getCidade(), PDO::PARAM_STR);
        $stmt->bindValue(':estado', $evento->getEstado(), PDO::PARAM_STR);
        $stmt->bindValue(':cep', $evento->getCep(), PDO::PARAM_STR);
        $stmt->bindValue(':preco', $evento->getPreco(), PDO::PARAM_STR);
        $stmt->bindValue(':capacidade', $evento->getCapacidade(), PDO::PARAM_INT);
        $stmt->bindValue(':organizador_id', $evento->getOrganizadorId(), PDO::PARAM_INT);
        $stmt->bindValue(':categoria', $evento->getCategoria(), PDO::PARAM_STR);
        $stmt->bindValue(':imagem', $evento->getImagem() ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':status', $evento->getStatus() ?? 'ativo', PDO::PARAM_STR);
        $stmt->bindValue(':data_atualizacao', $now, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM eventos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método auxiliar para mapear linha do banco para entidade
    private function mapRowToEntity(array $row): Evento
    {
        $dataCriacao = isset($row['data_criacao']) 
            ? new \DateTime($row['data_criacao']) 
            : new \DateTime();

        $dataAtualizacao = isset($row['data_atualizacao']) 
            ? new \DateTime($row['data_atualizacao']) 
            : new \DateTime();

        return new Evento(
            $row['nome'],
            $row['descricao'],
            $row['data'],
            $row['hora'] ?? null,
            $row['local'],
            $row['endereco'] ?? '',
            $row['cidade'] ?? '',
            $row['estado'] ?? '',
            $row['cep'] ?? '',
            (float) ($row['preco'] ?? 0.0),
            (int) $row['capacidade'],
            (int) $row['organizador_id'],
            $row['categoria'] ?? '',
            $row['imagem'] ?? '',
            (int) $row['id'],
            $dataCriacao,
            $dataAtualizacao
        );
    }
}