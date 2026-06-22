/**
 * @file admin_cartelera.js
 * @description Interactividad para el tablón de anuncios del panel admin.
 * - Confirmación antes de eliminar un anuncio (complementa el onclick en HTML).
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
            this.querySelectorAll('input, textarea').forEach(el => el.value = '');
        });
    }
});
