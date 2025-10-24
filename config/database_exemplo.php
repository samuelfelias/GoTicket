<?php
// EXEMPLO de configuração para o Supabase
// Copie este arquivo para database.php e substitua pelas suas credenciais

// Configurações de conexão com o banco de dados PostgreSQL no Supabase
define('DB_HOST', 'db.xxxxxxxxxxxxx.supabase.co'); // Substitua pelo seu host do Supabase
define('DB_USER', 'postgres'); // Substitua pelo seu usuário
define('DB_PASS', 'SUA_SENHA_AQUI'); // Substitua pela sua senha
define('DB_NAME', 'postgres'); // Mantenha como 'postgres'
define('DB_PORT', '5432'); // Porta padrão do PostgreSQL
define('DB_SSLMODE', 'require'); // Modo SSL para conexão segura

// Função para verificar e criar o schema se não existir
function verificarCriarBancoDados() {
    // No PostgreSQL/Supabase, conectamos diretamente ao banco postgres
    // e trabalhamos com schemas em vez de criar novos bancos de dados
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    
    try {
        // Verifica se a extensão PDO_PGSQL está disponível
        if (!extension_loaded('pdo_pgsql')) {
            die("Erro: A extensão PDO_PGSQL não está instalada ou habilitada no PHP. Por favor, instale a extensão para usar o PostgreSQL.");
        }
        
        // Conecta ao PostgreSQL
        $conexao = new PDO($dsn, DB_USER, DB_PASS);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verifica se o schema sistema_ingressos existe
        $stmt = $conexao->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = 'sistema_ingressos'");
        $schemaExists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schemaExists) {
            // Schema não existe, vamos criá-lo
            $conexao->exec("CREATE SCHEMA IF NOT EXISTS sistema_ingressos");
            
            // Schema criado com sucesso
            error_log("Schema 'sistema_ingressos' criado com sucesso.");
        }
        
        // Define o schema como padrão para esta conexão
        $conexao->exec("SET search_path TO sistema_ingressos");
        
    } catch (PDOException $e) {
        die("Falha na conexão ao servidor PostgreSQL: " . $e->getMessage());
    }
}

// Função para conectar ao banco de dados PostgreSQL
function conectarBD() {
    // Verifica se a extensão PDO_PGSQL está disponível
    if (!extension_loaded('pdo_pgsql')) {
        die("Erro: A extensão PDO_PGSQL não está instalada ou habilitada no PHP. Por favor, instale a extensão para usar o PostgreSQL.");
    }
    
    // Verifica e cria o schema se necessário
    verificarCriarBancoDados();
    
    // Conecta ao PostgreSQL usando PDO
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    
    try {
        // Cria a conexão PDO
        $conexao = new PDO($dsn, DB_USER, DB_PASS);
        
        // Configura o PDO para lançar exceções em caso de erro
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Define o schema como padrão para esta conexão
        $conexao->exec("SET search_path TO sistema_ingressos");
        
        // Configura o charset para UTF8
        $conexao->exec("SET client_encoding TO 'UTF8'");
        
        return $conexao;
    } catch (PDOException $e) {
        die("Falha na conexão ao banco de dados PostgreSQL: " . $e->getMessage());
    }
}
?>
