<?php
// Configurações de conexão com o banco de dados PostgreSQL no Supabase
// IMPORTANTE: Substitua pelas suas credenciais do Supabase
define('DB_HOST', 'aws-1-sa-east-1.pooler.supabase.com'); // Host do Supabase
define('DB_USER', 'postgres.tvtafimybhuvvsoxxiap'); // Usuário do PostgreSQL no Supabase
define('DB_PASS', 'Silas12345@2007pit'); // Senha do banco no Supabase
define('DB_NAME', 'postgres'); // Banco padrão no Supabase
define('DB_PORT', '5432'); // Porta padrão do PostgreSQL
define('DB_SSLMODE', 'require'); // Modo SSL para conexão segura

// Função para verificar e criar o schema se não existir (executada apenas uma vez)
function verificarCriarBancoDados() {
    static $schemaVerificado = false;
    
    // Se já foi verificado nesta execução, não verifica novamente
    if ($schemaVerificado) {
        return;
    }
    
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    
    try {
        // Conecta ao PostgreSQL com conexão não persistente para verificação inicial
        $conexao = new PDO($dsn, DB_USER, DB_PASS, array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 3, // Reduzido timeout
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
        
        // Verifica se o schema sistema_ingressos existe
        $stmt = $conexao->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'sistema_ingressos'");
        $schemaExists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schemaExists) {
            // Schema não existe, vamos criá-lo
            $conexao->exec("CREATE SCHEMA IF NOT EXISTS sistema_ingressos");
            error_log("Schema 'sistema_ingressos' criado com sucesso.");
        }
        
        // Fecha a conexão não persistente
        $conexao = null;
        $schemaVerificado = true; // Marca como verificado
        
    } catch (PDOException $e) {
        error_log("Falha na verificação do schema: " . $e->getMessage());
        // Não interrompe a execução
    }
}

// Variável estática para armazenar a conexão persistente
$conexaoPersistente = null;

// Função para conectar ao banco de dados PostgreSQL
function conectarBD() {
    global $conexaoPersistente;
    
    // Verificar se a conexão existente ainda está ativa
    if ($conexaoPersistente !== null) {
        try {
            // Tenta executar uma consulta simples para verificar se a conexão ainda está ativa
            $conexaoPersistente->query('SELECT 1');
            return $conexaoPersistente;
        } catch (PDOException $e) {
            // Conexão não está mais ativa, vamos criar uma nova
            $conexaoPersistente = null;
            error_log("Reconectando ao PostgreSQL: " . $e->getMessage());
        }
    }
    
    // Verifica se a extensão PDO_PGSQL está disponível
    if (!extension_loaded('pdo_pgsql')) {
        die("Erro: A extensão PDO_PGSQL não está instalada ou habilitada no PHP. Por favor, instale a extensão para usar o PostgreSQL.");
    }
    
    // Verifica e cria o schema se necessário (apenas uma vez por execução)
    verificarCriarBancoDados();
    
    // Conecta ao PostgreSQL usando PDO
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    
    try {
        // Cria a conexão PDO com conexão persistente
        $conexao = new PDO($dsn, DB_USER, DB_PASS, array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
        
        // Define o schema como padrão para esta conexão
        $conexao->exec("SET search_path TO sistema_ingressos");
        
        // Configura o charset para UTF8
        $conexao->exec("SET client_encoding TO 'UTF8'");
        
        // Armazena a conexão na variável global para reutilização
        $conexaoPersistente = $conexao;
        
        return $conexao;
    } catch (PDOException $e) {
        // Registra o erro no log
        error_log("Erro de conexão PostgreSQL: " . $e->getMessage());
        
        // Tenta reconectar uma vez em caso de falha
        static $tentativa = 0;
        if ($tentativa < 1) {
            $tentativa++;
            $conexaoPersistente = null; // Limpa a conexão anterior
            return conectarBD(); // Tenta conectar novamente
        }
        
        // Se ainda falhar após a tentativa, exibe mensagem de erro
        $tentativa = 0; // Reseta o contador para futuras chamadas
        throw new PDOException("Falha na conexão ao banco de dados PostgreSQL: " . $e->getMessage());
    }
}
