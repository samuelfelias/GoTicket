<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Singleton Pattern - Garante uma única instância de conexão com o banco de dados
 * Suporta múltiplos drivers (MySQL, PostgreSQL)
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private string $driver;

    /**
     * Construtor privado para evitar instanciação direta
     */
    private function __construct()
    {
        // Carrega as configurações do banco de dados
        require_once __DIR__ . '/../../config/database.php';
        
        try {
            // Verifica se existe a função conectarBD (para compatibilidade)
            if (function_exists('conectarBD')) {
                $this->connection = conectarBD();
                $this->driver = 'pgsql'; // Assume PostgreSQL para conectarBD
            } else {
                // Configuração padrão para MySQL
                $this->connection = new PDO(
                    "mysql:host={$db_config['host']};dbname={$db_config['database']};charset=utf8",
                    $db_config['username'],
                    $db_config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
                $this->driver = 'mysql';
            }
        } catch (PDOException $e) {
            throw new \RuntimeException("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne deserialização da instância
     */
    public function __wakeup()
    {
        throw new \Exception("Não é possível deserializar um singleton.");
    }

    /**
     * Método para obter a instância única da classe
     * @return Database
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Obtém a conexão PDO
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    /**
     * Retorna o driver de banco de dados em uso
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
}
