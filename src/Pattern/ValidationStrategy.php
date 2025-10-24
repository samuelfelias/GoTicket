<?php

namespace App\Pattern;

use App\Model\Ingresso;

/**
 * Strategy Pattern - Interface para estratégias de validação de ingressos
 */
interface ValidationStrategy
{
    public function validar(Ingresso $ingresso): array;
}

/**
 * Estratégia concreta - Validação por QR Code
 */
class QRCodeValidationStrategy implements ValidationStrategy
{
    public function validar(Ingresso $ingresso): array
    {
        // Simula validação de QR Code
        $codigo = $ingresso->getCodigoValidacao();
        
        if ($ingresso->getStatus() === 'usado') {
            return [
                'valido' => false,
                'mensagem' => 'Ingresso já foi utilizado',
                'codigo' => $codigo
            ];
        }

        if ($ingresso->getStatus() === 'cancelado') {
            return [
                'valido' => false,
                'mensagem' => 'Ingresso cancelado',
                'codigo' => $codigo
            ];
        }

        return [
            'valido' => true,
            'mensagem' => 'Ingresso válido - Acesso liberado',
            'codigo' => $codigo,
            'tipo' => $ingresso->getTipo(),
            'evento_id' => $ingresso->getEventoId()
        ];
    }
}

/**
 * Estratégia concreta - Validação por Código Numérico
 */
class CodigoNumericoValidationStrategy implements ValidationStrategy
{
    public function validar(Ingresso $ingresso): array
    {
        $codigo = $ingresso->getCodigoValidacao();
        
        // Verifica se o código tem formato válido
        if (strlen($codigo) < 8) {
            return [
                'valido' => false,
                'mensagem' => 'Código de validação inválido',
                'codigo' => $codigo
            ];
        }

        if ($ingresso->getStatus() !== 'ativo') {
            return [
                'valido' => false,
                'mensagem' => 'Status do ingresso não permite acesso',
                'status' => $ingresso->getStatus()
            ];
        }

        return [
            'valido' => true,
            'mensagem' => 'Ingresso validado com sucesso',
            'codigo' => $codigo,
            'metodo' => 'Código Numérico'
        ];
    }
}

/**
 * Estratégia concreta - Validação por Biometria
 */
class BiometriaValidationStrategy implements ValidationStrategy
{
    public function validar(Ingresso $ingresso): array
    {
        // Simula validação biométrica
        $usuario_id = $ingresso->getUsuarioId();
        
        if ($ingresso->getStatus() !== 'ativo') {
            return [
                'valido' => false,
                'mensagem' => 'Ingresso não está ativo para uso',
                'usuario_id' => $usuario_id
            ];
        }

        // Simula verificação biométrica bem-sucedida
        return [
            'valido' => true,
            'mensagem' => 'Biometria verificada - Acesso autorizado',
            'usuario_id' => $usuario_id,
            'metodo' => 'Biometria',
            'tipo_ingresso' => $ingresso->getTipo()
        ];
    }
}
