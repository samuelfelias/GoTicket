<?php
// Iniciar sessão
session_start();

// Não é necessário verificar login para visualizar eventos

// Incluir arquivo de conexão com o banco de dados
require_once '../config/database.php';
require_once '../config/cache.php';
require_once '../includes/verificar_eventos_expirados.php';
$conexao = conectarBD();

// Atualizar status de eventos expirados
atualizarEventosExpirados($conexao);
// Deletar eventos expirados automaticamente
deletarEventosExpirados($conexao);

// Parâmetros de busca
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$data = isset($_GET['data']) ? $_GET['data'] : '';
$local = isset($_GET['local']) ? $_GET['local'] : '';

// Parâmetros de paginação
$eventos_por_pagina = 6;
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $eventos_por_pagina;

// Construir a consulta SQL base
$sql = "SELECT e.*, u.nome as organizador_nome 
        FROM evento e 
        INNER JOIN usuario u ON e.id_organizador = u.id_usuario 
        WHERE e.status = 'ATIVO'";

// Adicionar filtros se fornecidos
$params = [];

if (!empty($busca)) {
    $sql .= " AND e.nome LIKE ?";
    $params[] = "%$busca%";
}

if (!empty($data)) {
    $sql .= " AND e.data = ?";
    $params[] = $data;
}

if (!empty($local)) {
    // Buscar em todos os campos de endereço
    $sql .= " AND (e.cidade LIKE ? OR e.bairro LIKE ? OR e.rua LIKE ?)";
    $params[] = "%$local%";
    $params[] = "%$local%";
    $params[] = "%$local%";
}

// Ordenar por data
$sql .= " ORDER BY e.data ASC";

// Adicionar paginação
$sql .= " LIMIT ? OFFSET ?";
$params[] = $eventos_por_pagina;
$params[] = $offset;

// Usar cache para consultas sem filtros (mais comuns)
$cache_key = 'eventos_lista_' . md5($busca . $data . $local . $pagina_atual);
$cache_ttl = 300; // 5 minutos

$eventos = cacheQuery($cache_key, function() use ($conexao, $sql, $params) {
    $stmt = $conexao->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}, $cache_ttl);

// Contar total de eventos para paginação (com cache)
$cache_count_key = 'eventos_count_' . md5($busca . $data . $local);
$total_eventos = cacheQuery($cache_count_key, function() use ($conexao, $busca, $data, $local) {
    $sql_count = "SELECT COUNT(*) as total 
                  FROM evento e 
                  INNER JOIN usuario u ON e.id_organizador = u.id_usuario 
                  WHERE e.status = 'ATIVO'";

    $params_count = [];
    if (!empty($busca)) {
        $sql_count .= " AND e.nome LIKE ?";
        $params_count[] = "%$busca%";
    }
    if (!empty($data)) {
        $sql_count .= " AND e.data = ?";
        $params_count[] = $data;
    }
    if (!empty($local)) {
        $sql_count .= " AND (e.cidade LIKE ? OR e.bairro LIKE ? OR e.rua LIKE ?)";
        $params_count[] = "%$local%";
        $params_count[] = "%$local%";
        $params_count[] = "%$local%";
    }

    $stmt_count = $conexao->prepare($sql_count);
    $stmt_count->execute($params_count);
    return $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
}, $cache_ttl);

$total_paginas = ceil($total_eventos / $eventos_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Disponíveis - GoTicket</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .eventos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .evento-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 15px;
            transition: transform 0.3s;
        }
        
        .evento-card:hover {
            transform: translateY(-5px);
        }
        
        .evento-data {
            color: #3498db;
            font-weight: bold;
        }
        
        .evento-local {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .evento-descricao {
            margin: 10px 0;
            color: #555;
        }
        
        .evento-organizador {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        .search-form {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .search-form .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .search-form .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
        }
        
        .btn-ver-detalhes {
            background-color: #2ecc71;
        }
        
        .btn-ver-detalhes:hover {
            background-color: #27ae60;
        }
        
        .no-events {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .pagination-info {
            color: #666;
            font-size: 14px;
        }
        
        .pagination-links {
            display: flex;
            gap: 5px;
        }
        
        .btn-pagination {
            padding: 8px 12px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .btn-pagination:hover {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        
        .btn-pagination.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="panel-container">
            <h2 class="panel-title" data-i18n="h.available_events">Eventos Disponíveis</h2>
            
            <!-- Formulário de busca -->
            <div class="search-form">
                <form action="listar_eventos.php" method="GET">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="busca" data-i18n="label.search_event_name">Nome do evento:</label>
                            <input type="text" id="busca" name="busca" class="form-control" value="<?php echo htmlspecialchars($busca); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="data" data-i18n="label.date">Data:</label>
                            <input type="date" id="data" name="data" class="form-control" value="<?php echo htmlspecialchars($data); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="local" data-i18n="label.location">Local:</label>
                                <input type="text" id="local" name="local" class="form-control" value="<?php echo htmlspecialchars($local); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn" data-i18n="btn.search">Buscar</button>
                    <a href="listar_eventos.php" class="btn" style="background-color: #95a5a6;" data-i18n="btn.clear_filters">Limpar Filtros</a>
                </form>
            </div>
            
            <?php if (count($eventos) > 0): ?>
                <div class="eventos-grid">
                    <?php foreach ($eventos as $evento): ?>
                        <div class="evento-card">
                            <h3><?php echo htmlspecialchars($evento['nome']); ?></h3>
                            <p class="evento-data">
                                <?php 
                                $data_formatada = '';
                                if (!empty($evento['data'])) {
                                    $ts_data = strtotime($evento['data']);
                                    if ($ts_data !== false) {
                                        $data_formatada = date('d/m/Y', $ts_data);
                                    }
                                }

                                $hora_formatada = '';
                                if (!empty($evento['horario'])) {
                                    $ts_hora = strtotime($evento['horario']);
                                    if ($ts_hora !== false) {
                                        $hora_formatada = date('H:i', $ts_hora);
                                    }
                                }

                                echo $data_formatada . (!empty($hora_formatada) ? ' às ' . $hora_formatada : ''); 
                                ?>
                            </p>
                            <p class="evento-local"><?php 
                if (isset($evento['cidade']) && isset($evento['bairro'])) {
                    echo htmlspecialchars($evento['cidade']) . ' - ' . htmlspecialchars($evento['bairro']);
                } else if (isset($evento['local'])) {
                    // Compatibilidade com eventos antigos
                    echo htmlspecialchars($evento['local']);
                } else {
                    echo "Local não disponível";
                }
                ?></p>
                            <p class="evento-descricao">
                                <?php 
                                $descricao = $evento['descricao'];
                                echo !empty($descricao) ? htmlspecialchars(substr($descricao, 0, 100)) . '...' : 'Sem descrição disponível.'; 
                                ?>
                            </p>
                            <p class="evento-organizador">Organizado por: <?php echo htmlspecialchars($evento['organizador_nome']); ?></p>
                            <a href="detalhes_evento.php?id=<?php echo $evento['id_evento']; ?>" class="btn btn-ver-detalhes" data-i18n="btn.view_details">Ver Detalhes</a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($total_paginas > 1): ?>
                    <div class="pagination">
                        <div class="pagination-info">
                            Mostrando <?php echo count($eventos); ?> de <?php echo $total_eventos; ?> eventos
                        </div>
                        <div class="pagination-links">
                            <?php if ($pagina_atual > 1): ?>
                                <?php 
                                $params = $_GET;
                                $params['pagina'] = $pagina_atual - 1;
                                ?>
                                <a href="listar_eventos.php?<?php echo http_build_query($params); ?>" class="btn-pagination">« Anterior</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++): ?>
                                <?php 
                                $params = $_GET;
                                $params['pagina'] = $i;
                                ?>
                                <a href="listar_eventos.php?<?php echo http_build_query($params); ?>" 
                                   class="btn-pagination <?php echo $i == $pagina_atual ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($pagina_atual < $total_paginas): ?>
                                <?php 
                                $params = $_GET;
                                $params['pagina'] = $pagina_atual + 1;
                                ?>
                                <a href="listar_eventos.php?<?php echo http_build_query($params); ?>" class="btn-pagination">Próxima »</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-events">
                    <p>Nenhum evento encontrado com os critérios de busca.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
