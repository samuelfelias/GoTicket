<?php

namespace App\Services;

use PDO;
use PDOException;

/**
 * Serviço de Autenticação
 * Encapsula toda a lógica de negócio relacionada à autenticação
 */
class AuthService
{
    private PDO $conexao;

    public function __construct(PDO $conexao)
    {
        $this->conexao = $conexao;
    }

    /**
     * Realiza login do usuário
     * 
     * @param string $email
     * @param string $senha
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login(string $email, string $senha): array
    {
        // Validar dados
        $validacao = $this->validarCredenciais($email, $senha);
        if (!$validacao['valid']) {
            return [
                'success' => false,
                'message' => $validacao['message'],
                'user' => null
            ];
        }

        try {
            // Verificar se o email existe
            $verificaEmail = $this->conexao->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
            $verificaEmail->execute([$email]);
            $emailExiste = (int)$verificaEmail->fetchColumn() > 0;

            if (!$emailExiste) {
                return [
                    'success' => false,
                    'message' => 'E-mail não cadastrado no sistema',
                    'user' => null
                ];
            }

            // Buscar usuário
            $stmt = $this->conexao->prepare("SELECT id_usuario, nome, email, tipo, senha FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido
                unset($usuario['senha']); // Remover senha do retorno
                return [
                    'success' => true,
                    'message' => 'Login realizado com sucesso',
                    'user' => $usuario
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Senha incorreta',
                    'user' => null
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro de conexão com o servidor. Por favor, tente novamente.',
                'user' => null
            ];
        }
    }

    /**
     * Registra novo usuário
     * 
     * @param array $dados
     * @return array ['success' => bool, 'message' => string]
     */
    public function registrar(array $dados): array
    {
        // Validar dados
        $validacao = $this->validarDadosCadastro($dados);
        if (!$validacao['valid']) {
            return [
                'success' => false,
                'message' => $validacao['message']
            ];
        }

        $nome = trim($dados['nome']);
        $cpf = preg_replace('/[^0-9]/', '', $dados['cpf']);
        $email = trim($dados['email']);
        $senha = $dados['senha'];
        $tipo = $dados['tipo'] ?? 'CLIENTE';

        try {
            // Verificar se CPF já existe
            $stmt = $this->conexao->prepare("SELECT id_usuario FROM usuario WHERE cpf = ?");
            $stmt->execute([$cpf]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'CPF já cadastrado no sistema'
                ];
            }

            // Verificar se email já existe
            $stmt = $this->conexao->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'E-mail já cadastrado no sistema'
                ];
            }

            // Cadastrar usuário
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $this->conexao->prepare(
                "INSERT INTO usuario (nome, cpf, email, tipo, senha, plano) VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            if ($stmt->execute([$nome, $cpf, $email, $tipo, $senhaHash, 'NORMAL'])) {
                return [
                    'success' => true,
                    'message' => 'Cadastro realizado com sucesso! Faça login para continuar.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao cadastrar usuário'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro no cadastro: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar cadastro'
            ];
        }
    }

    /**
     * Valida credenciais de login
     */
    private function validarCredenciais(string $email, string $senha): array
    {
        if (empty($email)) {
            return ['valid' => false, 'message' => 'E-mail é obrigatório'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'E-mail inválido'];
        }

        if (empty($senha)) {
            return ['valid' => false, 'message' => 'Senha é obrigatória'];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Valida dados de cadastro
     */
    private function validarDadosCadastro(array $dados): array
    {
        $erros = [];

        // Validar nome
        if (empty($dados['nome'])) {
            $erros[] = "Nome é obrigatório";
        }

        // Validar CPF
        $cpf = preg_replace('/[^0-9]/', '', $dados['cpf'] ?? '');
        if (empty($cpf)) {
            $erros[] = "CPF é obrigatório";
        } elseif (strlen($cpf) != 11) {
            $erros[] = "CPF deve conter 11 dígitos";
        } elseif (!$this->validarCPF($cpf)) {
            $erros[] = "CPF inválido";
        }

        // Validar email
        if (empty($dados['email'])) {
            $erros[] = "E-mail é obrigatório";
        } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "E-mail inválido";
        }

        // Validar senha
        if (empty($dados['senha'])) {
            $erros[] = "Senha é obrigatória";
        } elseif (strlen($dados['senha']) < 6) {
            $erros[] = "Senha deve ter pelo menos 6 caracteres";
        } elseif ($dados['senha'] != ($dados['confirmar_senha'] ?? '')) {
            $erros[] = "As senhas não coincidem";
        }

        if (!empty($erros)) {
            return ['valid' => false, 'message' => implode("<br>", $erros)];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Valida CPF
     */
    private function validarCPF(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}
