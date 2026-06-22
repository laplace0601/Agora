/**
 * @file admin_finanzas_cobro.js
 * @description Interactividad para la vista de generación de cobros del panel admin.
 * - Confirmación antes de emitir facturación masiva.
 * - Auto-dismiss de alertas flash.
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

    // Confirmación antes de efectuar la facturación masiva
    const formFacturar = document.querySelector('form[action*="finanzas/facturar"]');
    if (formFacturar) {
        formFacturar.addEventListener('submit', function (e) {
            const condo = document.getElementById('condominio_id');
            const mes = document.getElementById('mes');
            const monto = document.getElementById('monto_base');

            if (condo && mes && monto) {
                const condoText = condo.options[condo.selectedIndex]?.text ?? '---';
                const mesText = mes.options[mes.selectedIndex]?.text ?? '---';
                const montoVal = parseFloat(monto.value).toFixed(2);
                const ok = confirm(
                    `¿Confirmar facturación masiva?\n\n` +
                    `Condominio: ${condoText}\n` +
                    `Mes: ${mesText}\n` +
                    `Monto Base: $${montoVal}\n\n` +
                    `Se generarán recibos para todos los apartamentos activos.`
                );
                if (!ok) e.preventDefault();
            }
        });
    }
});
