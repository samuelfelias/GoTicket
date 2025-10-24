<?php
// Script para verificar e fornecer instruções para instalar a extensão PDO_PGSQL

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
    .btn { display: inline-block; background-color: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; }
    .btn:hover { background-color: #0056b3; }
    ul { padding-left: 20px; }
    li { margin-bottom: 10px; }
</style>";

echo "<div class='container'>";
echo "<h1>Verificação e Instalação da Extensão PDO_PGSQL</h1>";

// Verificar se as extensões necessárias estão instaladas
echo "<h2>Verificando extensões necessárias:</h2>";
echo "<ul>";
echo "<li>PDO: " . (extension_loaded('pdo') ? '<span class="success">Instalada</span>' : '<span class="error">Não instalada</span>') . "</li>";
echo "<li>PDO_PGSQL: " . (extension_loaded('pdo_pgsql') ? '<span class="success">Instalada</span>' : '<span class="error">Não instalada</span>') . "</li>";
echo "<li>PGSQL: " . (extension_loaded('pgsql') ? '<span class="success">Instalada</span>' : '<span class="error">Não instalada</span>') . "</li>";
echo "</ul>";

// Verificar o sistema operacional
$os = PHP_OS;
$isWindows = (strtoupper(substr($os, 0, 3)) === 'WIN');
$isLinux = (strtoupper(substr($os, 0, 5)) === 'LINUX');
$isMac = (strtoupper(substr($os, 0, 6)) === 'DARWIN');

echo "<h2>Informações do Sistema:</h2>";
echo "<ul>";
echo "<li>Sistema Operacional: <strong>$os</strong></li>";
echo "<li>Versão do PHP: <strong>" . phpversion() . "</strong></li>";
echo "<li>Arquivo php.ini: <strong>" . php_ini_loaded_file() . "</strong></li>";
echo "</ul>";

// Se PDO_PGSQL não estiver instalado, fornecer instruções
if (!extension_loaded('pdo_pgsql')) {
    echo "<div class='error'>";
    echo "<h2>A extensão PDO_PGSQL não está instalada ou habilitada!</h2>";
    echo "<p>Esta extensão é necessária para conectar ao PostgreSQL usando PDO.</p>";
    echo "</div>";
    
    echo "<h3>Instruções para instalar a extensão PDO_PGSQL:</h3>";
    
    if ($isWindows) {
        echo "<div class='info'>";
        echo "<h4>Para Windows:</h4>";
        echo "<ol>";
        echo "<li>Abra o arquivo php.ini (localizado em: <code>" . php_ini_loaded_file() . "</code>)</li>";
        echo "<li>Procure a linha <code>;extension=pdo_pgsql</code> e remova o ponto e vírgula do início (descomente a linha)</li>";
        echo "<li>Se a linha não existir, adicione <code>extension=pdo_pgsql</code> na seção de extensões</li>";
        echo "<li>Salve o arquivo e reinicie o servidor web (Apache, Nginx, etc.)</li>";
        echo "</ol>";
        
        echo "<h4>Se a extensão não estiver disponível:</h4>";
        echo "<ol>";
        echo "<li>Verifique se você tem o PostgreSQL instalado no seu sistema</li>";
        echo "<li>Para XAMPP/WAMP, você pode precisar baixar a extensão separadamente</li>";
        echo "<li>Para XAMPP: <a href='https://www.apachefriends.org/download.html' target='_blank'>Baixe a versão mais recente do XAMPP</a> que inclui a extensão</li>";
        echo "<li>Para WAMP: Use o WampServer Manager para instalar a extensão</li>";
        echo "</ol>";
        echo "</div>";
    } elseif ($isLinux) {
        echo "<div class='info'>";
        echo "<h4>Para Linux (Debian/Ubuntu):</h4>";
        echo "<pre>sudo apt-get update\nsudo apt-get install php-pgsql</pre>";
        
        echo "<h4>Para Linux (CentOS/RHEL):</h4>";
        echo "<pre>sudo yum install php-pgsql</pre>";
        
        echo "<p>Após a instalação, reinicie o servidor web:</p>";
        echo "<pre>sudo systemctl restart apache2</pre> ou <pre>sudo systemctl restart nginx</pre>";
        echo "</div>";
    } elseif ($isMac) {
        echo "<div class='info'>";
        echo "<h4>Para macOS (usando Homebrew):</h4>";
        echo "<pre>brew install php\nbrew install postgresql</pre>";
        
        echo "<p>Ou se você estiver usando MAMP:</p>";
        echo "<ol>";
        echo "<li>Abra o MAMP</li>";
        echo "<li>Vá para Preferências > PHP</li>";
        echo "<li>Selecione a versão do PHP que você está usando</li>";
        echo "<li>Clique em 'Extensões' e verifique se 'pdo_pgsql' está habilitado</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='info'>";
        echo "<p>Para o seu sistema operacional, consulte a documentação específica para instalar a extensão PDO_PGSQL para PHP.</p>";
        echo "</div>";
    }
    
    echo "<h3>Verificação após a instalação:</h3>";
    echo "<p>Depois de instalar a extensão, volte a esta página para verificar se a instalação foi bem-sucedida.</p>";
    echo "<p>Você também pode verificar usando a função <code>phpinfo()</code>:</p>";
    echo "<pre>&lt;?php phpinfo(); ?&gt;</pre>";
    
    echo "<p>Procure por 'pdo_pgsql' na página gerada para confirmar que a extensão está ativa.</p>";
} else {
    echo "<div class='success'>";
    echo "<h2>A extensão PDO_PGSQL está instalada e ativa!</h2>";
    echo "<p>Você pode prosseguir com a conexão ao PostgreSQL usando PDO.</p>";
    echo "</div>";
    
    echo "<h3>Próximos passos:</h3>";
    echo "<ul>";
    echo "<li><a href='teste_conexao.php' class='btn'>Testar Conexão com o PostgreSQL</a></li>";
    echo "<li><a href='diagnostico_postgres.php' class='btn'>Executar Diagnóstico Completo</a></li>";
    echo "<li><a href='index_diagnostico_postgres.php' class='btn'>Voltar para o Diagnóstico</a></li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index_diagnostico_postgres.php'>Voltar para a página de diagnóstico</a></p>";
echo "</div>";
?>