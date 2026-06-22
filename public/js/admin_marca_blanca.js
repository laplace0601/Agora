/**
 * @file admin_marca_blanca.js
 * @description Reactividad e inyección dinámica de colores para la vista previa de marca blanca.
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. CAPTURA DE NODOS DEL DOM (SELECTORES Y TEXTOS)
    const inputTitle = document.getElementById('custom-title');
    const previewTitle = document.getElementById('preview-logo-text');

    const pickerPrimary = document.getElementById('color-primary');
    const textPrimary = document.getElementById('color-primary-text');
    const previewSidebar = document.getElementById('preview-sidebar');

    const pickerAccent = document.getElementById('color-accent');
    const textAccent = document.getElementById('color-accent-text');
    const previewActiveLink = document.getElementById('preview-active-link');

    // 2. REACTIVIDAD EN TIEMPO REAL: CAMBIO DE TEXTO
    if (inputTitle && previewTitle) {
        inputTitle.addEventListener('input', function(e) {
            previewTitle.textContent = e.target.value.toUpperCase() || 'MI CONDOMINIO';
        });
    }

    // 3. REACTIVIDAD EN TIEMPO REAL: CAMBIO DE COLOR PRIMARIO
    if (pickerPrimary && textPrimary && previewSidebar) {
        pickerPrimary.addEventListener('input', function(e) {
            const hexColor = e.target.value;
            textPrimary.value = hexColor.toUpperCase(); // Sincroniza el texto hexadecimal
            previewSidebar.style.backgroundColor = hexColor; // Muta el fondo de la simulación
        });
    }

    // 4. REACTIVIDAD EN TIEMPO REAL: CAMBIO DE COLOR DE ACENTO
    if (pickerAccent && textAccent && previewActiveLink) {
        pickerAccent.addEventListener('input', function(e) {
            const hexColor = e.target.value;
            textAccent.value = hexColor.toUpperCase(); // Sincroniza el texto hexadecimal
            previewActiveLink.style.backgroundColor = hexColor; // Muta el botón de la simulación
        });
    }
});