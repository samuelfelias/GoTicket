<?php
// Arquivo para atualizar o banco de dados com as novas tabelas e campos

// Configurações do banco de dados PostgreSQL/Supabase
$servername = "db.supabase.co"; // Substitua pelo host do Supabase
$username = "postgres"; // Substitua pelo usuário do Supabase
$password = "sua_senha"; // Substitua pela senha do Supabase
$dbname = "postgres"; // Banco de dados padrão do Supabase
$port = "5432"; // Porta padrão do PostgreSQL
$schema = "sistema_ingressos"; // Schema para o sistema

// Criar conexão PDO com PostgreSQL
try {
    $dsn = "pgsql:host=$servername;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $username, $password);
    
    // Configurar o PDO para lançar exceções em caso de erro
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se o schema existe
    $stmt = $conn->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$schema'");
    if ($stmt->rowCount() == 0) {
        // Criar o schema se não existir
        $conn->exec("CREATE SCHEMA $schema");
        echo "<p style='color: green'>Schema '$schema' criado com sucesso!</p>";
    }
    
    // Definir o schema como padrão para esta conexão
    $conn->exec("SET search_path TO $schema");
    
} catch (PDOException $e) {
    die("Falha na conexão ao banco de dados PostgreSQL: " . $e->getMessage());
}

echo "<h2>Atualizando banco de dados...</h2>";

// Ler o arquivo SQL para tipos de eventos e preferências
$sql_file = file_get_contents('sql/adicionar_tipos_eventos_preferencias.sql');

// Dividir o arquivo em comandos SQL individuais
$commands = explode(';', $sql_file);

// Executar cada comando SQL
foreach ($commands as $command) {
    $command = trim($command);
    if (!empty($command)) {
        try {
            $conn->exec($command);
            echo "<p>Comando executado com sucesso: " . substr($command, 0, 50) . "...</p>";
        } catch (PDOException $e) {
            echo "<p>Erro ao executar comando: " . $e->getMessage() . "</p>";
            echo "<p>Comando: " . $command . "</p>";
        }
    }
}

echo "<h3>Atualização concluída!</h3>";
// No PDO não é necessário fechar explicitamente a conexão
$conn = null;
?>
