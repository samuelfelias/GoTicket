<?php
/**
 * Configurações simples de caminhos do sistema GoTicket
 */

// Função para gerar URLs (versão simples)
function url($path = '') {
    // Se estivermos em um subdiretório, detectar automaticamente
    $current_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base = ($current_dir === '/' || $current_dir === '\\') ? '' : $current_dir;
    
    $path = ltrim($path, '/');
    return $base . ($path ? '/' . $path : '');
}

// Função para redirecionar (versão simples)
function redirect($path = '') {
    $url = url($path);
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        echo "<script>window.location.href = '$url';</script>";
    }
    exit;
}

// Função para incluir arquivos (versão simples)
function include_path($file) {
    $file = ltrim($file, '/');
    return __DIR__ . '/../' . $file;
}
?>
