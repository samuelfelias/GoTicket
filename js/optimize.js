/**
 * Arquivo de otimização JavaScript para o GoTicket
 * Este script melhora o desempenho do site implementando:
 * - Carregamento lazy de imagens
 * - Otimização de eventos DOM
 * - Redução de reflows e repaints
 * - Suporte otimizado para dispositivos móveis
 * - Compatibilidade com formatos de imagem JPEG, JPG, PNG e GIF
 */

document.addEventListener('DOMContentLoaded', function() {
    // Implementar lazy loading para todas as imagens
    implementLazyLoading();
    
    // Otimizar eventos de scroll
    optimizeScrollEvents();
    
    // Pré-carregar recursos críticos
    preloadCriticalResources();
    
    // Otimizar imagens para dispositivos móveis
    optimizeImagesForMobile();
    
    // Adicionar suporte para visualização de imagens
    setupImagePreview();
});

/**
 * Implementa lazy loading para imagens que não têm o atributo loading="lazy"
 */
function implementLazyLoading() {
    // Verificar se o navegador suporta Intersection Observer
    if ('IntersectionObserver' in window) {
        const imgObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        // Adicionar evento para tratar erros de carregamento
                        img.onerror = function() {
                            console.warn('Erro ao carregar imagem:', img.dataset.src);
                            // Tentar carregar uma imagem de fallback
                            img.src = '../semperfil.png';
                        };
                        img.removeAttribute('data-src');
                        // Adicionar classe para animação de fade-in
                        img.classList.add('img-loaded');
                    }
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px', // Carregar imagens um pouco antes de entrarem na viewport
            threshold: 0.1 // Iniciar carregamento quando 10% da imagem estiver visível
        });

        // Selecionar todas as imagens que não têm loading="lazy"
        document.querySelectorAll('img:not([loading="lazy"])').
            forEach(img => {
                // Armazenar o src original em data-src e limpar src
                if (img.src && !img.dataset.src) {
                    img.dataset.src = img.src;
                    // Não limpar src para imagens críticas (com fetchpriority="high")
                    if (!img.getAttribute('fetchpriority') || img.getAttribute('fetchpriority') !== 'high') {
                        img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E';
                    }
                }
                // Adicionar classe para estilização
                img.classList.add('lazy-image');
                imgObserver.observe(img);
            });
    } else {
        // Fallback para navegadores que não suportam Intersection Observer
        document.querySelectorAll('img[data-src]').forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

/**
 * Otimiza eventos de scroll usando throttling
 */
function optimizeScrollEvents() {
    let scrollTimeout;
    const scrollHandlers = [];
    
    // Substituir manipuladores de eventos de scroll diretos
    window.addEventListener('scroll', function() {
        if (!scrollTimeout) {
            scrollTimeout = setTimeout(function() {
                scrollTimeout = null;
                // Executar todos os manipuladores registrados
                scrollHandlers.forEach(handler => handler());
            }, 100); // Throttle de 100ms
        }
    });
    
    // Método para adicionar manipuladores de scroll otimizados
    window.addOptimizedScrollHandler = function(callback) {
        if (typeof callback === 'function') {
            scrollHandlers.push(callback);
        }
    };
}

/**
 * Pré-carrega recursos críticos para melhorar o desempenho percebido
 */
function preloadCriticalResources() {
    // Pré-conectar a domínios externos comuns
    const domains = [
        'https://cdn.jsdelivr.net',
        'https://cdnjs.cloudflare.com'
    ];
    
    domains.forEach(domain => {
        if (!document.querySelector(`link[rel="preconnect"][href="${domain}"]`)) {
            const link = document.createElement('link');
            link.rel = 'preconnect';
            link.href = domain;
            document.head.appendChild(link);
        }
    });
}

/**
 * Otimiza imagens para dispositivos móveis
 * - Ajusta o tamanho das imagens com base no dispositivo
 * - Melhora o desempenho em conexões lentas
 * - Adiciona suporte para preview de imagens em dispositivos móveis
 */
function optimizeImagesForMobile() {
    // Detectar se é um dispositivo móvel
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // Adicionar classe ao body para estilos específicos para mobile
        document.body.classList.add('mobile-device');
        
        // Otimizar todas as imagens para dispositivos móveis
        document.querySelectorAll('img').forEach(img => {
            // Garantir que as imagens não ultrapassem a largura da tela
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            
            // Adicionar atributo loading="lazy" para navegadores que suportam
            if ('loading' in HTMLImageElement.prototype) {
                img.loading = 'lazy';
            }
            
            // Adicionar classe para estilos específicos para mobile
            img.classList.add('mobile-optimized');
        });
        
        // Adicionar meta tag para viewport
        if (!document.querySelector('meta[name="viewport"]')) {
            const meta = document.createElement('meta');
            meta.name = 'viewport';
            meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
            document.head.appendChild(meta);
        }
        
        // Otimizar inputs de arquivo para dispositivos móveis
        optimizeFileInputsForMobile();
    }
    
    // Adicionar estilos para imagens otimizadas
    addOptimizedImageStyles();
}

/**
 * Otimiza inputs de arquivo para dispositivos móveis
 */
function optimizeFileInputsForMobile() {
    // Selecionar todos os inputs de arquivo
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        // Adicionar classe para estilos específicos para mobile
        input.classList.add('mobile-optimized-input');
        
        // Adicionar label personalizado para melhorar a experiência em dispositivos móveis
        const label = document.createElement('label');
        label.htmlFor = input.id || `file-input-${Math.random().toString(36).substr(2, 9)}`;
        if (!input.id) {
            input.id = label.htmlFor;
        }
        
        label.className = 'custom-file-label';
        label.innerHTML = '<span>Escolher arquivo</span>';
        label.style.display = 'inline-block';
        label.style.padding = '8px 16px';
        label.style.backgroundColor = '#4CAF50';
        label.style.color = 'white';
        label.style.borderRadius = '4px';
        label.style.cursor = 'pointer';
        label.style.marginBottom = '10px';
        label.style.textAlign = 'center';
        
        // Esconder o input original
        input.style.opacity = '0';
        input.style.position = 'absolute';
        input.style.zIndex = '-1';
        
        // Adicionar o label antes do input
        input.parentNode.insertBefore(label, input);
        
        // Atualizar o texto do label quando um arquivo é selecionado
        input.addEventListener('change', function() {
            const fileName = this.files && this.files.length > 0 ? 
                this.files[0].name : 
                'Nenhum arquivo selecionado';
            
            const span = label.querySelector('span');
            if (span) {
                span.textContent = fileName.length > 20 ? 
                    fileName.substring(0, 17) + '...' : 
                    fileName;
            }
        });
    });
}

/**
 * Adiciona estilos CSS para imagens otimizadas
 */
function addOptimizedImageStyles() {
    // Verificar se os estilos já foram adicionados
    if (document.getElementById('optimized-image-styles')) return;
    
    const styleSheet = document.createElement('style');
    styleSheet.id = 'optimized-image-styles';
    styleSheet.textContent = `
        .lazy-image {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .img-loaded {
            opacity: 1;
        }
        
        .mobile-optimized {
            max-width: 100% !important;
            height: auto !important;
        }
        
        .mobile-device .evento-imagem img {
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .preview-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        
        .preview-container.active {
            opacity: 1;
            pointer-events: auto;
        }
        
        .preview-image {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .preview-container.active .preview-image {
            transform: scale(1);
        }
        
        .preview-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 30px;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.5);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Estilos para o preview de imagens em formulários */
        .image-preview-container {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }
        
        .image-preview {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            max-height: 200px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        /* Estilos para o input de arquivo */
        input[type="file"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            width: 100%;
        }
        
        /* Estilos para o botão de remover imagem */
        .remove-preview-button {
            display: block;
            margin: 10px auto 0;
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .remove-preview-button:hover {
            background-color: #d32f2f;
        }
        
        /* Estilos para inputs de arquivo em dispositivos móveis */
        .mobile-device .custom-file-label {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px 16px;
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .mobile-device .image-preview-container {
            margin-top: 15px;
            padding: 15px;
        }
        
        .mobile-device .image-preview {
            max-height: 250px;
        }
        
        .mobile-device .remove-preview-button {
            padding: 10px 15px;
            font-size: 16px;
            margin-top: 15px;
        }
        
        /* Estilos para o tema escuro */
        .dark-theme .image-preview-container {
            border-color: #444;
            background-color: #333;
        }
        
        .dark-theme input[type="file"] {
            border-color: #444;
            background-color: #333;
            color: #fff;
        }
        
        .dark-theme .custom-file-label {
            background-color: #2e7d32;
        }
        
        .dark-theme .remove-preview-button {
            background-color: #c62828;
        }
        
        .dark-theme .remove-preview-button:hover {
            background-color: #b71c1c;
        }
    `;
    
    document.head.appendChild(styleSheet);
}

/**
 * Configura a visualização de imagens em tela cheia e preview de imagens em formulários
 */
function setupImagePreview() {
    // Criar container para preview
    const previewContainer = document.createElement('div');
    previewContainer.className = 'preview-container';
    
    const previewImage = document.createElement('img');
    previewImage.className = 'preview-image';
    
    const closeButton = document.createElement('div');
    closeButton.className = 'preview-close';
    closeButton.innerHTML = '&times;';
    closeButton.addEventListener('click', () => {
        previewContainer.classList.remove('active');
    });
    
    previewContainer.appendChild(previewImage);
    previewContainer.appendChild(closeButton);
    document.body.appendChild(previewContainer);
    
    // Fechar preview ao clicar fora da imagem
    previewContainer.addEventListener('click', (e) => {
        if (e.target === previewContainer) {
            previewContainer.classList.remove('active');
        }
    });
    
    // Adicionar evento de clique para todas as imagens de eventos
    document.querySelectorAll('.evento-imagem img, .evento-card img').forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', () => {
            previewImage.src = img.src;
            previewContainer.classList.add('active');
        });
    });
    
    // Adicionar evento para imagens que são carregadas dinamicamente
    document.addEventListener('DOMNodeInserted', (e) => {
        if (e.target.tagName === 'IMG' && 
            (e.target.closest('.evento-imagem') || e.target.closest('.evento-card'))) {
            e.target.style.cursor = 'pointer';
            e.target.addEventListener('click', () => {
                previewImage.src = e.target.src;
                previewContainer.classList.add('active');
            });
        }
    });
    
    // Configurar preview para inputs de arquivo de imagem
    setupFileInputPreview();
}

/**
 * Configura o preview de imagens para inputs de arquivo
 */
function setupFileInputPreview() {
    // Procurar por todos os inputs de arquivo que aceitam imagens
    const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"], input[type="file"][id="imagem"]');
    
    fileInputs.forEach(input => {
        // Criar ou encontrar o container de preview
        let previewContainer = document.getElementById(`${input.id}-preview`);
        
        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.id = `${input.id}-preview`;
            previewContainer.className = 'image-preview-container';
            previewContainer.style.marginTop = '10px';
            previewContainer.style.display = 'none';
            
            const previewImg = document.createElement('img');
            previewImg.className = 'image-preview';
            previewImg.style.maxWidth = '100%';
            previewImg.style.maxHeight = '200px';
            previewImg.style.borderRadius = '4px';
            previewImg.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
            
            // Adicionar botão para remover a imagem
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'remove-preview-button';
            removeButton.innerHTML = 'Remover imagem';
            removeButton.style.marginTop = '5px';
            removeButton.style.padding = '5px 10px';
            removeButton.style.backgroundColor = '#f44336';
            removeButton.style.color = 'white';
            removeButton.style.border = 'none';
            removeButton.style.borderRadius = '4px';
            removeButton.style.cursor = 'pointer';
            removeButton.style.display = 'none';
            
            removeButton.addEventListener('click', function() {
                // Limpar o input de arquivo
                input.value = '';
                previewContainer.style.display = 'none';
                this.style.display = 'none';
                
                // Disparar evento de change para atualizar qualquer listener
                const event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            });
            
            previewContainer.appendChild(previewImg);
            previewContainer.appendChild(removeButton);
            input.parentNode.insertBefore(previewContainer, input.nextSibling);
        }
        
        // Adicionar evento de mudança para mostrar o preview
        input.addEventListener('change', function() {
            const previewImg = previewContainer.querySelector('img');
            const removeButton = previewContainer.querySelector('.remove-preview-button');
            
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Verificar se o arquivo é uma imagem válida
                if (!file.type.match('image.*')) {
                    alert('Por favor, selecione uma imagem válida (JPEG, PNG, GIF).');
                    this.value = '';
                    return;
                }
                
                // Verificar o tamanho do arquivo (máximo 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('A imagem é muito grande. O tamanho máximo é 5MB.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                    if (removeButton) {
                        removeButton.style.display = 'block';
                    }
                };
                
                reader.onerror = function() {
                    alert('Erro ao ler o arquivo. Por favor, tente novamente.');
                    input.value = '';
                };
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                if (removeButton) {
                    removeButton.style.display = 'none';
                }
            }
        });
        
        // Verificar se já existe um arquivo selecionado (para casos de recarregamento da página)
        if (input.files && input.files[0]) {
            // Disparar o evento change manualmente
            const event = new Event('change', { bubbles: true });
            input.dispatchEvent(event);
        }
    });
}