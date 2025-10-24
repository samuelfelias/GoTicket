<?php

// Usa o autoloader manual (funciona sem Composer)
require_once __DIR__ . '/../autoload.php';

// Carrega arquivos de tipos manualmente (classes múltiplas em um arquivo)
require_once __DIR__ . '/../src/Model/EventoTypes.php';
require_once __DIR__ . '/../src/Model/IngressoTypes.php';

// Carrega arquivos com múltiplas classes de padrões
require_once __DIR__ . '/../src/Pattern/IngressoDecorator.php'; // Contém várias classes Decorator
require_once __DIR__ . '/../src/Pattern/PaymentStrategy.php';   // Contém várias classes Strategy
require_once __DIR__ . '/../src/Pattern/ValidationStrategy.php'; // Contém várias classes Strategy
require_once __DIR__ . '/../src/Pattern/NotificationObserver.php'; // Contém várias classes Observer
require_once __DIR__ . '/../src/Pattern/EventoFactory.php';     // Contém várias factories
require_once __DIR__ . '/../src/Pattern/IngressoFactory.php';   // Contém várias factories

use App\Model\Usuario;
use App\Pattern\EventoFactory;
use App\Pattern\IngressoFactory;
use App\Pattern\PacoteIngresso;
use App\Pattern\DescontoPercentual;
use App\Pattern\DescontoFixo;
use App\Pattern\PromocaoBlackFriday;
use App\Pattern\DescontoGrupo;
use App\Pattern\CartaoCreditoStrategy;
use App\Pattern\PixStrategy;
use App\Pattern\BoletoStrategy;
use App\Pattern\QRCodeValidationStrategy;
use App\Pattern\NotificationManager;
use App\Pattern\EmailObserver;
use App\Pattern\SMSObserver;
use App\Pattern\PushNotificationObserver;

echo "═══════════════════════════════════════════════════════════════════\n";
echo "          🎫 SISTEMA GOTICKET - DEMONSTRAÇÃO DE PADRÕES GOF\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// ============================================================================
// 1. SINGLETON PATTERN - Conexão única com banco de dados
// ============================================================================
echo "📌 1. SINGLETON PATTERN - Conexão com Banco de Dados\n";
echo "─────────────────────────────────────────────────────────────────────\n";
try {
    $db1 = \App\Database\Database::getInstance();
    $db2 = \App\Database\Database::getInstance();
    
    if ($db1 === $db2) {
        echo "✅ Sucesso: Ambas as instâncias são idênticas (Singleton funcionando)\n";
        echo "   Hash DB1: " . spl_object_hash($db1) . "\n";
        echo "   Hash DB2: " . spl_object_hash($db2) . "\n";
    }
} catch (\Exception $e) {
    echo "ℹ️  Conexão com banco não configurada: {$e->getMessage()}\n";
    echo "   (Configure config/database.php para conectar ao MySQL)\n";
}
echo "\n";

// ============================================================================
// 2. FACTORY METHOD PATTERN - Criação de Eventos e Ingressos
// ============================================================================
echo "📌 2. FACTORY METHOD PATTERN - Criação de Eventos\n";
echo "─────────────────────────────────────────────────────────────────────\n";

$eventoFactory = EventoFactory::getFactory('show');
$show = $eventoFactory->criarEvento(
    null,
    'Rock in Rio 2025',
    'Festival de música com grandes bandas internacionais',
    '2025-09-20 18:00:00',
    'Cidade do Rock - RJ',
    100000
);
echo "✅ Evento criado: {$show->getNome()} - Tipo: {$show->getTipoEvento()}\n";

$eventoFactory2 = EventoFactory::getFactory('palestra');
$palestra = $eventoFactory2->criarEvento(
    null,
    'DevConf 2025',
    'Conferência de desenvolvimento de software',
    '2025-11-15 09:00:00',
    'Centro de Convenções SP',
    500
);
echo "✅ Evento criado: {$palestra->getNome()} - Tipo: {$palestra->getTipoEvento()}\n\n";

echo "📌 2.1. FACTORY METHOD PATTERN - Criação de Ingressos\n";
echo "─────────────────────────────────────────────────────────────────────\n";

$ingressoNormalFactory = IngressoFactory::getFactory('normal');
$ingressoNormal = $ingressoNormalFactory->criarIngresso(null, 1, 1, 100.00);
echo "✅ {$ingressoNormal->getTipoIngresso()} - R$ " . number_format($ingressoNormal->getPreco(), 2, ',', '.') . "\n";

$ingressoVIPFactory = IngressoFactory::getFactory('vip');
$ingressoVIP = $ingressoVIPFactory->criarIngresso(null, 1, 1, 100.00);
echo "✅ {$ingressoVIP->getTipoIngresso()} - R$ " . number_format($ingressoVIP->getPreco(), 2, ',', '.') . "\n";

$ingressoMeiaFactory = IngressoFactory::getFactory('meia');
$ingressoMeia = $ingressoMeiaFactory->criarIngresso(null, 1, 1, 100.00);
echo "✅ {$ingressoMeia->getTipoIngresso()} - R$ " . number_format($ingressoMeia->getPreco(), 2, ',', '.') . "\n\n";

// ============================================================================
// 3. COMPOSITE PATTERN - Pacotes de Ingressos
// ============================================================================
echo "📌 3. COMPOSITE PATTERN - Pacote Familiar de Ingressos\n";
echo "─────────────────────────────────────────────────────────────────────\n";

$pacoteFamiliar = new PacoteIngresso("Pacote Família - Rock in Rio");
$pacoteFamiliar->adicionarIngresso($ingressoNormalFactory->criarIngresso(null, 1, 1, 100.00));
$pacoteFamiliar->adicionarIngresso($ingressoNormalFactory->criarIngresso(null, 1, 2, 100.00));
$pacoteFamiliar->adicionarIngresso($ingressoMeiaFactory->criarIngresso(null, 1, 3, 100.00));
$pacoteFamiliar->adicionarIngresso($ingressoMeiaFactory->criarIngresso(null, 1, 4, 100.00));

echo $pacoteFamiliar->getDescricao() . "\n";
echo "Quantidade de ingressos: {$pacoteFamiliar->getQuantidade()}\n\n";

// ============================================================================
// 4. DECORATOR PATTERN - Descontos e Promoções Cumulativas
// ============================================================================
echo "📌 4. DECORATOR PATTERN - Aplicando Descontos Cumulativos\n";
echo "─────────────────────────────────────────────────────────────────────\n";

$ingresso = $ingressoVIPFactory->criarIngresso(null, 1, 1, 200.00);
echo "Ingresso Base:\n{$ingresso->getDescricao()}\n";
echo "Preço: R$ " . number_format($ingresso->getPreco(), 2, ',', '.') . "\n\n";

// Aplicando desconto de 10% (cliente fidelidade)
$ingressoComDesconto1 = new DescontoPercentual($ingresso, 10, "Cliente Fidelidade");
echo "Após aplicar desconto fidelidade:\n{$ingressoComDesconto1->getDescricao()}\n";
echo "Preço: R$ " . number_format($ingressoComDesconto1->getPreco(), 2, ',', '.') . "\n\n";

// Aplicando mais um desconto de R$ 20 (cupom)
$ingressoComDesconto2 = new DescontoFixo($ingressoComDesconto1, 20.00, "Cupom BEMVINDO");
echo "Após aplicar cupom:\n{$ingressoComDesconto2->getDescricao()}\n";
echo "Preço final: R$ " . number_format($ingressoComDesconto2->getPreco(), 2, ',', '.') . "\n\n";

// Black Friday no pacote
echo "Exemplo: Black Friday no Pacote Familiar\n";
$pacoteComPromocao = new PromocaoBlackFriday($pacoteFamiliar);
echo $pacoteComPromocao->getDescricao() . "\n";
echo "Preço final: R$ " . number_format($pacoteComPromocao->getPreco(), 2, ',', '.') . "\n\n";

// ============================================================================
// 5. STRATEGY PATTERN - Formas de Pagamento
// ============================================================================
echo "📌 5. STRATEGY PATTERN - Processamento de Pagamentos\n";
echo "─────────────────────────────────────────────────────────────────────\n";

$valorCompra = $ingressoComDesconto2->getPreco();

// Pagamento com Cartão de Crédito
$pagamentoCartao = new CartaoCreditoStrategy(3);
echo "Método: {$pagamentoCartao->getNome()}\n";
$resultadoCartao = $pagamentoCartao->processar($valorCompra);
echo "  {$resultadoCartao['mensagem']}\n";
echo "  ID Transação: {$resultadoCartao['transacao_id']}\n\n";

// Pagamento com PIX (com desconto adicional)
$pagamentoPix = new PixStrategy();
echo "Método: {$pagamentoPix->getNome()}\n";
$resultadoPix = $pagamentoPix->processar($valorCompra);
echo "  {$resultadoPix['mensagem']}\n";
echo "  ID Transação: {$resultadoPix['transacao_id']}\n\n";

// Pagamento com Boleto
$pagamentoBoleto = new BoletoStrategy();
echo "Método: {$pagamentoBoleto->getNome()}\n";
$resultadoBoleto = $pagamentoBoleto->processar($valorCompra);
echo "  {$resultadoBoleto['mensagem']}\n";
echo "  Código de Barras: {$resultadoBoleto['codigo_barras']}\n\n";

// ============================================================================
// 6. STRATEGY PATTERN - Validação de Ingressos
// ============================================================================
echo "📌 6. STRATEGY PATTERN - Validação de Ingressos na Entrada\n";
echo "─────────────────────────────────────────────────────────────────────\n";

$ingressoParaValidar = $ingressoNormalFactory->criarIngresso(null, 1, 1, 100.00);
$ingressoParaValidar->setStatus('ativo');

$validadorQRCode = new QRCodeValidationStrategy();
$resultadoValidacao = $validadorQRCode->validar($ingressoParaValidar);

if ($resultadoValidacao['valido']) {
    echo "✅ {$resultadoValidacao['mensagem']}\n";
    echo "   Código: {$resultadoValidacao['codigo']}\n";
    echo "   Tipo: {$resultadoValidacao['tipo']}\n";
} else {
    echo "❌ {$resultadoValidacao['mensagem']}\n";
}
echo "\n";

// ============================================================================
// 7. OBSERVER PATTERN - Sistema de Notificações
// ============================================================================
echo "📌 7. OBSERVER PATTERN - Sistema de Notificações\n";
echo "─────────────────────────────────────────────────────────────────────\n";

// Criando usuários
$usuario1 = new Usuario(1, 'João Silva', 'joao@email.com', '(11) 98765-4321');
$usuario2 = new Usuario(2, 'Maria Santos', 'maria@email.com', '(11) 91234-5678');

// Criando gerenciador de notificações
$notificationManager = new NotificationManager();

// Registrando observadores para o usuário 1
$emailObserver1 = new EmailObserver($usuario1);
$smsObserver1 = new SMSObserver($usuario1);
$pushObserver1 = new PushNotificationObserver($usuario1);

$notificationManager->attach($emailObserver1);
$notificationManager->attach($smsObserver1);
$notificationManager->attach($pushObserver1);

// Registrando apenas email para o usuário 2
$emailObserver2 = new EmailObserver($usuario2);
$notificationManager->attach($emailObserver2);

// Notificando sobre novo evento
echo "Notificando usuários sobre novo evento:\n";
$notificationManager->notify(
    'Novo evento disponível: Rock in Rio 2025! Garanta já seus ingressos.',
    [
        'tipo' => 'novo_evento',
        'titulo' => 'Novo Evento',
        'evento_id' => 1,
        'evento_nome' => 'Rock in Rio 2025'
    ]
);

echo "\n";

// Notificando sobre promoção
echo "Notificando usuários sobre promoção:\n";
$notificationManager->notify(
    'BLACK FRIDAY: 50% de desconto em todos os ingressos! Corre!',
    [
        'tipo' => 'promocao',
        'titulo' => 'Promoção Especial',
        'desconto' => 50
    ]
);

echo "\n";

// ============================================================================
// 8. DEMONSTRAÇÃO COMPLETA: Fluxo de Compra
// ============================================================================
echo "═══════════════════════════════════════════════════════════════════\n";
echo "📌 8. FLUXO COMPLETO DE COMPRA COM TODOS OS PADRÕES\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "1️⃣  Cliente: {$usuario1->getNome()}\n";
echo "2️⃣  Evento: {$show->getNome()} - {$show->getTipoEvento()}\n";
echo "3️⃣  Criando pacote de ingressos (Composite)...\n\n";

$pacoteCompra = new PacoteIngresso("Combo Show");
$pacoteCompra->adicionarIngresso($ingressoVIPFactory->criarIngresso(null, 1, 1, 150.00));
$pacoteCompra->adicionarIngresso($ingressoNormalFactory->criarIngresso(null, 1, 1, 150.00));

echo $pacoteCompra->getDescricao() . "\n\n";

echo "4️⃣  Aplicando promoções (Decorator)...\n";
$pacoteComDescontoGrupo = new DescontoGrupo($pacoteCompra, 2);
$pacoteComDescontoECupom = new DescontoFixo($pacoteComDescontoGrupo, 30.00, "Cupom PRIMEIRAVEZ");

echo $pacoteComDescontoECupom->getDescricao() . "\n";
echo "\nValor final: R$ " . number_format($pacoteComDescontoECupom->getPreco(), 2, ',', '.') . "\n\n";

echo "5️⃣  Processando pagamento (Strategy - PIX)...\n";
$pagamento = new PixStrategy();
$resultado = $pagamento->processar($pacoteComDescontoECupom->getPreco());
echo "   Status: {$resultado['status']}\n";
echo "   {$resultado['mensagem']}\n";
echo "   ID Transação: {$resultado['transacao_id']}\n\n";

echo "6️⃣  Enviando confirmação (Observer)...\n";
$confirmacaoManager = new NotificationManager();
$confirmacaoManager->attach($emailObserver1);
$confirmacaoManager->attach($smsObserver1);

$confirmacaoManager->notify(
    "Compra confirmada! Seus ingressos para {$show->getNome()} estão prontos. Total: R$ " . 
    number_format($resultado['valor_total'], 2, ',', '.'),
    [
        'tipo' => 'confirmacao',
        'titulo' => 'Compra Confirmada',
        'transacao_id' => $resultado['transacao_id']
    ]
);

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "✅ DEMONSTRAÇÃO CONCLUÍDA - Todos os padrões GoF aplicados!\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "📊 PADRÕES UTILIZADOS:\n";
echo "   ✓ Singleton       - Conexão única com banco de dados\n";
echo "   ✓ Repository      - Encapsulamento de persistência\n";
echo "   ✓ Factory Method  - Criação de eventos e ingressos\n";
echo "   ✓ Composite       - Pacotes de ingressos\n";
echo "   ✓ Decorator       - Descontos e promoções cumulativas\n";
echo "   ✓ Strategy        - Pagamentos e validações\n";
echo "   ✓ Observer        - Sistema de notificações\n\n";
