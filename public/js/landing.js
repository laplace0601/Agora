/**
 * Función encargada de cerrar el modal activo de forma limpia,
 * eliminar el fondo oscuro (backdrop) y desplazar la pantalla a la sección de planes.
 * @param {string} modalId - El ID del modal que se desea cerrar.
 */
function irAPlanes(modalId) {
    var miModalElemento = document.getElementById(modalId);
    var modalInstancia = bootstrap.Modal.getInstance(miModalElemento);
    
    if (modalInstancia) {
        modalInstancia.hide();
    }
    
    setTimeout(function() {
        document.getElementById('planes').scrollIntoView({ 
            behavior: 'smooth'
        });
    }, 300);
}

/**
 * Lógica de Validación JavaScript para el Formulario de Contacto
 */
(() => {
    'use strict'
    const formulario = document.querySelector('.needs-validation')

    if (formulario) {
        formulario.addEventListener('submit', event => {
            if (!formulario.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            formulario.classList.add('was-validated')
        }, false)
    }
})()