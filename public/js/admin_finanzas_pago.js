/**
 * @file admin_finanzas_pago.js
 * @description Manejo interactivo del estado de validación de bauchers de pago en el panel administrativo.
 * @version 1.1.0
 */

function actualizarEstado(id, accion) {
    // Captura del nodo de la etiqueta (badge) correspondiente al registro modificado
    const etiqueta = document.getElementById('badge-' + id);
    if (!etiqueta) return; // Cláusula de salvaguarda en caso de que el ID no exista en el DOM
    
    // Reseteo completo de clases dinámicas anteriores para evitar colisiones de color
    etiqueta.className = 'badge rounded-pill px-3 py-1.5 fw-semibold transition-all-custom';
    
    // Evaluación semántica de la acción disparada por el administrador
    if (accion === 'aprobado') {
        // Mutación de contenido e inyección de clases premium aprobadas
        etiqueta.textContent = 'Validado';
        etiqueta.classList.add('badge-agora-success');
    } else if (accion === 'rechazado') {
        // Mutación de contenido e inyección de clases premium rechazadas
        etiqueta.textContent = 'Rechazado';
        etiqueta.classList.add('badge-agora-danger');
    }
}