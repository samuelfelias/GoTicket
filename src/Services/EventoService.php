<?php

namespace GoTicket\Services;

use GoTicket\Entities\Evento;
use GoTicket\Repositories\Interfaces\EventoRepositoryInterface;

class EventoService
{
    private EventoRepositoryInterface $eventoRepository;

    public function __construct(EventoRepositoryInterface $eventoRepository)
    {
        $this->eventoRepository = $eventoRepository;
    }

    public function criarEvento(array $dados, int $organizadorId): ?Evento
    {
        // Validações básicas
        if (empty($dados['nome']) || empty($dados['data']) || empty($dados['local'])) {
            throw new \Exception("Campos obrigatórios não preenchidos");
        }

        // Cria o objeto Evento
        $evento = new Evento(
            $dados['nome'],
            $dados['descricao'] ?? '',
            $dados['data'],
            $dados['hora'] ?? '00:00',
            $dados['local'],
            $dados['endereco'] ?? '',
            $dados['cidade'] ?? '',
            $dados['estado'] ?? '',
            $dados['cep'] ?? '',
            (float)($dados['preco'] ?? 0),
            (int)($dados['capacidade'] ?? 100),
            $organizadorId,
            $dados['categoria'] ?? 'Outros',
            $dados['imagem'] ?? ''
        );

        // Salva no banco
        $sucesso = $this->eventoRepository->create($evento);

        if (!$sucesso) {
            throw new \Exception("Erro ao criar evento");
        }

        return $evento;
    }

    public function atualizarEvento(int $id, array $dados, int $organizadorId): ?Evento
    {
        $evento = $this->eventoRepository->findById($id);

        if (!$evento) {
            throw new \Exception("Evento não encontrado");
        }

        // Verifica se o usuário é o organizador do evento
        if ($evento->getOrganizadorId() !== $organizadorId) {
            throw new \Exception("Você não tem permissão para editar este evento");
        }

        // Atualiza os dados do evento
        if (isset($dados['nome'])) {
            $evento->setNome($dados['nome']);
        }

        if (isset($dados['descricao'])) {
            $evento->setDescricao($dados['descricao']);
        }

        if (isset($dados['data'])) {
            $evento->setData($dados['data']);
        }

        if (isset($dados['hora'])) {
            $evento->setHora($dados['hora']);
        }

        if (isset($dados['local'])) {
            $evento->setLocal($dados['local']);
        }

        if (isset($dados['endereco'])) {
            $evento->setEndereco($dados['endereco']);
        }

        if (isset($dados['cidade'])) {
            $evento->setCidade($dados['cidade']);
        }

        if (isset($dados['estado'])) {
            $evento->setEstado($dados['estado']);
        }

        if (isset($dados['cep'])) {
            $evento->setCep($dados['cep']);
        }

        if (isset($dados['preco'])) {
            $evento->setPreco((float)$dados['preco']);
        }

        if (isset($dados['capacidade'])) {
            $evento->setCapacidade((int)$dados['capacidade']);
        }

        if (isset($dados['categoria'])) {
            $evento->setCategoria($dados['categoria']);
        }

        if (isset($dados['imagem']) && !empty($dados['imagem'])) {
            $evento->setImagem($dados['imagem']);
        }

        // Atualiza a data de atualização
        $evento->setDataAtualizacao(new \DateTime());

        // Salva as alterações
        $sucesso = $this->eventoRepository->update($evento);

        if (!$sucesso) {
            throw new \Exception("Erro ao atualizar evento");
        }

        return $evento;
    }

    public function excluirEvento(int $id, int $organizadorId): bool
    {
        $evento = $this->eventoRepository->findById($id);

        if (!$evento) {
            throw new \Exception("Evento não encontrado");
        }

        // Verifica se o usuário é o organizador do evento
        if ($evento->getOrganizadorId() !== $organizadorId) {
            throw new \Exception("Você não tem permissão para excluir este evento");
        }

        return $this->eventoRepository->delete($id);
    }

    public function buscarPorId(int $id): ?Evento
    {
        return $this->eventoRepository->findById($id);
    }

    public function listarTodos(): array
    {
        return $this->eventoRepository->findAll();
    }

    public function listarPorOrganizador(int $organizadorId): array
    {
        return $this->eventoRepository->findByOrganizador($organizadorId);
    }

    public function listarPorCategoria(string $categoria): array
    {
        return $this->eventoRepository->findByCategoria($categoria);
    }

    public function buscarEventos(string $termo): array
    {
        return $this->eventoRepository->buscarEventos($termo);
    }
}