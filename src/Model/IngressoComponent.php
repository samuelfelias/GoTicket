<?php

namespace App\Model;

/**
 * Interface para Composite Pattern - permite tratar ingressos individuais e pacotes uniformemente
 */
interface IngressoComponent
{
    public function getPreco(): float;
    public function getDescricao(): string;
    public function getQuantidade(): int;
}
