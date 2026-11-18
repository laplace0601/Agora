/**
 * @file admin_cartelera.js
 * @description Interactividad para el tablón de anuncios del panel admin.
 * - Prevención de doble clic con spinner en el botón de publicación.
 * - Auto-dismiss de alertas flash tras 4 segundos.
 */
document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss de alertas flash (success / error)
    const alertas = document.querySelectorAll('.alert.alert-dismissible');
    alertas.forEach(alerta => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        }, 4000);
    });

    // Limpieza del modal de nuevo anuncio al cerrarse
    const modalNuevo = document.getElementById('modalNuevoAnuncio');
    if (modalNuevo) {
        modalNuevo.addEventListener('hidden.bs.modal', function () {
            this.querySelectorAll('input:not([type="hidden"]), textarea').forEach(el => el.value = '');
            const preview = document.getElementById('imagenPreview');
            if (preview) preview.classList.add('d-none');
            const btnSubmit = document.getElementById('btnPublicarAnuncio');
            const spinner = document.getElementById('spinnerPublicar');
            const icon = document.getElementById('iconoPublicar');
            const texto = document.getElementById('textoPublicar');
            if (btnSubmit) btnSubmit.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (icon) icon.classList.remove('d-none');
            if (texto) texto.textContent = 'Publicar en Cartelera';
        });
    }

    // Indicador visual / spinner al enviar formulario de publicación
    const formPublicar = document.getElementById('formPublicarAnuncio') || document.querySelector('form[action*="cartelera/publicar"]');
    if (formPublicar) {
        formPublicar.addEventListener('submit', function () {
            const btn = document.getElementById('btnPublicarAnuncio') || this.querySelector('button[type="submit"]');
            const spinner = document.getElementById('spinnerPublicar');
            const icon = document.getElementById('iconoPublicar');
            const texto = document.getElementById('textoPublicar');

            if (btn) btn.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            if (icon) icon.classList.add('d-none');
            if (texto) texto.textContent = 'Publicando...';
        });
    }

    // Deshabilitar botón de eliminar al confirmar para evitar envíos duplicados
    document.querySelectorAll('form[action*="cartelera/eliminar"]').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.4';
            }
        });
    });
});
