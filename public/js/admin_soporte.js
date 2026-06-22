/**
 * @file admin_soporte.js
 * @description Interactividad para la vista de tickets de soporte del admin.
 * - Auto-dismiss de alertas flash.
 * - Confirmación visual al cambiar estado de un ticket a "Resuelto".
 */
document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss de alertas flash tras 4 segundos
    const alertas = document.querySelectorAll('.alert.alert-dismissible');
    alertas.forEach(alerta => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        }, 4000);
    });

    // Confirmación antes de marcar como Resuelto
    document.querySelectorAll('form[action*="soporte/validar"]').forEach(form => {
        form.addEventListener('submit', function (e) {
            const select = this.querySelector('select[name="estado"]');
            if (select && select.value === 'Resuelto') {
                const ok = confirm('¿Marcar este ticket como Resuelto? Esta acción registrará la fecha de resolución.');
                if (!ok) e.preventDefault();
            }
        });
    });
});
