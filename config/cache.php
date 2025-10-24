<?php
/**
 * Sistema de cache simples para otimizar performance
 * Cache baseado em arquivos temporários
 */

class SimpleCache {
    private static $cacheDir = 'cache/';
    private static $defaultTTL = 300; // 5 minutos
    
    /**
     * Inicializa o diretório de cache
     */
    private static function initCacheDir() {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }
    
    /**
     * Armazena um valor no cache
     */
    public static function set($key, $value, $ttl = null) {
        self::initCacheDir();
        
        if ($ttl === null) {
            $ttl = self::$defaultTTL;
        }
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        $file = self::$cacheDir . md5($key) . '.cache';
        file_put_contents($file, serialize($data));
    }
    
    /**
     * Recupera um valor do cache
     */
    public static function get($key) {
        self::initCacheDir();
        
        $file = self::$cacheDir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }
    
    /**
     * Remove um item do cache
     */
    public static function delete($key) {
        self::initCacheDir();
        
        $file = self::$cacheDir . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Limpa todo o cache
     */
    public static function clear() {
        self::initCacheDir();
        
        $files = glob(self::$cacheDir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
    
    /**
     * Verifica se uma chave existe no cache
     */
    public static function has($key) {
        return self::get($key) !== null;
    }
}

/**
 * Função helper para cache de consultas de banco
 */
function cacheQuery($key, $callback, $ttl = 300) {
    $cached = SimpleCache::get($key);
    
    if ($cached !== null) {
        return $cached;
    }
    
    $result = $callback();
    SimpleCache::set($key, $result, $ttl);
    
    return $result;
}

/**
 * Função para invalidar cache relacionado a eventos
 */
function invalidateEventCache() {
    $patterns = [
        'eventos_lista_*',
        'eventos_destaque_*',
        'eventos_organizador_*'
    ];
    
    foreach ($patterns as $pattern) {
        $files = glob('cache/' . str_replace('*', '*', md5($pattern)) . '.cache');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
?>
