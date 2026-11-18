/**
 * @file admin_finanzas_cobro.js
 * @description Interactividad para la vista de generación de cobros del panel admin.
 * - Confirmación antes de emitir facturación masiva.
 * - Auto-dismiss de alertas flash.
 * NOTA: El botón "Simular" fue deshabilitado porque la ruta de simulación
 *       no existe y fetch es bloqueado por el firewall de InfinityFree.
 *       El flujo es 100% POST nativo.
 */
document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss de alertas flash tras 5 segundos
    const alertas = document.querySelectorAll('.alert.alert-dismissible');
    alertas.forEach(alerta => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        }, 5000);
    });

    // Ocultar el botón Simular si existe (la ruta no está implementada en producción)
    const btnSimular = document.getElementById('btn-simular');
    if (btnSimular) {
        btnSimular.style.display = 'none';
    }
    // Ocultar el contenedor de simulación si existe
    const contenedorSimulacion = document.getElementById('contenedor-simulacion');
    if (contenedorSimulacion) {
        contenedorSimulacion.style.display = 'none';
    }

    const formFacturar = document.getElementById('form-facturacion');

    // Confirmación definitiva al hacer Submit nativo
    if (formFacturar) {
        formFacturar.addEventListener('submit', function (e) {
            const condo = document.getElementById('condominio_id');
            const monto = document.getElementById('monto_global_gastos');

            if (condo && monto) {
                const condoText = condo.options[condo.selectedIndex]?.text ?? '---';
                const montoVal = parseFloat(monto.value).toFixed(2);
                const ok = confirm(
                    `¿Está absolutamente seguro de procesar la facturación definitiva?\n\n` +
                    `Esta acción generará recibos formales en base al Monto Global de $${montoVal} para el condominio ${condoText}.\n` +
                    `Esta acción no se puede deshacer.`
                );
                if (!ok) {
                    e.preventDefault();
                    return;
                }
            }

            // Spinner anti doble clic
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
            }
        });
    }
});
