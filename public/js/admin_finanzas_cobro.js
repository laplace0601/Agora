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

    const btnSimular = document.getElementById('btn-simular');
    const formFacturar = document.getElementById('form-facturacion'); // Asume que tu form tiene este ID
    const contenedorSimulacion = document.getElementById('contenedor-simulacion');
    const alertaError = document.getElementById('alerta-error');

    if (btnSimular && formFacturar) {
        btnSimular.addEventListener('click', async function () {
            // 1. Limpiar UI previa
            if(alertaError) {
                alertaError.classList.add('d-none');
                alertaError.innerHTML = '';
            }
            if(contenedorSimulacion) contenedorSimulacion.innerHTML = '';

            // 2. Estado de Carga (Spinner)
            const textoOriginal = btnSimular.innerHTML;
            btnSimular.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Calculando...';
            btnSimular.disabled = true;

            try {
                // 3. Capturar datos del formulario
                const formData = new FormData(formFacturar);

                // Usamos una ruta relativa desde el root de la app asumiendo la URL actual
                // Se asume la estructura del CRM: /admin/finanzas/cobro
                const urlSimulacion = '../crm/finanzas/simular-facturacion';

                // 4. Petición Fetch
                const response = await fetch(urlSimulacion, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                // 5. Manejo de Errores
                if (!response.ok) {
                    throw new Error(result.message || 'Error desconocido al simular la facturación.');
                }

                // 6. Éxito: Renderizar Tabla
                if(contenedorSimulacion) {
                    renderizarTablaSimulacion(result, contenedorSimulacion);
                }

            } catch (error) {
                if(alertaError) {
                    alertaError.innerHTML = `<strong>Atención:</strong> ${error.message}`;
                    alertaError.classList.remove('d-none');
                } else {
                    alert('Atención: ' + error.message);
                }
            } finally {
                // 7. Restaurar Botón
                btnSimular.innerHTML = textoOriginal;
                btnSimular.disabled = false;
            }
        });
    }

    // Confirmación definitiva al hacer Submit normal
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
                    `Asegúrese de haber revisado primero la Simulación.`
                );
                if (!ok) e.preventDefault();
            }
        });
    }

    function renderizarTablaSimulacion(data, contenedor) {
        let tbody = '';
        data.detalle.forEach(apto => {
            tbody += `
                <tr>
                    <td class="text-center fw-semibold">${apto.nro_apartamento}</td>
                    <td class="text-center">${apto.alicuota}</td>
                    <td class="text-end text-success fw-bold">$${apto.monto_a_pagar.toFixed(2)}</td>
                </tr>
            `;
        });

        const htmlTabla = `
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-header bg-light border-bottom-0 pt-3 pb-0">
                    <h5 class="text-primary"><i class="bi bi-calculator"></i> Resultado de Simulación</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-md-4">
                            <span class="d-block small text-muted">Monto Global</span>
                            <strong class="fs-5">$${data.monto_global_base.toFixed(2)}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="d-block small text-muted">Total Distribuido</span>
                            <strong class="fs-5 text-success">$${data.total_distribuido.toFixed(2)}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="d-block small text-muted">Suma Alícuotas</span>
                            <strong class="fs-5 ${data.suma_alicuotas === '100%' || data.suma_alicuotas === '100.00%' ? 'text-success' : 'text-danger'}">${data.suma_alicuotas}</strong>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Apto.</th>
                                    <th class="text-center">Alícuota</th>
                                    <th class="text-end">Monto Proporcional</th>
                                </tr>
                            </thead>
                            <tbody>${tbody}</tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> Descuadre por redondeo: <strong>$${data.descuadre_centavos}</strong>
                    </div>
                </div>
            </div>
        `;
        contenedor.innerHTML = htmlTabla;
    }
});
