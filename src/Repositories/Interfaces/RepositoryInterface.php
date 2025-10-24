<?php

namespace GoTicket\Repositories\Interfaces;

/**
 * Interface base para todos os repositórios
 * Implementa o padrão Repository para separar a lógica de acesso a dados
 */
interface RepositoryInterface
{
    /**
     * Busca todos os registros
     * 
     * @return array
     */
    public function findAll(): array;
    
    /**
     * Busca um registro por ID
     * 
     * @param int $id
     * @return object|null
     */
    public function findById(int $id): ?object;
    
    /**
     * Cria uma nova entidade
     * 
     * @param object $entity
     * @return bool
     */
    public function create(object $entity): bool;
    
    /**
     * Atualiza uma entidade existente
     * 
     * @param object $entity
     * @return bool
     */
    public function update(object $entity): bool;
    
    /**
     * Exclui uma entidade
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}