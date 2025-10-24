<?php
// Arquivo para verificar e criar o schema PostgreSQL se necessário

// Exibir erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Verificação e Criação do Schema PostgreSQL</h1>";

// Configurações do banco de dados PostgreSQL
$servername = "aws-1-sa-east-1.pooler.supabase.com"; // Host do Supabase
$username = "postgres.tvtafimybhuvvsoxxiap"; // Usuário do PostgreSQL no Supabase
$password = "Silas123@"; // Senha do banco no Supabase
$dbname = "postgres"; // Banco padrão no Supabase
$port = "5432"; // Porta padrão do PostgreSQL
$schema = "sistema_ingressos"; // Nome do schema que vamos criar
$sslmode = "require"; // Modo SSL para conexão segura

// Conectar ao servidor PostgreSQL
try {
    // Criar string de conexão PDO para PostgreSQL
    $dsn = "pgsql:host=$servername;port=$port;dbname=$dbname;sslmode=$sslmode";
    
    // Conectar usando PDO
    $conn = new PDO($dsn, $username, $password);
    
    // Configurar PDO para lançar exceções em caso de erro
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green'>Conexão ao servidor PostgreSQL bem-sucedida!</p>";
    
    // Verificar se o schema existe
    $stmt = $conn->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$schema'");
    $schemaExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($schemaExists) {
        echo "<p style='color: green'>Schema '$schema' já existe!</p>";
    } else {
        echo "<p style='color: orange'>Schema '$schema' não existe. Tentando criar...</p>";
        
        // Criar o schema
        try {
            $conn->exec("CREATE SCHEMA $schema");
            echo "<p style='color: green'>Schema '$schema' criado com sucesso!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red'>Erro ao criar o schema: " . $e->getMessage() . "</p>";
        }
    }
    
    // Definir o schema como padrão para esta conexão
    $conn->exec("SET search_path TO $schema");
    
    // Verificar se as tabelas existem
    $tabelas = [
        'usuario',
        'evento',
        'ingresso',
        'ingressousuario',
        'pedido',
        'pagamento',
        'notificacao',
        'avaliacao',
        'alerta',
        'tipoevento',
        'preferenciausuario',
        'recuperacaosenha',
        'redefinicaosenha'
    ];
    
    echo "<h2>Verificando tabelas:</h2>";
    echo "<ul>";
    
    $tabelas_faltando = [];
    
    foreach ($tabelas as $tabela) {
        $stmt = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$schema' AND table_name = '$tabela'");
        $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tableExists) {
            echo "<li style='color: green'>Tabela '$tabela' existe</li>";
        } else {
            echo "<li style='color: red'>Tabela '$tabela' NÃO existe</li>";
            $tabelas_faltando[] = $tabela;
        }
    }
    
    echo "</ul>";
    
    // Se faltarem tabelas, sugerir executar o script SQL
    if (!empty($tabelas_faltando)) {
        echo "<h3 style='color: red'>Atenção: Algumas tabelas estão faltando!</h3>";
        echo "<p>Execute o script SQL para criar as tabelas:</p>";
        echo "<a href='executar_sql.php' class='btn btn-primary'>Executar Script SQL</a>";
    } else {
        echo "<h3 style='color: green'>Todas as tabelas necessárias existem!</h3>";
    }
    
    // No PDO não é necessário fechar explicitamente a conexão
    $conn = null;
    
} catch (PDOException $e) {
    echo "<p style='color: red'>{$e->getMessage()}</p>";
}
?>