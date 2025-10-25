<?php
session_start();
require_once 'config/database.php';

// Verificar se o usuário está logado e é um CLIENTE
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'CLIENTE') {
    $_SESSION['mensagem'] = "Acesso não autorizado!";
    header("Location: login.php");
    exit();
}

// Verificar se o ID do ingresso foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem'] = "ID do ingresso não fornecido!";
    header("Location: meus_ingressos.php");
    exit();
}

$ingresso_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];
$conexao = conectarBD();

try {
    // Buscar informações do ingresso
    $stmt = $conexao->prepare("SELECT iu.id, iu.codigo, iu.status, iu.data_aquisicao, iu.id_evento,
                           i.tipo, i.preco, 
                           e.nome as evento_nome, e.data, e.horario_inicio, e.local, e.status as status_evento
                           FROM ingressousuario iu
                           JOIN ingresso i ON iu.ingresso_id = i.id_ingresso
                           JOIN evento e ON iu.id_evento = e.id_evento
                           WHERE iu.id = ? AND iu.usuario_id = ?");
    $stmt->execute([$ingresso_id, $usuario_id]);
    $ingresso = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ingresso) {
        throw new Exception("Ingresso não encontrado ou não pertence a este usuário!");
    }
    
    // Gerar conteúdo do ingresso otimizado para impressão
    $html = '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Ingresso - ' . htmlspecialchars($ingresso['evento_nome']) . '</title>
        <style>
            @media print {
                body { margin: 0; padding: 0; }
                .no-print { display: none !important; }
                .ingresso { box-shadow: none; border: 2px solid #000; }
            }
            
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .ingresso {
                border: 2px solid #000;
                padding: 20px;
                max-width: 600px;
                margin: 0 auto;
                background-color: #fff;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                border-radius: 10px;
                position: relative;
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #4a6fdc;
                padding-bottom: 15px;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #4a6fdc, #6c5ce7);
                color: white;
                margin: -20px -20px 20px -20px;
                padding: 20px;
                border-radius: 10px 10px 0 0;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
                font-weight: bold;
            }
            .header h3 {
                margin: 5px 0 0 0;
                font-size: 18px;
                opacity: 0.9;
            }
            .qr-code {
                text-align: center;
                margin: 20px 0;
                padding: 20px;
                border: 2px dashed #4a6fdc;
                background: linear-gradient(45deg, #f8f9ff, #e8f2ff);
                border-radius: 10px;
            }
            .codigo-valor {
                font-size: 28px;
                font-weight: bold;
                letter-spacing: 3px;
                color: #2d3436;
                margin: 10px 0;
                font-family: "Courier New", monospace;
            }
            .info {
                margin-bottom: 20px;
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            .info-item {
                margin-bottom: 15px;
                padding: 10px;
                background-color: #f8f9fa;
                border-radius: 5px;
                border-left: 4px solid #4a6fdc;
            }
            .info-label {
                font-weight: bold;
                color: #2d3436;
                margin-bottom: 5px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .info-value {
                font-size: 16px;
                color: #636e72;
            }
            .status {
                display: inline-block;
                padding: 8px 15px;
                border-radius: 20px;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 12px;
                letter-spacing: 1px;
            }
            .status-ATIVO {
                background-color: #00b894;
                color: white;
            }
            .status-USADO {
                background-color: #636e72;
                color: white;
            }
            .status-CANCELADO {
                background-color: #e17055;
                color: white;
            }
            .footer {
                text-align: center;
                font-size: 12px;
                margin-top: 30px;
                border-top: 1px solid #ddd;
                padding-top: 15px;
                color: #636e72;
                background-color: #f8f9fa;
                margin: 30px -20px -20px -20px;
                padding: 15px 20px;
                border-radius: 0 0 10px 10px;
            }
            .print-btn {
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #4a6fdc;
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 25px;
                cursor: pointer;
                font-weight: bold;
                box-shadow: 0 4px 15px rgba(74, 111, 220, 0.3);
                transition: all 0.3s ease;
            }
            .print-btn:hover {
                background-color: #3b5bdb;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(74, 111, 220, 0.4);
            }
            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 60px;
                color: rgba(74, 111, 220, 0.1);
                font-weight: bold;
                pointer-events: none;
                z-index: 1;
            }
        </style>
    </head>
    <body>
        <button class="print-btn no-print" onclick="window.print()">
            🖨️ Imprimir Ingresso
        </button>
        
        <div class="ingresso">
            <div class="watermark">GOTICKET</div>
            
            <div class="header">
                <h1>' . htmlspecialchars($ingresso['evento_nome']) . '</h1>
                <h3>Ingresso ' . htmlspecialchars($ingresso['tipo']) . '</h3>
            </div>
            
            <div class="qr-code">
                <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold; color: #2d3436;">CÓDIGO DO INGRESSO</p>
                <p class="codigo-valor">' . htmlspecialchars($ingresso['codigo']) . '</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($ingresso['codigo']) . '&format=png&ecc=M&color=2d3436&bgcolor=ffffff" alt="QR Code" style="margin: 15px auto; display: block; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div class="info">
                <div class="info-item">
                    <div class="info-label">Data do Evento</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($ingresso['data'])) . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Horário</div>
                    <div class="info-value">' . $ingresso['horario_inicio'] . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Local</div>
                    <div class="info-value">' . htmlspecialchars($ingresso['local']) . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tipo de Ingresso</div>
                    <div class="info-value">' . htmlspecialchars($ingresso['tipo']) . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Valor Pago</div>
                    <div class="info-value">R$ ' . number_format($ingresso['preco'], 2, ',', '.') . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status status-' . $ingresso['status'] . '">' . $ingresso['status'] . '</span>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p><strong>Ingresso emitido em:</strong> ' . date('d/m/Y H:i', strtotime($ingresso['data_aquisicao'])) . '</p>
                <p><strong>Importante:</strong> Este ingresso é pessoal e intransferível.</p>
                <p><strong>Instruções:</strong> Apresente este ingresso na entrada do evento.</p>
                <p style="margin-top: 10px; font-size: 10px; color: #999;">Sistema GoTicket - www.goticket.com.br</p>
            </div>
        </div>
        
        <script>
            // Auto-print quando a página carrega (opcional)
            // window.onload = function() { window.print(); }
        </script>
    </body>
    </html>
    ';
    
    // Definir cabeçalhos para download
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="ingresso-' . $ingresso['codigo'] . '.html"');
    
    // Enviar o conteúdo
    echo $html;
    exit();
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao gerar ingresso para download: " . $e->getMessage();
    header("Location: meus_ingressos.php");
    exit();
}
?>
