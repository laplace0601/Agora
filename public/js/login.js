/**
 * Lógica de Validación JavaScript para el Formulario de Login
 */
(() => {
    'use strict'
    const formulario = document.querySelector('.needs-validation')

    if (formulario) {
        formulario.addEventListener('submit', event => {
            if (!formulario.checkValidity()) {
                event.preventDefault(); 
                event.stopPropagation(); 
            }
            formulario.classList.add('was-validated');
        }, false);
    }
})()