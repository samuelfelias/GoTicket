<?php
// Script para corrigir problemas de collation no banco de dados

// Exibir erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Aumentar limite de tempo de execução
set_time_limit(300); // 5 minutos

echo "<h1>Correção de Collation do Banco de Dados</h1>";

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = ""; // Senha padrão vazia para o WAMP
$dbname = "sistema_ingressos";

// Conectar ao servidor MySQL
try {
    $conn = new mysqli($servername, $username, $password);
    
    // Verificar conexão
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }
    
    echo "<p style='color: green'>Conexão ao servidor MySQL bem-sucedida!</p>";
    
    // Verificar se o banco de dados existe
    $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
    
    if ($result->num_rows == 0) {
        echo "<p style='color: red'>Banco de dados '$dbname' não existe. Por favor, execute primeiro o script para criar o banco de dados.</p>";
        echo "<p><a href='criar_banco.php'>Criar Banco de Dados</a></p>";
        exit;
    }
    
    // Selecionar o banco de dados
    $conn->select_db($dbname);
    
    echo "<p>Verificando e corrigindo collation do banco de dados...</p>";
    
    // Alterar o collation do banco de dados
    if ($conn->query("ALTER DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        echo "<p style='color: green'>Collation do banco de dados alterado com sucesso para utf8mb4_unicode_ci.</p>";
    } else {
        echo "<p style='color: red'>Erro ao alterar collation do banco de dados: " . $conn->error . "</p>";
    }
    
    // Obter todas as tabelas do banco de dados
    $result = $conn->query("SHOW TABLES");
    
    if ($result->num_rows > 0) {
        echo "<h2>Corrigindo collation das tabelas:</h2>";
        echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;'>";
        
        while ($row = $result->fetch_row()) {
            $table = $row[0];
            
            // Alterar o collation da tabela
            if ($conn->query("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                echo "<p style='color: green'>Tabela '$table': Collation alterado com sucesso.</p>";
            } else {
                echo "<p style='color: red'>Tabela '$table': Erro ao alterar collation: " . $conn->error . "</p>";
            }
            
            // Obter todas as colunas da tabela
            $columns_result = $conn->query("SHOW FULL COLUMNS FROM `$table`");
            
            if ($columns_result->num_rows > 0) {
                while ($column = $columns_result->fetch_assoc()) {
                    $column_name = $column['Field'];
                    $column_type = $column['Type'];
                    
                    // Verificar se a coluna é do tipo string (char, varchar, text, etc.)
                    if (strpos($column_type, 'char') !== false || 
                        strpos($column_type, 'text') !== false || 
                        strpos($column_type, 'enum') !== false || 
                        strpos($column_type, 'set') !== false) {
                        
                        // Alterar o collation da coluna
                        $query = "ALTER TABLE `$table` MODIFY `$column_name` $column_type CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                        
                        if ($conn->query($query)) {
                            echo "<p style='color: green'>Coluna '$table.$column_name': Collation alterado com sucesso.</p>";
                        } else {
                            echo "<p style='color: red'>Coluna '$table.$column_name': Erro ao alterar collation: " . $conn->error . "</p>";
                        }
                    }
                }
            }
        }
        
        echo "</div>";
    } else {
        echo "<p style='color: orange'>Nenhuma tabela encontrada no banco de dados.</p>";
    }
    
    echo "<h2>Processo de correção concluído!</h2>";
    echo "<p>O banco de dados e suas tabelas foram atualizados para usar o collation utf8mb4_unicode_ci.</p>";
    echo "<p>Isso deve resolver problemas relacionados a caracteres especiais e compatibilidade entre tabelas.</p>";
    
    echo "<p><a href='diagnostico.php'>Voltar para o Diagnóstico</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red'>{$e->getMessage()}</p>";
    echo "<p><a href='diagnostico.php'>Voltar para o Diagnóstico</a></p>";
}
?>