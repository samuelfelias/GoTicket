<?php
/**
 * Configurações de caminhos do sistema GoTicket
 */

// Detectar se estamos em HTTPS
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

// Detectar o host
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

// Detectar o caminho base
$script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/teste-pit2-ps/index.php';
$base_path = dirname($script_name);

// Limpar o caminho base
if ($base_path === '/' || $base_path === '\\' || $base_path === '.') {
    $base_path = '';
}

// URL base do sistema
$base_url = $protocol . '://' . $host . $base_path;
define('BASE_URL', $base_url);

// Caminho físico base
define('BASE_PATH', dirname(__DIR__));

// Função para gerar URLs absolutas
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

// Função para incluir arquivos com caminho correto
function include_path($file) {
    $file = ltrim($file, '/');
    return BASE_PATH . DIRECTORY_SEPARATOR . $file;
}

// Função para redirecionar com URL correta
function redirect($path = '') {
    $url = url($path);
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        echo "<script>window.location.href = '$url';</script>";
    }
    exit;
}
?>
