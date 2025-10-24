<?php
namespace GoTicket\Repositories\Factory;

use GoTicket\Database\BancoDados;
use GoTicket\Repositories\Implementations\RepositorioUsuario;
use GoTicket\Repositories\Implementations\RepositorioEvento;
use GoTicket\Repositories\Implementations\RepositorioIngresso;
use GoTicket\Repositories\Interfaces\InterfaceRepositorio;

/**
 * Classe FabricaRepositorio implementando o padrão Factory Method
 * Responsável por criar instâncias de repositórios
 */
class FabricaRepositorio {
    /**
     * Cria e retorna uma instância do repositório solicitado
     * 
     * @param string $tipo Tipo de repositório (usuario, evento, ingresso)
     * @return InterfaceRepositorio
     * @throws \Exception
     */
    public function criarRepositorio(string $tipo): InterfaceRepositorio {
        // Obtém a conexão do singleton BancoDados
        $conexao = BancoDados::obterInstancia()->obterConexao();
        
        // Cria o repositório apropriado com base no tipo
        switch (strtolower($tipo)) {
            case 'usuario':
                return new RepositorioUsuario($conexao);
            case 'evento':
                return new RepositorioEvento($conexao);
            case 'ingresso':
                return new RepositorioIngresso($conexao);
            default:
                throw new \Exception("Tipo de repositório não suportado: $tipo");
        }
    }
}