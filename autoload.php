<?php
/**
 * Autoloader PSR-4 manual para o projeto GoTicket
 * Substitui temporariamente o Composer
 */

spl_autoload_register(function ($class) {
    // Namespace base do projeto
    $prefix = 'App\\';
    
    // Diretório base das classes
    $base_dir = __DIR__ . '/src/';
    
    // Verifica se a classe usa o namespace base
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Remove o namespace base da classe
    $relative_class = substr($class, $len);
    
    // Converte namespace separators para directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Se o arquivo existe, carrega
    if (file_exists($file)) {
        require $file;
    }
});
