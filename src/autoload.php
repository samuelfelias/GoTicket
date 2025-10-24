<?php

/**
 * Função de autoload para carregar classes automaticamente
 */
spl_autoload_register(function ($className) {
    // Converte o namespace para caminho de arquivo
    $prefix = 'GoTicket\\';
    $baseDir = __DIR__ . '/';
    
    // Verifica se a classe usa o namespace GoTicket
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }
    
    // Obtém o caminho relativo da classe
    $relativeClass = substr($className, $len);
    
    // Substitui os separadores de namespace por separadores de diretório
    // e adiciona .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Se o arquivo existir, carrega-o
    if (file_exists($file)) {
        require $file;
    }
});