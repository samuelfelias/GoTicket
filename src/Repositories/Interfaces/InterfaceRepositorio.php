<?php
namespace GoTicket\Repositories\Interfaces;

/**
 * Interface para todos os repositórios
 */
interface InterfaceRepositorio {
    /**
     * Buscar todos os registros
     * 
     * @return array
     */
    public function buscarTodos(): array;
    
    /**
     * Buscar um registro por ID
     * 
     * @param int $id
     * @return object|null
     */
    public function buscarPorId(int $id);
    
    /**
     * Criar um novo registro
     * 
     * @param array $dados
     * @return int ID do registro criado
     */
    public function criar(array $dados): int;
    
    /**
     * Atualizar um registro
     * 
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar(int $id, array $dados): bool;
    
    /**
     * Excluir um registro
     * 
     * @param int $id
     * @return bool
     */
    public function excluir(int $id): bool;
}