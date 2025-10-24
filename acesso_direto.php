<?php
// Este arquivo permite acesso direto às ferramentas de diagnóstico sem passar pelo sistema de login

// Exibir erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Estilo CSS para melhorar a aparência
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
    h1, h2, h3 { color: #333; }
    .container { max-width: 800px; margin: 0 auto; }
    .success { color: green; background-color: #e8f5e9; padding: 10px; border-left: 5px solid green; }
    .error { color: #721c24; background-color: #f8d7da; padding: 10px; border-left: 5px solid #721c24; }
    .warning { color: #856404; background-color: #fff3cd; padding: 10px; border-left: 5px solid #856404; }
    .info { color: #0c5460; background-color: #d1ecf1; padding: 10px; border-left: 5px solid #0c5460; }
    code { background-color: #f5f5f5; padding: 2px 5px; border-radius: 3px; font-family: monospace; }
    pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    .btn { display: inline-block; background-color: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px; }
    .btn:hover { background-color: #0056b3; }
    ul { padding-left: 20px; }
    li { margin-bottom: 10px; }
</style>";

echo "<div class='container'>";
echo "<h1>Acesso Direto - GoTicket com PostgreSQL</h1>";

echo "<div class='info'>";
echo "<p>Esta página permite acessar diretamente as ferramentas de diagnóstico e configuração do PostgreSQL sem passar pelo sistema de login.</p>";
echo "</div>";

echo "<h2>Ferramentas de Diagnóstico</h2>";
echo "<ul>";
echo "<li><a href='diagnostico_postgres.php' class='btn'>Verificar Ambiente PostgreSQL</a></li>";
echo "<li><a href='teste_conexao.php' class='btn'>Testar Conexão com o Banco</a></li>";
echo "<li><a href='instalar_extensao_pgsql.php' class='btn'>Instalar Extensão PDO_PGSQL</a></li>";
echo "<li><a href='criar_banco.php' class='btn'>Criar Schema</a></li>";
echo "<li><a href='executar_sql.php' class='btn'>Executar Script SQL</a></li>";
echo "<li><a href='migrar_banco_postgres.php' class='btn'>Migrar Banco de Dados</a></li>";
echo "<li><a href='atualizar_banco_postgres.php' class='btn'>Atualizar Estrutura do Banco</a></li>";
echo "<li><a href='corrigir_encoding_postgres.php' class='btn'>Corrigir Encoding</a></li>";
echo "</ul>";

echo "<h2>Informações do Sistema</h2>";
echo "<ul>";
echo "<li>Sistema Operacional: <strong>" . PHP_OS . "</strong></li>";
echo "<li>Versão do PHP: <strong>" . phpversion() . "</strong></li>";
echo "<li>Extensão PDO: " . (extension_loaded('pdo') ? '<span class="success">Instalada</span>' : '<span class="error">Não instalada</span>') . "</li>";
echo "<li>Extensão PDO_PGSQL: " . (extension_loaded('pdo_pgsql') ? '<span class="success">Instalada</span>' : '<span class="error">Não instalada</span>') . "</li>";
echo "<li>Extensão PGSQL: " . (extension_loaded('pgsql') ? '<span class="success">Instalada</span>' : '<span class="error">Não instalada</span>') . "</li>";
echo "</ul>";

echo "<h2>Links Úteis</h2>";
echo "<ul>";
echo "<li><a href='index_diagnostico_postgres.php'>Página de Diagnóstico PostgreSQL</a></li>";
echo "<li><a href='login.php'>Página de Login</a></li>";
echo "<li><a href='https://www.postgresql.org/docs/' target='_blank'>Documentação do PostgreSQL</a></li>";
echo "<li><a href='https://supabase.com/docs' target='_blank'>Documentação do Supabase</a></li>";
echo "</ul>";

echo "</div>";
?>