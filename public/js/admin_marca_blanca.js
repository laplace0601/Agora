/**
 * @file admin_marca_blanca.js
 * @description Reactividad e inyección dinámica de colores y previsualización de imágenes para la vista previa de marca blanca.
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

    // 5. PREVISUALIZACIÓN INSTANTÁNEA PARA EL LOGO
    const logoInput = document.getElementById('logo-input');
    const logoContainer = document.getElementById('logo-preview-container');
    const logoImg = document.getElementById('logo-preview');
    const logoFilename = document.getElementById('logo-filename');
    const logoLabel = document.getElementById('logo-label');

    if (logoInput) {
        logoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                if (logoFilename) logoFilename.textContent = file.name + ' (' + sizeMb + ' MB)';
                if (logoLabel) logoLabel.innerHTML = 'Archivo seleccionado: <strong class="text-success">' + file.name + '</strong>';

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (logoImg) logoImg.src = e.target.result;
                    if (logoContainer) logoContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 6. PREVISUALIZACIÓN INSTANTÁNEA PARA EL FONDO DEL LOGIN
    const bgInput = document.getElementById('login-bg-input');
    const bgContainer = document.getElementById('login-bg-preview-container');
    const bgImg = document.getElementById('login-bg-preview');
    const bgFilename = document.getElementById('login-bg-filename');
    const bgLabel = document.getElementById('login-bg-label');

    if (bgInput) {
        bgInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                if (bgFilename) bgFilename.textContent = file.name + ' (' + sizeMb + ' MB)';
                if (bgLabel) bgLabel.innerHTML = 'Archivo seleccionado: <strong class="text-success">' + file.name + '</strong>';

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (bgImg) bgImg.src = e.target.result;
                    if (bgContainer) bgContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});