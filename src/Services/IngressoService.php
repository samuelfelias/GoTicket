<?php

namespace GoTicket\Services;

use GoTicket\Entities\Ingresso;
use GoTicket\Repositories\Interfaces\IngressoRepositoryInterface;
use GoTicket\Repositories\Interfaces\EventoRepositoryInterface;

class IngressoService
{
    private IngressoRepositoryInterface $ingressoRepository;
    private EventoRepositoryInterface $eventoRepository;

    public function __construct(
        IngressoRepositoryInterface $ingressoRepository,
        EventoRepositoryInterface $eventoRepository
    ) {
        $this->ingressoRepository = $ingressoRepository;
        $this->eventoRepository = $eventoRepository;
    }

    public function gerarIngressos(int $eventoId, int $quantidade): array
    {
        $evento = $this->eventoRepository->findById($eventoId);
        
        if (!$evento) {
            throw new \Exception("Evento não encontrado");
        }
        
        $ingressosGerados = [];
        
        for ($i = 0; $i < $quantidade; $i++) {
            // Gera um código único para o ingresso
            $codigo = $this->gerarCodigoUnico($eventoId);
            
            // Cria o objeto Ingresso
            $ingresso = new Ingresso(
                $eventoId,
                $codigo,
                'disponivel'
            );
            
            // Salva no banco
            $sucesso = $this->ingressoRepository->create($ingresso);
            
            if ($sucesso) {
                $ingressosGerados[] = $ingresso;
            }
        }
        
        return $ingressosGerados;
    }

    public function comprarIngresso(int $eventoId, int $usuarioId): ?Ingresso
    {
        $evento = $this->eventoRepository->findById($eventoId);
        
        if (!$evento) {
            throw new \Exception("Evento não encontrado");
        }
        
        // Verifica se há ingressos disponíveis
        $ingresso = $this->ingressoRepository->comprarIngresso($eventoId, $usuarioId);
        
        if (!$ingresso) {
            throw new \Exception("Não há ingressos disponíveis para este evento");
        }
        
        return $ingresso;
    }

    public function validarIngresso(string $codigo): bool
    {
        $ingresso = $this->ingressoRepository->findByCodigo($codigo);
        
        if (!$ingresso) {
            throw new \Exception("Ingresso não encontrado");
        }
        
        if ($ingresso->getStatus() === 'usado') {
            throw new \Exception("Este ingresso já foi utilizado");
        }
        
        if ($ingresso->getUsuarioId() === null) {
            throw new \Exception("Este ingresso não foi vendido");
        }
        
        return $this->ingressoRepository->validarIngresso($codigo);
    }

    public function listarIngressosPorEvento(int $eventoId): array
    {
        return $this->ingressoRepository->findByEvento($eventoId);
    }

    public function listarIngressosPorUsuario(int $usuarioId): array
    {
        return $this->ingressoRepository->findByUsuario($usuarioId);
    }

    private function gerarCodigoUnico(int $eventoId): string
    {
        $prefix = substr(str_pad($eventoId, 4, '0', STR_PAD_LEFT), 0, 4);
        $unique = uniqid();
        $random = bin2hex(random_bytes(3));
        
        return $prefix . $unique . $random;
    }
}