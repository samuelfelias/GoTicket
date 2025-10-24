/**
 * Script para implementar o Google Maps Places Autocomplete
 * nos campos de endereço do GoTicket
 */

// Função para carregar a API do Google Maps com callback
function loadGoogleMapsScript() {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initAutocomplete';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Erro ao carregar a API do Google Maps. O campo de endereço funcionará como texto simples.');
    };
    document.head.appendChild(script);
}

// Inicializa o autocomplete
function initAutocomplete() {
    const input = document.getElementById('local');
    if (!input) return;
    
    // Cria o objeto autocomplete
    try {
        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'],
            fields: ['formatted_address']
        });
        
        // Quando um lugar é selecionado
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (place && place.formatted_address) {
                input.value = place.formatted_address;
            }
        });
    } catch (error) {
        console.error('Erro ao inicializar o autocomplete:', error);
    }
}

// Fallback para caso a API falhe
function setupFallback() {
    const input = document.getElementById('local');
    if (!input) return;
    
    input.addEventListener('input', function() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.log('Google Maps API não carregada. Usando campo de texto padrão.');
        }
    });
}

// Carrega o script quando a página estiver pronta
document.addEventListener('DOMContentLoaded', function() {
    setupFallback();
    loadGoogleMapsScript();
});