<?php

namespace GoTicket\Services;

use GoTicket\Entities\Usuario;
use GoTicket\Repositories\Interfaces\UsuarioRepositoryInterface;

class UsuarioService
{
    private UsuarioRepositoryInterface $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function autenticar(string $email, string $senha): ?Usuario
    {
        return $this->usuarioRepository->autenticar($email, $senha);
    }

    public function cadastrar(array $dados): ?Usuario
    {
        // Verifica se já existe um usuário com o mesmo email ou CPF
        if ($this->usuarioRepository->findByEmail($dados['email'])) {
            throw new \Exception("Email já cadastrado");
        }

        if ($this->usuarioRepository->findByCpf($dados['cpf'])) {
            throw new \Exception("CPF já cadastrado");
        }

        // Hash da senha
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        // Cria o objeto Usuario
        $usuario = new Usuario(
            $dados['cpf'],
            $dados['nome'],
            $dados['email'],
            $dados['tipo'],
            $senhaHash,
            $dados['plano'] ?? 'NORMAL',
            $dados['foto_perfil'] ?? null
        );

        // Salva no banco
        $sucesso = $this->usuarioRepository->create($usuario);

        if (!$sucesso) {
            throw new \Exception("Erro ao cadastrar usuário");
        }

        return $usuario;
    }

    public function atualizar(int $id, array $dados): ?Usuario
    {
        $usuario = $this->usuarioRepository->findById($id);

        if (!$usuario) {
            throw new \Exception("Usuário não encontrado");
        }

        // Verifica se o email já está em uso por outro usuário
        if (isset($dados['email']) && $dados['email'] !== $usuario->getEmail()) {
            $usuarioExistente = $this->usuarioRepository->findByEmail($dados['email']);
            if ($usuarioExistente && $usuarioExistente->getId() !== $id) {
                throw new \Exception("Email já está em uso");
            }
            $usuario->setEmail($dados['email']);
        }

        // Atualiza os dados do usuário
        if (isset($dados['nome'])) {
            $usuario->setNome($dados['nome']);
        }

        if (isset($dados['tipo'])) {
            $usuario->setTipo($dados['tipo']);
        }

        if (isset($dados['plano'])) {
            $usuario->setPlano($dados['plano']);
        }

        if (isset($dados['foto_perfil'])) {
            $usuario->setFotoPerfil($dados['foto_perfil']);
        }

        // Se a senha foi fornecida, atualiza com hash
        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            $usuario->setSenha($senhaHash);
        }

        // Salva as alterações
        $sucesso = $this->usuarioRepository->update($usuario);

        if (!$sucesso) {
            throw new \Exception("Erro ao atualizar usuário");
        }

        return $usuario;
    }

    public function excluir(int $id): bool
    {
        $usuario = $this->usuarioRepository->findById($id);

        if (!$usuario) {
            throw new \Exception("Usuário não encontrado");
        }

        return $this->usuarioRepository->delete($id);
    }

    public function buscarPorId(int $id): ?Usuario
    {
        return $this->usuarioRepository->findById($id);
    }

    public function listarTodos(): array
    {
        return $this->usuarioRepository->findAll();
    }
}