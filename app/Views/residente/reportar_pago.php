<?php
$pagina_actual = 'residente_reportar_pago'; // Define el nombre exacto para conectar CSS y JS
echo view('template/residente_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container my-5" role="main">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <a href="<?= site_url('residente/dashboard') ?>" class="text-decoration-none text-secondary small d-inline-flex align-items-center mb-4">
                <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
            </a>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <h1 class="fw-bold h3 text-dark mb-1">Reportar Pago Mensual</h1>
                <p class="text-secondary">Transfiere a las cuentas oficiales del condominio y registra el comprobante a continuación.</p>
            </div>

            <!-- Aviso de pagos previamente rechazados con su motivo -->
            <?php $pagos_rechazados = $pagos_rechazados ?? []; ?>
            <?php if (!empty($pagos_rechazados)): ?>
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-octagon-fill fs-4 text-danger"></i>
                        <h5 class="fw-bold text-danger mb-0">Tienes pago(s) rechazado(s) por la administración</h5>
                    </div>
                    <p class="small text-secondary mb-3">La administración revisó tu comprobante y lo rechazó indicando el siguiente motivo. Por favor verifica tus datos bancarios y reporta el comprobante correcto en el formulario:</p>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($pagos_rechazados as $pr): ?>
                            <div class="bg-white p-3 rounded-3 border border-danger border-opacity-25">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold small text-dark">
                                        Recibo: <?= $pr['mes'] ?>/<?= $pr['anio'] ?> · <?= htmlspecialchars(formatearMetodoPago($pr['metodo_pago'] ?? '')) ?> · Ref: <?= htmlspecialchars(formatearReferenciaPago($pr['referencia_transaccion'] ?? '', $pr['metodo_pago'] ?? '')) ?> · $<?= number_format((float)($pr['monto_pagado'] ?? 0), 2) ?>
                                    </span>
                                    <span class="badge bg-danger rounded-pill px-2.5 py-1">Rechazado</span>
                                </div>
                                <?php if (!empty($pr['motivo_rechazo'])): ?>
                                    <div class="small text-danger fw-semibold mt-1">
                                        <i class="bi bi-chat-quote-fill me-1"></i>Motivo del Administrador:
                                    </div>
                                    <div class="small text-dark mt-1 bg-light p-2 rounded">
                                        <?= nl2br(htmlspecialchars($pr['motivo_rechazo'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ======================================================== -->
            <!-- CARD DE DATOS DE PAGO DESTINO (MARCA BLANCA)             -->
            <!-- ======================================================== -->
            <?php
            $bancoDestino    = bancoPago();
            $cedulaDestino   = cedulaPago();
            $telefonoDestino = telefonoPago();
            $titularDestino  = nombreEmpresa();
            $hayDatosPago    = !empty($bancoDestino) || !empty($cedulaDestino) || !empty($telefonoDestino);
            ?>

            <?php if ($hayDatosPago): ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border-left: 5px solid var(--agora-primary, #007bff) !important; background: #fdfdfd;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: var(--agora-primary, #007bff);">
                                    <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">Datos de Pago Móvil / Transferencia</h5>
                                    <small class="text-secondary">Emite tu pago a los datos receptores autorizados</small>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 text-uppercase fw-semibold" style="background: rgba(0, 123, 255, 0.1); color: var(--agora-primary, #007bff);">
                                <i class="bi bi-shield-check me-1"></i> Cuenta Oficial
                            </span>
                        </div>

                        <div class="row g-3 pt-1">
                            <?php if (!empty($bancoDestino)): ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="bg-light p-3 rounded-3 border h-100">
                                        <div class="text-secondary small fw-semibold text-uppercase mb-1">
                                            <i class="bi bi-bank me-1" style="color: var(--agora-primary, #007bff);"></i> Banco Destino
                                        </div>
                                        <div class="fw-bold text-dark fs-6 user-select-all"><?= esc($bancoDestino) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cedulaDestino)): ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="bg-light p-3 rounded-3 border h-100 position-relative">
                                        <div class="text-secondary small fw-semibold text-uppercase mb-1">
                                            <i class="bi bi-person-vcard me-1" style="color: var(--agora-primary, #007bff);"></i> Cédula / RIF
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-bold text-dark fs-6 user-select-all" id="txt-cedula-destino"><?= esc($cedulaDestino) ?></span>
                                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-secondary" onclick="copiarDato('txt-cedula-destino', this)" title="Copiar Cédula">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($telefonoDestino)): ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="bg-light p-3 rounded-3 border h-100 position-relative">
                                        <div class="text-secondary small fw-semibold text-uppercase mb-1">
                                            <i class="bi bi-phone me-1" style="color: var(--agora-primary, #007bff);"></i> Pago Móvil
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-bold text-dark fs-6 user-select-all" id="txt-telefono-destino"><?= esc($telefonoDestino) ?></span>
                                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-secondary" onclick="copiarDato('txt-telefono-destino', this)" title="Copiar Teléfono">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($titularDestino)): ?>
                                <div class="col-12">
                                    <div class="bg-light p-2 px-3 rounded-3 border small text-secondary d-flex align-items-center">
                                        <i class="bi bi-building me-2" style="color: var(--agora-primary, #007bff);"></i>
                                        <span><strong>Beneficiario:</strong> <?= esc($titularDestino) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Cuentas bancarias en configuración:</strong>
                        <div class="small text-muted">La administración del condominio aún no ha publicado los datos bancarios de destino. Si tienes dudas, contáctalos directamente.</div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ======================================================== -->
            <!-- FORMULARIO DE REPORTE DE PAGO                            -->
            <!-- ======================================================== -->
            <?php
            $reciboIdUrl = service('request')->getGet('recibo_id');
            $montoUrl    = service('request')->getGet('monto');
            ?>
            <div class="card-agora-form p-4 p-md-5 bg-white shadow-sm rounded-4 border border-light">
                <h5 class="fw-bold text-dark mb-4">
                    <i class="bi bi-pencil-square me-2" style="color: var(--agora-primary, #007bff);"></i>Registrar Datos de tu Transacción
                </h5>

                <div id="alertaErrorPago" class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><span id="mensajeErrorPago"></span>
                    <button type="button" class="btn-close" onclick="document.getElementById('alertaErrorPago').classList.add('d-none')"></button>
                </div>

                <form id="formReportarPago" action="<?= site_url('residente/pago/enviar') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="bancoOrigen" name="banco" required>
                                    <option value="" selected disabled>Selecciona el método de pago</option>
                                    <option value="Pago Móvil">Pago Móvil</option>
                                    <option value="Efectivo">Efectivo</option>
                                </select>
                                <label for="bancoOrigen">Método de Pago *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="numReferencia" name="referencia" placeholder="Número de referencia bancaria" required>
                                <label for="numReferencia" id="labelNumReferencia">Número de Referencia Completo *</label>
                            </div>
                            <div id="ayudaReferencia" class="form-text small text-muted mt-1 d-none">
                                <i class="bi bi-info-circle me-1"></i>Para pagos en efectivo puedes dejar este campo vacío o indicar algún detalle (ej: Entregado en conserjería).
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="recibo" name="recibo_id" required>
                                    <option value="" <?= empty($reciboIdUrl) ? 'selected' : '' ?> disabled>Selecciona el recibo a pagar</option>
                                    <?php if (!empty($recibos_pendientes)): ?>
                                        <?php foreach ($recibos_pendientes as $recibo): ?>
                                             <?php 
                                            $isSelected = (!empty($reciboIdUrl) && $reciboIdUrl == $recibo['id']); 
                                            ?>
                                            <option value="<?= $recibo['id'] ?>" data-monto="<?= $recibo['monto_total'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                                Recibo #<?= $recibo['id'] ?> - $<?= number_format($recibo['monto_total'], 2) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay recibos pendientes</option>
                                    <?php endif; ?>
                                </select>
                                <label for="recibo">Recibo Pendiente *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" id="monto" name="monto" placeholder="Monto pagado" value="<?= esc($montoUrl ?? '') ?>" required>
                                <label for="monto">Monto Pagado ($) *</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" id="btnEnviarPago" class="btn btn-success text-white w-100 py-3 rounded-pill fs-6 fw-bold shadow-sm" style="background-color: #198754; border: none;">
                                <i class="bi bi-send-check me-2"></i>Enviar Reporte de Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- ======================================================== -->
<!-- MODAL: MINI VENTANA DE CONFIRMACIÓN DE PAGO REPORTADO    -->
<!-- ======================================================== -->
<div class="modal fade" id="modalPagoReportado" tabindex="-1" aria-labelledby="modalPagoReportadoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden text-center p-3 p-md-4 position-relative">
            
            <!-- Icono circular animado superior -->
            <div class="mx-auto mb-3 mt-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                 style="width: 72px; height: 72px; background: rgba(25, 135, 84, 0.12); color: #198754;">
                <i class="bi bi-clock-history fs-1"></i>
            </div>

            <!-- Título y Subtítulo -->
            <h4 class="fw-bold text-dark mb-1" id="modalPagoReportadoLabel">¡Pago Reportado!</h4>
            <p class="text-secondary small mb-3">Tu notificación de pago ha sido registrada correctamente.</p>

            <!-- Mini Alerta de Espera de Confirmación -->
            <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 text-start d-flex align-items-start gap-2 shadow-sm" style="background-color: #fff9e6; color: #856404;">
                <i class="bi bi-hourglass-split fs-5 flex-shrink-0 mt-0.5 text-warning"></i>
                <div class="small">
                    <strong>Por favor, espera la confirmación:</strong><br>
                    La administración revisará y validará tu comprobante en breve. Una vez verificado el depósito, tu recibo pasará automáticamente a estado <strong>Solvente</strong>.
                </div>
            </div>

            <!-- Resumen de Datos del Pago -->
            <div class="bg-light rounded-3 p-3 mb-4 text-start small border">
                <div class="d-flex justify-content-between mb-1.5">
                    <span class="text-muted">N° Recibo:</span>
                    <strong class="text-dark" id="modalConfirmRecibo">—</strong>
                </div>
                <div class="d-flex justify-content-between mb-1.5">
                    <span class="text-muted">Método de Pago:</span>
                    <span class="text-dark fw-semibold" id="modalConfirmBanco">—</span>
                </div>
                <div class="d-flex justify-content-between mb-1.5">
                    <span class="text-muted">Referencia:</span>
                    <span class="text-dark font-monospace fw-bold" id="modalConfirmRef">—</span>
                </div>
                <div class="d-flex justify-content-between mb-1.5">
                    <span class="text-muted">Monto Reportado:</span>
                    <strong class="text-success fs-6" id="modalConfirmMonto">$0.00</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted">Estatus Actual:</span>
                    <span class="badge rounded-pill px-2.5 py-1 text-uppercase fw-semibold" style="background: rgba(255, 193, 7, 0.2); color: #997404;">
                        <i class="bi bi-hourglass-split me-1"></i>Por Validar
                    </span>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex flex-column gap-2">
                <a href="<?= site_url('residente/finanzas') ?>" class="btn btn-success text-white rounded-pill py-2.5 fw-bold shadow-sm" style="background-color: #198754; border: none;">
                    <i class="bi bi-wallet2 me-1"></i> Ir a Mis Recibos y Solvencia
                </a>
                <a href="<?= site_url('residente/dashboard') ?>" class="btn btn-link text-secondary text-decoration-none small py-1">
                    Volver al Dashboard
                </a>
            </div>

        </div>
    </div>
</div>

<script>
function copiarDato(elementId, btn) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const texto = el.textContent.trim();
    navigator.clipboard.writeText(texto).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check text-success fs-5"></i>';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 1500);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const formReportar       = document.getElementById('formReportarPago');
    const reciboSelect       = document.getElementById('recibo');
    const montoInput         = document.getElementById('monto');
    const bancoOrigen        = document.getElementById('bancoOrigen');
    const numReferencia      = document.getElementById('numReferencia');
    const labelNumReferencia = document.getElementById('labelNumReferencia');
    const ayudaReferencia    = document.getElementById('ayudaReferencia');
    const btnEnviar          = document.getElementById('btnEnviarPago');
    const alertaError        = document.getElementById('alertaErrorPago');
    const mensajeError       = document.getElementById('mensajeErrorPago');

    // Manejar cambio de método de pago: si es Efectivo, la referencia es opcional
    function alternarMetodoPago() {
        if (!bancoOrigen || !numReferencia) return;
        const esEfectivo = (bancoOrigen.value === 'Efectivo');

        if (esEfectivo) {
            numReferencia.removeAttribute('required');
            numReferencia.placeholder = 'Opcional (Ej. Entregado en conserjería)';
            if (labelNumReferencia) {
                labelNumReferencia.textContent = 'Número de Referencia (Opcional)';
            }
            if (ayudaReferencia) {
                ayudaReferencia.classList.remove('d-none');
            }
        } else {
            numReferencia.setAttribute('required', 'required');
            numReferencia.placeholder = 'Número de referencia bancaria';
            if (labelNumReferencia) {
                labelNumReferencia.textContent = 'Número de Referencia Completo *';
            }
            if (ayudaReferencia) {
                ayudaReferencia.classList.add('d-none');
            }
        }
    }

    if (bancoOrigen) {
        bancoOrigen.addEventListener('change', alternarMetodoPago);
        alternarMetodoPago(); // Ejecutar al cargar por si hay valor previo
    }

    if (reciboSelect && montoInput) {
        reciboSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.monto) {
                montoInput.value = parseFloat(opt.dataset.monto).toFixed(2);
            }
        });
        // Si ya hay uno seleccionado y el monto está vacío, autocompletar
        if (reciboSelect.selectedIndex > 0 && !montoInput.value) {
            const opt = reciboSelect.options[reciboSelect.selectedIndex];
            if (opt && opt.dataset.monto) {
                montoInput.value = parseFloat(opt.dataset.monto).toFixed(2);
            }
        }
    }

    // Interceptar envío del formulario para mostrar la mini ventana de confirmación
    if (formReportar) {
        formReportar.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (alertaError) alertaError.classList.add('d-none');

            const esEfectivo = (bancoOrigen.value === 'Efectivo');

            if (!reciboSelect.value || !montoInput.value || !bancoOrigen.value) {
                if (alertaError && mensajeError) {
                    mensajeError.textContent = 'Por favor completa todos los campos obligatorios antes de enviar.';
                    alertaError.classList.remove('d-none');
                }
                return;
            }

            if (!esEfectivo && !numReferencia.value.trim()) {
                if (alertaError && mensajeError) {
                    mensajeError.textContent = 'El número de referencia es obligatorio para pagos por Pago Móvil.';
                    alertaError.classList.remove('d-none');
                }
                numReferencia.focus();
                return;
            }

            const originalBtnHtml = btnEnviar.innerHTML;
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando pago...';

            try {
                const formData = new FormData(formReportar);
                const resp = await fetch(formReportar.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await resp.json();

                if (result.status === 'success') {
                    // Refrescar token CSRF si la respuesta lo suministra
                    if (result.csrf) {
                        const csrfInput = formReportar.querySelector('input[name="<?= csrf_token() ?>"]');
                        if (csrfInput) csrfInput.value = result.csrf;
                    }

                    // Llenar datos en el modal de confirmación
                    const payload = result.data || {};
                    const metodoFinal = payload.metodo || bancoOrigen.value;
                    const refFinal = payload.referencia || (numReferencia.value.trim() ? numReferencia.value.trim() : (esEfectivo ? 'Efectivo / Sin referencia' : '—'));

                    document.getElementById('modalConfirmRecibo').textContent = '#' + (payload.recibo_id || reciboSelect.value);
                    document.getElementById('modalConfirmBanco').textContent = metodoFinal;
                    document.getElementById('modalConfirmRef').textContent = refFinal;
                    document.getElementById('modalConfirmMonto').textContent = '$' + (payload.monto || parseFloat(montoInput.value).toFixed(2));

                    // Desplegar mini ventana de confirmación
                    const modalEl = document.getElementById('modalPagoReportado');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    // Limpiar formulario para evitar envíos duplicados si se cierra
                    formReportar.reset();
                    alternarMetodoPago();
                    btnEnviar.disabled = false;
                    btnEnviar.innerHTML = originalBtnHtml;
                } else {
                    if (alertaError && mensajeError) {
                        mensajeError.textContent = result.message || 'Error al procesar el reporte de pago.';
                        alertaError.classList.remove('d-none');
                    }
                    btnEnviar.disabled = false;
                    btnEnviar.innerHTML = originalBtnHtml;
                }
            } catch (err) {
                console.warn('Fallo en solicitud AJAX, enviando por flujo estándar:', err);
                // Si la conexión AJAX falla, se envía por el método tradicional del navegador
                formReportar.submit();
            }
        });
    }
});
</script>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>
