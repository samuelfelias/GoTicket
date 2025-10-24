<?php
// Script para padronizar os engines das tabelas para InnoDB

// Exibir erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Aumentar limite de tempo de execução
set_time_limit(300); // 5 minutos

echo "<h1>Padronização de Engines das Tabelas</h1>";

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
    
    echo "<p>Verificando e padronizando engines das tabelas para InnoDB...</p>";
    
    // Obter todas as tabelas do banco de dados
    $result = $conn->query("SHOW TABLES");
    
    if ($result->num_rows > 0) {
        echo "<h2>Padronizando engines das tabelas:</h2>";
        echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;'>";
        
        // Desativar verificação de chaves estrangeiras temporariamente
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        
        while ($row = $result->fetch_row()) {
            $table = $row[0];
            
            // Verificar o engine atual da tabela
            $engine_result = $conn->query("SHOW TABLE STATUS WHERE Name = '$table'");
            $table_info = $engine_result->fetch_assoc();
            $current_engine = $table_info['Engine'];
            
            if ($current_engine != 'InnoDB') {
                // Alterar o engine da tabela para InnoDB
                if ($conn->query("ALTER TABLE `$table` ENGINE=InnoDB")) {
                    echo "<p style='color: green'>Tabela '$table': Engine alterado de $current_engine para InnoDB com sucesso.</p>";
                } else {
                    echo "<p style='color: red'>Tabela '$table': Erro ao alterar engine: " . $conn->error . "</p>";
                }
            } else {
                echo "<p style='color: blue'>Tabela '$table': Já está usando InnoDB.</p>";
            }
        }
        
        // Reativar verificação de chaves estrangeiras
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        
        echo "</div>";
    } else {
        echo "<p style='color: orange'>Nenhuma tabela encontrada no banco de dados.</p>";
    }
    
    echo "<h2>Processo de padronização concluído!</h2>";
    echo "<p>Todas as tabelas foram verificadas e, se necessário, convertidas para o engine InnoDB.</p>";
    echo "<p>Isso deve resolver problemas relacionados a chaves estrangeiras e integridade referencial.</p>";
    
    echo "<p><a href='diagnostico.php'>Voltar para o Diagnóstico</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red'>{$e->getMessage()}</p>";
    echo "<p><a href='diagnostico.php'>Voltar para o Diagnóstico</a></p>";
}
?>