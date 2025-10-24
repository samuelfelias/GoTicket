<?php
// Incluir arquivo de configuração do banco de dados
require_once 'config/database.php';

try {
    // Conectar ao banco de dados
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSLMODE;
    $conexao = new PDO($dsn, DB_USER, DB_PASS);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Adicionando coluna horario_inicio à tabela evento</h1>";
    
    // Verificar se a coluna já existe
    $stmt = $conexao->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'evento' AND column_name = 'horario_inicio' AND table_schema = 'public'");
    $stmt->execute();
    $coluna_existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$coluna_existe) {
        // Adicionar a coluna horario_inicio
        $sql = "ALTER TABLE evento ADD COLUMN horario_inicio TIME";
        $conexao->exec($sql);
        echo "<p>✓ Coluna horario_inicio adicionada com sucesso!</p>";
        
        // Se existir a coluna 'horario', copiar os valores para horario_inicio
        $stmt = $conexao->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'evento' AND column_name = 'horario' AND table_schema = 'public'");
        $stmt->execute();
        $coluna_horario_existe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($coluna_horario_existe) {
            $sql = "UPDATE evento SET horario_inicio = horario WHERE horario_inicio IS NULL AND horario IS NOT NULL";
            $conexao->exec($sql);
            echo "<p>✓ Valores da coluna horario copiados para horario_inicio.</p>";
        }
    } else {
        echo "<p>⚠ A coluna horario_inicio já existe na tabela evento.</p>";
    }
    
    echo "<p>Operação concluída com sucesso!</p>";
    echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
    
} catch (PDOException $e) {
    echo "<h1>Erro</h1>";
    echo "<p>Ocorreu um erro ao adicionar a coluna: " . $e->getMessage() . "</p>";
    echo "<p><a href='index.php'>Voltar para a página inicial</a></p>";
}
?>