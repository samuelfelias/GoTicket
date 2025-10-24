<?php
// Arquivo para executar o script SQL e criar as tabelas necessárias no PostgreSQL

// Exibir erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Aumentar limite de tempo de execução para scripts grandes
set_time_limit(300); // 5 minutos

// Aumentar limite de memória
ini_set('memory_limit', '256M');

echo "<h1>Executando Script SQL no PostgreSQL</h1>";

// Configurações do banco de dados PostgreSQL
$servername = "aws-1-sa-east-1.pooler.supabase.com"; // Host do Supabase
$username = "postgres.tvtafimybhuvvsoxxiap"; // Usuário do PostgreSQL no Supabase
$password = "Silas123@"; // Senha do banco no Supabase
$dbname = "postgres"; // Banco padrão no Supabase
$port = "5432"; // Porta padrão do PostgreSQL
$schema = "sistema_ingressos"; // Nome do schema que vamos usar
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
    
    // Verificar se o schema existe, se não, criar
    $stmt = $conn->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$schema'");
    $schemaExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$schemaExists) {
        echo "<p style='color: orange'>Schema '$schema' não existe. Criando...</p>";
        
        // Criar o schema
        try {
            $conn->exec("CREATE SCHEMA $schema");
            echo "<p style='color: green'>Schema '$schema' criado com sucesso!</p>";
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar o schema: " . $e->getMessage());
        }
    } else {
        echo "<p style='color: green'>Schema '$schema' já existe!</p>";
    }
    
    // Definir o schema como padrão para esta conexão
    $conn->exec("SET search_path TO $schema");
    
    // Verificar se existem tabelas no schema
    $stmt = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '$schema'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tableCount = $result['count'];
    
    if ($tableCount > 0) {
        echo "<p style='color: orange'>O schema já contém $tableCount tabelas.</p>";
        echo "<form method='post' action=''>
                <p>Deseja recriar todas as tabelas? (Isso irá apagar todos os dados existentes)</p>
                <input type='hidden' name='recreate_tables' value='1'>
                <button type='submit' style='background-color: #dc3545; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;'>Recriar Todas as Tabelas</button>
              </form>";
        
        // Se o usuário não confirmou a recriação, parar aqui
        if (!isset($_POST['recreate_tables'])) {
            echo "<p><a href='diagnostico.php'>Voltar para o Diagnóstico</a></p>";
            exit;
        }
        
        echo "<p style='color: orange'>Recriando todas as tabelas...</p>";
    }
    
    // Ler o arquivo SQL para PostgreSQL
    $sql_file = file_get_contents('sql/sistema_ingressos_postgres.sql');
    
    if ($sql_file === false) {
        throw new Exception("Não foi possível ler o arquivo SQL");
    }
    
    echo "<p>Arquivo SQL lido com sucesso. Executando comandos...</p>";
    
    // Dividir o arquivo em comandos SQL individuais
    $commands = explode(';', $sql_file);
    
    // Executar cada comando SQL
    $success_count = 0;
    $error_count = 0;
    
    echo "<h2>Resultados da execução:</h2>";
    echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;'>";
    
    // Desativar verificação de chaves estrangeiras temporariamente para evitar erros de dependência
    $conn->exec("SET session_replication_role = 'replica'"); // Equivalente ao FOREIGN_KEY_CHECKS=0 no PostgreSQL
    
    foreach ($commands as $command) {
        $command = trim($command);
        if (!empty($command)) {
            // Ignorar comentários e comandos USE
            if (strpos($command, '--') === 0 || strpos($command, 'USE ') === 0) {
                echo "<p style='color: gray'>Ignorando: " . htmlspecialchars(substr($command, 0, 50)) . "...</p>";
                continue;
            }
            
            // Executar o comando
            try {
                $conn->exec($command);
                echo "<p style='color: green'>Sucesso: " . htmlspecialchars(substr($command, 0, 50)) . "...</p>";
                $success_count++;
            } catch (PDOException $e) {
                echo "<p style='color: red'>Erro ao executar: " . htmlspecialchars(substr($command, 0, 100)) . "<br>Mensagem: " . $e->getMessage() . "</p>";
                // Não interromper a execução em caso de erro, continuar com os próximos comandos
                echo "<p style='color: red'>Comando: " . htmlspecialchars($command) . "</p>";
                $error_count++;
            }
        }
    }
    
    // Reativar verificação de chaves estrangeiras
    $conn->exec("SET session_replication_role = 'origin'"); // Equivalente ao FOREIGN_KEY_CHECKS=1 no PostgreSQL
    
    echo "</div>";
    
    echo "<h3>Resumo:</h3>";
    echo "<p>Comandos executados com sucesso: $success_count</p>";
    echo "<p>Comandos com erro: $error_count</p>";
    
    if ($error_count == 0) {
        echo "<h3 style='color: green'>Todos os comandos foram executados com sucesso!</h3>";
    } else {
        echo "<h3 style='color: orange'>Alguns comandos apresentaram erros. Verifique os detalhes acima.</h3>";
    }
    
    echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
    
    $conn = null; // Fechamento da conexão PDO
    
} catch (PDOException $e) {
    echo "<p style='color: red'>{$e->getMessage()}</p>";
    echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
}
?>