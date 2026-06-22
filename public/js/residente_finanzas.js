/**
 * @file residente_finanzas.js
 * @description Interactividad para la vista de finanzas del residente.
 * - Resalta la fila del recibo más reciente al cargar la página.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Resaltar la primera fila de recibos pendientes
    const primerPendiente = document.querySelector('tbody tr:first-child td.fw-bold.text-danger');
    if (primerPendiente) {
        primerPendiente.closest('tr').style.backgroundColor = 'rgba(239, 68, 68, 0.05)';
    }
});
