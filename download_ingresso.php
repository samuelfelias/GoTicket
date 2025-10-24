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
                           e.nome as evento_nome, e.data, e.horario, e.local, e.status as status_evento
                           FROM IngressoUsuario iu
                           JOIN Ingresso i ON iu.ingresso_id = i.id_ingresso
                           JOIN Evento e ON iu.id_evento = e.id_evento
                           WHERE iu.id = ? AND iu.usuario_id = ?");
    $stmt->bind_param("ii", $ingresso_id, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $ingresso = $resultado->fetch_assoc();
    
    if (!$ingresso) {
        throw new Exception("Ingresso não encontrado ou não pertence a este usuário!");
    }
    
    // Gerar conteúdo do ingresso em formato PDF (simulado com HTML)
    $html = '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Ingresso - ' . htmlspecialchars($ingresso['evento_nome']) . '</title>
        <style>
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
            }
            .header {
                text-align: center;
                border-bottom: 1px solid #ccc;
                padding-bottom: 10px;
                margin-bottom: 20px;
                background-color: #4a6fdc;
                color: white;
                margin: -20px -20px 20px -20px;
                padding: 20px;
                border-radius: 10px 10px 0 0;
            }
            .qr-code {
                text-align: center;
                margin: 20px 0;
                padding: 15px;
                border: 1px dashed #ccc;
                background-color: #f9f9f9;
                border-radius: 5px;
            }
            .codigo-valor {
                font-size: 24px;
                font-weight: bold;
                letter-spacing: 2px;
            }
            .info {
                margin-bottom: 20px;
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .info-item {
                margin-bottom: 10px;
            }
            .info-label {
                font-weight: bold;
                color: #555;
                margin-bottom: 5px;
            }
            .info-value {
                font-size: 16px;
            }
            .status {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 4px;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 14px;
            }
            .status-ATIVO {
                background-color: #4CAF50;
                color: white;
            }
            .status-USADO {
                background-color: #9E9E9E;
                color: white;
            }
            .status-CANCELADO {
                background-color: #F44336;
                color: white;
            }
            .footer {
                text-align: center;
                font-size: 12px;
                margin-top: 20px;
                border-top: 1px solid #ccc;
                padding-top: 10px;
                color: #777;
            }
        </style>
    </head>
    <body>
        <div class="ingresso">
            <div class="header">
                <h1>' . htmlspecialchars($ingresso['evento_nome']) . '</h1>
                <h3>Ingresso ' . htmlspecialchars($ingresso['tipo']) . '</h3>
            </div>
            
            <div class="qr-code">
                <p><strong>CÓDIGO DO INGRESSO</strong></p>
                <p class="codigo-valor">' . htmlspecialchars($ingresso['codigo']) . '</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($ingresso['codigo']) . '" alt="QR Code" style="margin: 15px auto; display: block;">
            </div>
            
            <div class="info">
                <div class="info-item">
                    <div class="info-label">Data:</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($ingresso['data'])) . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Horário:</div>
                    <div class="info-value">' . $ingresso['horario'] . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Local:</div>
                    <div class="info-value">' . htmlspecialchars($ingresso['local']) . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Tipo de Ingresso:</div>
                    <div class="info-value">' . htmlspecialchars($ingresso['tipo']) . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Valor:</div>
                    <div class="info-value">R$ ' . number_format($ingresso['preco'], 2, ',', '.') . '</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status status-' . $ingresso['status'] . '">' . $ingresso['status'] . '</span>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>Este ingresso foi emitido em ' . date('d/m/Y H:i', strtotime($ingresso['data_aquisicao'])) . '</p>
                <p>Este ingresso é pessoal e intransferível.</p>
                <p>Apresente este ingresso na entrada do evento.</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    // Definir cabeçalhos para download
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="ingresso-' . $ingresso['codigo'] . '.html"');
    
    // Fechar conexão
    $conexao->close();
    
    // Enviar o conteúdo
    echo $html;
    exit();
    
} catch (Exception $e) {
    $_SESSION['mensagem'] = "Erro ao gerar ingresso para download: " . $e->getMessage();
    header("Location: meus_ingressos.php");
    exit();
}
?>
