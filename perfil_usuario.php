<?php
// Iniciar sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Incluir arquivo de conexão com o banco de dados
require_once 'config/database.php';
$conexao = conectarBD();

// Obter dados do usuário
$id_usuario = $_SESSION['usuario_id'];
$sql_usuario = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt_usuario = $conexao->prepare($sql_usuario);
$stmt_usuario->execute([$id_usuario]);
$usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

// Processar upload de foto de perfil
$mensagem = "";

// Processar remoção de foto de perfil
if (isset($_POST['remover_foto'])) {
    // Verificar se o usuário tem uma foto de perfil
    if (!empty($usuario['foto_perfil'])) {
        // Remover o arquivo físico se existir
        if (file_exists($usuario['foto_perfil'])) {
            unlink($usuario['foto_perfil']);
        }
        
        // Atualizar o banco de dados
        $sql_remover_foto = "UPDATE usuario SET foto_perfil = NULL WHERE id_usuario = ?";
        $stmt_remover_foto = $conexao->prepare($sql_remover_foto);
        
        if ($stmt_remover_foto->execute([$id_usuario])) {
            $mensagem = "<div class='alert alert-success'>Foto de perfil removida com sucesso!</div>";
            $usuario['foto_perfil'] = null;
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao remover foto de perfil.</div>";
        }
    }
}

// Ajuste: permitir auto-submit do upload sem depender do botão "atualizar_foto"
if ((isset($_POST['atualizar_foto'])) || (isset($_FILES['foto_perfil']) && isset($_FILES['foto_perfil']['tmp_name']) && $_FILES['foto_perfil']['error'] === 0)) {
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
        $diretorio_upload = "uploads/perfil/";
        if (!file_exists($diretorio_upload)) {
            mkdir($diretorio_upload, 0777, true);
        }
        $nome_arquivo = time() . '_' . $_FILES['foto_perfil']['name'];
        $caminho_arquivo = $diretorio_upload . $nome_arquivo;
        $tipos_permitidos = array('image/jpeg', 'image/png', 'image/gif');
        if (in_array($_FILES['foto_perfil']['type'], $tipos_permitidos)) {
            // Remover foto antiga se existir
            if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])) {
                unlink($usuario['foto_perfil']);
            }
            
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_arquivo)) {
                $sql_atualizar_foto = "UPDATE usuario SET foto_perfil = ? WHERE id_usuario = ?";
                $stmt_atualizar_foto = $conexao->prepare($sql_atualizar_foto);
                if ($stmt_atualizar_foto->execute([$caminho_arquivo, $id_usuario])) {
                    $mensagem = "<div class='alert alert-success'>Foto de perfil atualizada com sucesso!</div>";
                    $usuario['foto_perfil'] = $caminho_arquivo;
                } else {
                    $mensagem = "<div class='alert alert-danger'>Erro ao atualizar foto de perfil no banco de dados.</div>";
                }
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao fazer upload da foto.</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-danger'>Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Selecione uma foto para upload.</div>";
    }
}

// Processar atualização de preferências
if (isset($_POST['atualizar_preferencias'])) {
    // Primeiro, remover todas as preferências atuais do usuário
    $sql_remover = "DELETE FROM preferenciausuario WHERE id_usuario = ?";
    $stmt_remover = $conexao->prepare($sql_remover);
    $stmt_remover->execute([$id_usuario]);
    
    // Depois, adicionar as novas preferências selecionadas
    if (isset($_POST['preferencias']) && is_array($_POST['preferencias'])) {
        $sql_adicionar = "INSERT INTO preferenciausuario (id_usuario, id_tipo_evento) VALUES (?, ?)";
        $stmt_adicionar = $conexao->prepare($sql_adicionar);
        
        foreach ($_POST['preferencias'] as $id_tipo_evento) {
            $stmt_adicionar->execute([$id_usuario, $id_tipo_evento]);
        }
        
        $mensagem = "<div class='alert alert-success'>Preferências atualizadas com sucesso!</div>";
    }
}

// Buscar todos os tipos de eventos
$sql_tipos = "SELECT * FROM tipoevento ORDER BY nome";
$stmt_tipos = $conexao->prepare($sql_tipos);
$stmt_tipos->execute();
$tipos_eventos = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);

// Buscar preferências do usuário
$sql_preferencias = "SELECT id_tipo_evento FROM preferenciausuario WHERE id_usuario = ?";
$stmt_preferencias = $conexao->prepare($sql_preferencias);
$stmt_preferencias->execute([$id_usuario]);
$preferencias_data = $stmt_preferencias->fetchAll(PDO::FETCH_ASSOC);

$preferencias_usuario = array();
foreach ($preferencias_data as $row) {
    $preferencias_usuario[] = $row['id_tipo_evento'];
}

// Título da página
$titulo = "Perfil do Usuário";

// Incluir cabeçalho
include 'includes/header.php';
?>

<div class="container mt-4 fade-in">
    <h1 class="text-center mb-4">Meu Perfil</h1>
    
    <?php if (!empty($mensagem)): ?>
    <div class="alert-container mb-4 fade-in">
        <?php echo $mensagem; ?>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Coluna da esquerda - Informações do usuário e foto -->
        <div class="col-md-4 mb-4">
            <div class="card panel-container slide-in-left" style="animation-delay: 0.2s;">
                <div class="card-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Informações Pessoais</h5>
                </div>
                <div class="card-body text-center">
                    <!-- Foto de perfil -->
                    <div class="profile-photo-container mb-4">
                        <div class="profile-photo-wrapper">
                            <?php if (!empty($usuario['foto_perfil'])): ?>
                                <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de Perfil" class="profile-photo" loading="lazy" decoding="async" fetchpriority="high">
                            <?php else: ?>
                                <img src="semperfil.png" alt="Sem Foto" class="profile-photo" loading="lazy" decoding="async" fetchpriority="high">
                            <?php endif; ?>
                            
                            <!-- Overlay para efeito de hover -->
                            <div class="profile-photo-overlay">
                                <div class="overlay-content">
                                    <i class="fas fa-camera fa-2x mb-2 pulse-icon"></i>
                                    <span>Alterar foto</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botões de ação para a foto -->
                        <div class="photo-actions">
                            <!-- Botão de upload com design melhorado -->
                            <label for="foto_perfil" class="photo-upload-btn" title="Alterar foto de perfil">
                                <i class="fas fa-camera"></i>
                                <span class="btn-text">Alterar</span>
                            </label>
                            
                            <?php if (!empty($usuario['foto_perfil'])): ?>
                            <!-- Botão para remover foto -->
                            <button type="button" class="photo-remove-btn" title="Remover foto de perfil" onclick="document.getElementById('formRemoverFoto').submit();">
                                <i class="fas fa-trash"></i>
                                <span class="btn-text">Remover</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Formulário de upload de foto (auto-submit no change) -->
                    <form method="post" enctype="multipart/form-data" id="formFotoPerfil" class="d-none">
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" onchange="document.getElementById('formFotoPerfil').submit()">
                    </form>
                    
                    <!-- Formulário para remover foto -->
                    <form method="post" id="formRemoverFoto" class="d-none">
                        <input type="hidden" name="remover_foto" value="1">
                    </form>
                    
                    <hr class="mb-4">
                    
                    <!-- Informações do usuário -->
                    <div class="user-info">
                        <div class="user-info-item">
                            <span class="user-info-label"><i class="fas fa-user me-2"></i>Nome:</span>
                            <span class="user-info-value"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label"><i class="fas fa-envelope me-2"></i>Email:</span>
                            <span class="user-info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label"><i class="fas fa-id-badge me-2"></i>Tipo:</span>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($usuario['tipo']); ?></span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label"><i class="fas fa-crown me-2"></i>Plano:</span>
                            <span class="badge <?php echo $usuario['plano'] == 'GOLD' ? 'bg-warning text-dark' : 'bg-secondary'; ?>">
                                <?php echo htmlspecialchars($usuario['plano']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="pagamentos.php" class="btn btn-success btn-lg" style="border-radius: 30px; padding: 10px 30px; width: 100%; font-weight: bold; box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);">
                            <?php if ($usuario['plano'] == 'GOLD'): ?>
                                <i class="fas fa-cog me-2"></i>Gerenciar Plano
                            <?php else: ?>
                                <i class="fas fa-arrow-circle-up me-2"></i>Atualizar para Gold
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Coluna da direita - Preferências de eventos -->
        <div class="col-md-8">
            <div class="card panel-container slide-in-right" style="animation-delay: 0.3s;">
                <div class="card-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="card-title mb-0"><i class="fas fa-heart me-2"></i>Preferências de Eventos</h5>
                </div>
                <div class="card-body">
                    <form method="post" id="formPreferencias">
                        <p class="lead mb-4">Selecione os tipos de eventos que você tem interesse:</p>

                        <div class="preferences-toolbar d-flex flex-wrap align-items-center gap-2 mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control search-input border-start-0" id="filtroPreferencias" 
                                       placeholder="Filtrar tipos de eventos..." aria-label="Filtrar tipos de eventos">
                            </div>
                            <div class="ms-auto d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" type="button" id="btnSelecionarTodos">
                                    <i class="fas fa-check-double me-1"></i> Selecionar todos
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="btnLimpar">
                                    <i class="fas fa-times me-1"></i> Limpar
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <span class="badge bg-info text-dark" id="contadorSelecionados"></span>
                        </div>
                        
                        <div class="row" id="listaPreferencias">
                            <?php foreach ($tipos_eventos as $tipo): ?>
                                <?php $checked = in_array($tipo['id_tipo_evento'], $preferencias_usuario); ?>
                                <div class="col-md-6 mb-3 preferencia-item" data-nome="<?php echo htmlspecialchars(strtolower($tipo['nome'])); ?>">
                                    <div class="form-check custom-checkbox <?php echo $checked ? 'is-checked' : ''; ?>" style="padding: 12px 15px; border-radius: 10px; transition: all 0.3s ease;">
                                        <input class="form-check-input" type="checkbox" 
                                               name="preferencias[]" 
                                               value="<?php echo $tipo['id_tipo_evento']; ?>" 
                                               id="tipo_<?php echo $tipo['id_tipo_evento']; ?>"
                                               <?php echo $checked ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="tipo_<?php echo $tipo['id_tipo_evento']; ?>" style="cursor: pointer; font-weight: <?php echo $checked ? '600' : '400'; ?>; transition: all 0.3s ease;">
                                            <?php echo htmlspecialchars($tipo['nome']); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="text-center mt-5">
                            <button type="submit" name="atualizar_preferencias" id="btnSalvarPreferencias" class="btn btn-primary btn-lg" style="border-radius: 30px; padding: 12px 50px; font-weight: bold; box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);">
                                <i class="fas fa-save me-2"></i>
                                <span class="btn-text">Salvar Preferências</span>
                                <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// UX aprimorada: filtro de preferências, selecionar/limpar, contador dinâmico e animações
document.addEventListener('DOMContentLoaded', function() {
  // Elementos DOM
  const filtro = document.getElementById('filtroPreferencias');
  const itens = Array.from(document.querySelectorAll('#listaPreferencias .preferencia-item'));
  const checkboxes = Array.from(document.querySelectorAll('#listaPreferencias .form-check-input'));
  const btnTodos = document.getElementById('btnSelecionarTodos');
  const btnLimpar = document.getElementById('btnLimpar');
  const contador = document.getElementById('contadorSelecionados');
  const formPrefs = document.getElementById('formPreferencias');
  const btnSalvar = document.getElementById('btnSalvarPreferencias');
  const btnText = btnSalvar?.querySelector('.btn-text');
  const spinner = btnSalvar?.querySelector('.spinner-border');
  const photoUploadBtn = document.querySelector('.photo-upload-btn');
  const photoInput = document.getElementById('foto_perfil');
  
  // Função para atualizar o contador de preferências selecionadas
  function atualizarContador() {
    if (!contador || !checkboxes.length) return;
    
    const total = checkboxes.length;
    const marcados = checkboxes.filter(c => c.checked).length;
    contador.textContent = `Selecionados: ${marcados} de ${total}`;
    
    // Atualizar estilo do contador baseado na quantidade selecionada
    if (marcados === 0) {
      contador.className = 'badge bg-secondary';
    } else if (marcados < total / 3) {
      contador.className = 'badge bg-info text-dark';
    } else if (marcados < total * 2/3) {
      contador.className = 'badge bg-primary';
    } else if (marcados < total) {
      contador.className = 'badge bg-success';
    } else {
      contador.className = 'badge bg-warning text-dark';
    }
    
    // Animação sutil
    contador.animate([{transform: 'scale(1.1)'}, {transform: 'scale(1)'}], {
      duration: 300,
      easing: 'ease-out'
    });
  }

  // Função para atualizar estilos dos checkboxes
  function atualizarStyles() {
    checkboxes.forEach(cb => {
      const container = cb.closest('.custom-checkbox');
      const label = cb.nextElementSibling;
      
      if (!container || !label) return;
      
      container.classList.toggle('is-checked', cb.checked);
      label.style.fontWeight = cb.checked ? '600' : '400';
    });
  }

  // Filtro de preferências com debounce para melhor performance
  let timeoutId;
  filtro?.addEventListener('input', function() {
    clearTimeout(timeoutId);
    
    timeoutId = setTimeout(() => {
      const termo = this.value.trim().toLowerCase();
      let encontrados = 0;
      
      itens.forEach(item => {
        const nome = item.getAttribute('data-nome');
        const match = !termo || nome.includes(termo);
        item.style.display = match ? '' : 'none';
        if (match) encontrados++;
        
        // Animação sutil para os itens que aparecem
        if (match && termo) {
          item.animate([{opacity: '0.7'}, {opacity: '1'}], {
            duration: 300,
            easing: 'ease-out'
          });
        }
      });
      
      // Feedback visual quando não há resultados
      const listaContainer = document.getElementById('listaPreferencias');
      const msgNaoEncontrado = document.getElementById('msgNaoEncontrado');
      
      if (encontrados === 0 && termo) {
        if (!msgNaoEncontrado) {
          const msg = document.createElement('div');
          msg.id = 'msgNaoEncontrado';
          msg.className = 'alert alert-info text-center w-100 mt-3';
          msg.innerHTML = `<i class="fas fa-info-circle me-2"></i>Nenhum tipo de evento encontrado com "${termo}"`;
          listaContainer.appendChild(msg);
        }
      } else if (msgNaoEncontrado) {
        msgNaoEncontrado.remove();
      }
    }, 300); // Debounce de 300ms
  });

  // Selecionar todos os itens visíveis
  btnTodos?.addEventListener('click', function() {
    let alterados = false;
    
    itens.forEach(item => {
      if (item.style.display !== 'none') {
        const cb = item.querySelector('.form-check-input');
        if (cb && !cb.checked) {
          cb.checked = true;
          alterados = true;
          
          // Animação para destacar a mudança
          const container = cb.closest('.custom-checkbox');
          if (container) {
            container.animate([{backgroundColor: '#d1ecf1'}, {backgroundColor: '#e8f4fd'}], {
              duration: 400,
              easing: 'ease-out'
            });
          }
        }
      }
    });
    
    if (alterados) {
      atualizarStyles();
      atualizarContador();
      
      // Feedback visual
      btnTodos.animate([{transform: 'scale(1.1)'}, {transform: 'scale(1)'}], {
        duration: 200,
        easing: 'ease-out'
      });
    }
  });

  // Limpar todas as seleções
  btnLimpar?.addEventListener('click', function() {
    let alterados = false;
    
    checkboxes.forEach(cb => {
      if (cb.checked) {
        cb.checked = false;
        alterados = true;
      }
    });
    
    if (alterados) {
      atualizarStyles();
      atualizarContador();
      
      // Feedback visual
      btnLimpar.animate([{transform: 'scale(1.1)'}, {transform: 'scale(1)'}], {
        duration: 200,
        easing: 'ease-out'
      });
    }
  });

  // Atualizar estilos quando um checkbox é alterado
  checkboxes.forEach(cb => cb.addEventListener('change', () => {
    const container = cb.closest('.custom-checkbox');
    
    // Animação para destacar a mudança
    if (container) {
      const color = cb.checked ? '#e8f4fd' : '#f8f9fa';
      container.animate([{backgroundColor: cb.checked ? '#d1ecf1' : '#e9ecef'}, {backgroundColor: color}], {
        duration: 300,
        easing: 'ease-out'
      });
    }
    
    atualizarStyles();
    atualizarContador();
  }));

  // Feedback visual ao enviar o formulário
  formPrefs?.addEventListener('submit', function() {
    btnSalvar.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Salvando...';
    
    // Adicionar classe para feedback visual
    btnSalvar.classList.add('pulse-animation');
  });
  
  // Melhorar acessibilidade do botão de upload de foto
  photoUploadBtn?.addEventListener('click', function() {
    photoInput?.click();
  });
  
  // Efeito de hover para a foto de perfil
  const profilePhoto = document.querySelector('.profile-photo');
  const photoActions = document.querySelector('.photo-actions');
  const photoOverlay = document.querySelector('.profile-photo-overlay');
  
  if (profilePhoto && photoActions) {
    const photoContainer = document.querySelector('.profile-photo-container');
    
    // Mostrar ações ao passar o mouse
    photoContainer?.addEventListener('mouseenter', function() {
      photoActions.classList.add('visible');
    });
    
    photoContainer?.addEventListener('mouseleave', function() {
      photoActions.classList.remove('visible');
    });
    
    // Adicionar acessibilidade por teclado
    photoContainer?.addEventListener('focusin', function() {
      photoActions.classList.add('visible');
    });
    
    photoContainer?.addEventListener('focusout', function(e) {
      // Verificar se o foco ainda está dentro do container
      if (!photoContainer.contains(e.relatedTarget)) {
        photoActions.classList.remove('visible');
      }
    });
    
    // Permitir clicar no overlay para abrir o seletor de arquivo
    photoOverlay?.addEventListener('click', function() {
      photoInput?.click();
    });
    
    // Adicionar efeito de feedback ao clicar no container da foto
    photoContainer?.addEventListener('click', function() {
      // Adicionar classe para efeito visual temporário
      this.classList.add('clicked');
      
      // Remover a classe após a animação
      setTimeout(() => {
        this.classList.remove('clicked');
      }, 300);
      
      // Abrir o seletor de arquivo
      photoInput?.click();
    });
  }
  
  // Confirmação para remover foto
  const photoRemoveBtn = document.querySelector('.photo-remove-btn');
  if (photoRemoveBtn) {
    photoRemoveBtn.addEventListener('click', function(e) {
      if (!confirm('Tem certeza que deseja remover sua foto de perfil?')) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  }

  // Inicializar
  atualizarStyles();
  atualizarContador();
  
  // Adicionar classes de animação aos elementos principais
  document.querySelectorAll('.panel-container').forEach((panel, index) => {
    panel.classList.add('fade-in');
    panel.style.animationDelay = `${index * 0.1}s`;
  });
});
</script>

<style>
/* Estilos específicos para a página */
.pulse-animation {
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(67, 97, 238, 0); }
  100% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0); }
}

/* Animação para o ícone de câmera no overlay */
@keyframes pulseIcon {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

/* Estilos para o container da foto de perfil */
.profile-photo-container {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto 20px;
  border-radius: 50%;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  cursor: pointer;
}

.profile-photo-container.clicked {
  animation: clickEffect 0.3s ease;
}

@keyframes clickEffect {
  0% { transform: scale(1); }
  50% { transform: scale(0.95); }
  100% { transform: scale(1); }
}

.profile-photo-wrapper {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--primary-color);
  transition: all 0.3s ease;
  position: relative;
}

/* Estilo para garantir que a foto seja circular e tenha tamanho adequado */
.profile-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: all 0.3s ease;
}

/* Overlay para efeito de hover */
.profile-photo-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.3s ease;
  border-radius: 50%;
  color: white;
  text-align: center;
}

.overlay-content {
  padding: 10px;
}

.pulse-icon {
  animation: pulseIcon 2s infinite;
  color: white;
}

.profile-photo-container:hover .profile-photo-overlay {
  opacity: 1;
}

/* Botões de ação para a foto */
.photo-actions {
  position: absolute;
  bottom: -15px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 15px;
  opacity: 0;
  transition: all 0.3s ease;
  width: auto;
  z-index: 10;
}

.photo-upload-btn,
.photo-remove-btn {
  background-color: var(--primary-color);
  color: white;
  border-radius: 20px;
  padding: 8px 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0.9;
  transition: all 0.3s ease;
  border: none;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  font-size: 14px;
  transform: translateY(10px);
}

.photo-upload-btn i,
.photo-remove-btn i {
  margin-right: 5px;
}

.photo-remove-btn {
  background-color: #dc3545;
}

.profile-photo-container:hover .photo-actions,
.photo-actions.visible {
  opacity: 1;
  bottom: -25px;
}

.photo-upload-btn:hover,
.photo-remove-btn:hover {
  opacity: 1;
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.profile-photo-container:hover .profile-photo-wrapper {
  border-color: var(--primary-hover);
}

.profile-photo-container:hover .photo-actions .photo-upload-btn,
.profile-photo-container:hover .photo-actions .photo-remove-btn,
.photo-actions.visible .photo-upload-btn,
.photo-actions.visible .photo-remove-btn {
  transform: translateY(0);
}

/* Estilo para tema escuro */
.dark-theme .profile-photo-wrapper {
  border-color: var(--primary-hover);
}

.dark-theme .photo-upload-btn,
.dark-theme .photo-remove-btn {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.dark-theme .profile-photo-overlay {
  background-color: rgba(0, 0, 0, 0.7);
}
</style>

<?php
// Incluir rodapé
include 'includes/footer.php';
?>
