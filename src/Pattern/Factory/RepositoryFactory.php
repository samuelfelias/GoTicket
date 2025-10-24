<?php

namespace GoTicket\Pattern\Factory;

use App\Database\Database;
use GoTicket\Repositories\Implementations\EventoRepository;
use GoTicket\Repositories\Implementations\IngressoRepository;
use GoTicket\Repositories\Implementations\UsuarioRepository;
use GoTicket\Repositories\Interfaces\EventoRepositoryInterface;
use GoTicket\Repositories\Interfaces\IngressoRepositoryInterface;
use GoTicket\Repositories\Interfaces\UsuarioRepositoryInterface;

/**
 * Factory para criação de repositórios
 * Implementa o padrão Factory para criar instâncias de repositórios
 */
class RepositoryFactory
{
    /**
     * Cria uma instância do repositório de usuários
     * 
     * @return UsuarioRepositoryInterface
     */
    public static function createUsuarioRepository(): UsuarioRepositoryInterface
    {
        $db = Database::getInstance();
        return new UsuarioRepository($db->getConnection());
    }
    
    /**
     * Cria uma instância do repositório de eventos
     * 
     * @return EventoRepositoryInterface
     */
    public static function createEventoRepository(): EventoRepositoryInterface
    {
        $db = Database::getInstance();
        return new EventoRepository($db->getConnection());
    }
    
    /**
     * Cria uma instância do repositório de ingressos
     * 
     * @return IngressoRepositoryInterface
     */
    public static function createIngressoRepository(): IngressoRepositoryInterface
    {
        $db = Database::getInstance();
        return new IngressoRepository($db->getConnection());
    }
}