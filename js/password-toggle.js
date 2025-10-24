// Funcionalidade para mostrar/ocultar senha
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            // Alterna o tipo do input entre password e text
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                this.setAttribute('title', 'Ocultar senha');
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fa fa-eye"></i>';
                this.setAttribute('title', 'Mostrar senha');
            }
        });
    });
});