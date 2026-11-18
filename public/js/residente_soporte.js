/**
 * @file residente_soporte.js
 * @description Interactividad para la vista de soporte del residente.
 * - Auto-dismiss de alertas flash.
 * - Prevención de doble clic con spinner en el botón de abrir ticket.
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

    // Indicador visual / spinner al enviar formulario de reporte de incidencia
    const formIncidencia = document.getElementById('formReportarIncidencia') || document.querySelector('form[action*="soporte/abrir"]');
    if (formIncidencia) {
        formIncidencia.addEventListener('submit', function () {
            const btn = document.getElementById('btnAbrirTicket') || this.querySelector('button[type="submit"]');
            const spinner = document.getElementById('spinnerTicket');
            const texto = document.getElementById('textoTicket');

            if (btn) btn.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            if (texto) texto.textContent = 'Enviando...';
        });
    }
});
