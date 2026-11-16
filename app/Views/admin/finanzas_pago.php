<?php 
    $pagina_actual = 'admin_finanzas_pago'; // Conecta con public/css/admin_finanzas_pago.css
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="p-5" role="main">
    <div class="mb-4">
        <h1 class="fw-bold h3 text-dark">Administración de Pagos</h1>
        <p class="text-secondary mb-0">Valida los comprobantes de transferencias bancarias enviados por los residentes.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small fw-bold text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Apartamento</th>
                            <th>Datos de Transferencia</th>
                            <th class="text-center">Monto</th>
                            <th class="text-center">Mes / Año</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pagos = $pagos ?? [];
                        if (empty($pagos)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No hay pagos pendientes de validación.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pagos as $pago): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark fs-6"><?= htmlspecialchars($pago['nombre_completo'] ?? 'Sin Asignar') ?></div>
                                        <div class="small text-muted mt-1 mb-1">Apto <?= htmlspecialchars($pago['nro_apartamento'] ?? '—') ?></div>
                                        <span class="badge rounded-pill px-2 py-1 text-dark mt-1" 
                                              style="background-color: rgba(217,119,6,0.1); color: #D97706 !important;">
                                            Por validar
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small text-secondary">
                                            <strong>Método:</strong> <?= htmlspecialchars(formatearMetodoPago($pago['metodo_pago'] ?? '')) ?><br>
                                            <strong>Ref:</strong> <?= htmlspecialchars(formatearReferenciaPago($pago['referencia_transaccion'] ?? '', $pago['metodo_pago'] ?? '')) ?><br>
                                            <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($pago['fecha_registro'])) ?>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        $<?= number_format((float)($pago['monto_pagado'] ?? 0), 2) ?>
                                    </td>
                                    <td class="text-center small text-secondary">
                                        <?= $pago['mes'] ?? '—' ?> / <?= $pago['anio'] ?? '—' ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <!-- Aprobar: submit directo -->
                                            <form action="<?= site_url('admin/finanzas/validar-pago') ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="pago_id" value="<?= $pago['id'] ?>">
                                                <input type="hidden" name="accion" value="aprobar">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-medium shadow-sm">
                                                    <i class="bi bi-check-lg me-1"></i> Aprobar
                                                </button>
                                            </form>
                                            <!-- Rechazar: abre modal con motivo obligatorio -->
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-medium"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalRechazo"
                                                    data-pago-id="<?= $pago['id'] ?>"
                                                    data-residente="<?= htmlspecialchars($pago['nombre_completo'] ?? '') ?>"
                                                    data-apto="<?= htmlspecialchars($pago['nro_apartamento'] ?? '') ?>"
                                                    data-monto="$<?= number_format((float)($pago['monto_pagado'] ?? 0), 2) ?>">
                                                <i class="bi bi-x-lg me-1"></i> Rechazar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Historial de Validaciones Recientes -->
    <?php $historial = $historial ?? []; ?>
    <?php if (!empty($historial)): ?>
        <div class="mt-5 mb-3">
            <h2 class="fw-bold h4 text-dark mb-1">Historial Reciente de Validaciones</h2>
            <p class="text-secondary small mb-0">Últimos pagos procesados por la administración con su resolución y motivo.</p>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small fw-bold text-secondary">
                            <tr>
                                <th class="ps-4 py-3">Apartamento</th>
                                <th>Datos de Transferencia</th>
                                <th class="text-center">Monto</th>
                                <th class="text-center">Mes / Año</th>
                                <th class="text-center">Estatus</th>
                                <th class="pe-4">Resolución / Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $hp): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark fs-6"><?= htmlspecialchars($hp['nombre_completo'] ?? 'Sin Asignar') ?></div>
                                        <div class="small text-muted">Apto <?= htmlspecialchars($hp['nro_apartamento'] ?? '—') ?></div>
                                    </td>
                                    <td>
                                        <div class="small text-secondary">
                                            <strong>Método:</strong> <?= htmlspecialchars(formatearMetodoPago($hp['metodo_pago'] ?? '')) ?><br>
                                            <strong>Ref:</strong> <?= htmlspecialchars(formatearReferenciaPago($hp['referencia_transaccion'] ?? '', $hp['metodo_pago'] ?? '')) ?><br>
                                            <span class="text-muted"><?= date('d/m/Y H:i', strtotime($hp['fecha_registro'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        $<?= number_format((float)($hp['monto_pagado'] ?? 0), 2) ?>
                                    </td>
                                    <td class="text-center small text-secondary">
                                        <?= $hp['mes'] ?? '—' ?> / <?= $hp['anio'] ?? '—' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hp['estado_validacion'] === 'Aprobado'): ?>
                                            <span class="badge rounded-pill bg-success px-3 py-1.5">
                                                <i class="bi bi-check-circle me-1"></i> Aprobado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-danger px-3 py-1.5">
                                                <i class="bi bi-x-circle me-1"></i> Rechazado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 small">
                                        <?php if (!empty($hp['motivo_rechazo'])): ?>
                                            <div class="p-2 rounded-2 border border-danger border-opacity-25 bg-danger bg-opacity-10">
                                                <div class="text-danger fw-semibold mb-1">
                                                    <i class="bi bi-chat-quote-fill me-1"></i>Motivo de rechazo:
                                                </div>
                                                <div class="text-dark">
                                                    <?= nl2br(htmlspecialchars($hp['motivo_rechazo'])) ?>
                                                </div>
                                            </div>
                                        <?php elseif ($hp['estado_validacion'] === 'Aprobado'): ?>
                                            <span class="text-success"><i class="bi bi-check2-all me-1"></i>Aprobado y conciliado</span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Sin motivo especificado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<!-- ============================================================ -->
<!-- MODAL: Motivo de Rechazo                                     -->
<!-- ============================================================ -->
<div class="modal fade" id="modalRechazo" tabindex="-1" aria-labelledby="modalRechazoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="<?= site_url('admin/finanzas/validar-pago') ?>" method="POST" id="formRechazo" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="pago_id" id="rechazo_pago_id">
                <input type="hidden" name="accion" value="rechazar">

                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-danger" id="modalRechazoLabel">
                            <i class="bi bi-x-circle me-2"></i>Rechazar Pago
                        </h5>
                        <p class="text-muted small mb-0 mt-1" id="rechazo_info_residente"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body px-4 pb-2">
                    <div class="alert alert-warning border-0 rounded-3 small py-2 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        El residente verá este motivo en su panel. Sé claro y específico.
                    </div>

                    <label for="motivo_rechazo" class="form-label fw-semibold text-dark small text-uppercase">
                        Motivo del rechazo <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="motivo_rechazo"
                        name="motivo_rechazo"
                        class="form-control rounded-3"
                        rows="4"
                        maxlength="500"
                        placeholder="Ej: El número de referencia no coincide con nuestros registros bancarios. Por favor, verifica y reenvía el comprobante correcto."
                        required></textarea>
                    <div class="invalid-feedback">El motivo del rechazo es obligatorio.</div>
                    <div class="form-text text-end mt-1">
                        <span id="char_count">0</span>/500 caracteres
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-x-lg me-1"></i> Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    const modal      = document.getElementById('modalRechazo');
    const pagoIdInput = document.getElementById('rechazo_pago_id');
    const infoResidente = document.getElementById('rechazo_info_residente');
    const textarea   = document.getElementById('motivo_rechazo');
    const charCount  = document.getElementById('char_count');
    const form       = document.getElementById('formRechazo');

    // Poblar datos en el modal al abrirlo
    modal.addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        pagoIdInput.value     = btn.dataset.pagoId;
        infoResidente.textContent = btn.dataset.residente + ' — Apto ' + btn.dataset.apto + ' · ' + btn.dataset.monto;
        textarea.value        = '';
        textarea.classList.remove('is-invalid');
        charCount.textContent = '0';
    });

    // Contador de caracteres en tiempo real
    textarea.addEventListener('input', function () {
        charCount.textContent = this.value.length;
    });

    // Validación antes de enviar
    form.addEventListener('submit', function (e) {
        if (textarea.value.trim() === '') {
            e.preventDefault();
            textarea.classList.add('is-invalid');
            textarea.focus();
        } else {
            textarea.classList.remove('is-invalid');
        }
    });
})();
</script>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>