<?php 
    $pagina_actual = 'residente_finanzas'; // Carga css/residente_finanzas.css automáticamente
    echo view('template/residente_header', ['pagina_actual' => $pagina_actual]);

    // Lógica y cálculos de métricas para tarjetas KPI
    $recibos_pendientes = $recibos_pendientes ?? [];
    $recibos_pagados    = $recibos_pagados ?? [];
    $pagos_rechazados   = $pagos_rechazados ?? [];

    $saldoPendienteTotal  = 0.0;
    $pagosEnRevisionCount = 0;
    $pagosEnRevisionMonto = 0.0;

    foreach ($recibos_pendientes as $rp) {
        $monto = (float)($rp['monto_total'] ?? 0);
        $saldoPendienteTotal += $monto;
        if (($rp['estado_validacion_pago'] ?? '') === 'Por Validar') {
            $pagosEnRevisionCount++;
            $pagosEnRevisionMonto += $monto;
        }
    }

    $esSolvente = ($saldoPendienteTotal <= 0.001);
?>

<main class="container my-5" role="main">
    <!-- Navegación y Encabezado de Página -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= site_url('residente/dashboard') ?>" class="text-decoration-none text-secondary small d-inline-flex align-items-center mb-3">
                <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
            </a>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h1 class="fw-bold h3 text-dark mb-1">Mis Recibos y Solvencias</h1>
                    <p class="text-secondary mb-0">Consulta tus recibos vigentes por pagar, estado de solvencia y comprobantes aprobados.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- 1. Tarjetas KPI Superiores Resumidas -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Saldo Pendiente Total -->
        <div class="col-12 col-md-4">
            <div class="kpi-card-agora h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Saldo Pendiente Total</div>
                    <div class="fs-3 fw-bold text-dark font-monospace mb-1">
                        $<?= number_format($saldoPendienteTotal, 2) ?>
                    </div>
                    <div class="small <?= $saldoPendienteTotal > 0 ? 'text-danger' : 'text-success' ?> fw-medium">
                        <?php if ($saldoPendienteTotal > 0): ?>
                            <i class="bi bi-exclamation-circle me-1"></i><?= count($recibos_pendientes) ?> recibo(s) por pagar
                        <?php else: ?>
                            <i class="bi bi-check-circle me-1"></i>Sin recibos adeudados
                        <?php endif; ?>
                    </div>
                </div>
                <div class="kpi-icon-box <?= $saldoPendienteTotal > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>

        <!-- KPI 2: Pagos en Revisión -->
        <div class="col-12 col-md-4">
            <div class="kpi-card-agora h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Pagos en Revisión</div>
                    <div class="fs-3 fw-bold text-dark font-monospace mb-1">
                        <?= $pagosEnRevisionCount ?>
                    </div>
                    <div class="small <?= $pagosEnRevisionCount > 0 ? 'text-warning-emphasis' : 'text-muted' ?> fw-medium">
                        <?php if ($pagosEnRevisionCount > 0): ?>
                            <i class="bi bi-clock-history me-1"></i>$<?= number_format($pagosEnRevisionMonto, 2) ?> en validación
                        <?php else: ?>
                            <i class="bi bi-check2-all me-1"></i>Sin reportes por validar
                        <?php endif; ?>
                    </div>
                </div>
                <div class="kpi-icon-box bg-warning-subtle text-warning-emphasis">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>

        <!-- KPI 3: Estatus de Solvencia -->
        <div class="col-12 col-md-4">
            <div class="kpi-card-agora h-100 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Estatus de Solvencia</div>
                    <div class="fs-4 fw-bold mb-1 <?= $esSolvente ? 'text-success' : 'text-danger' ?>">
                        <?= $esSolvente ? 'Solvente' : 'Saldo Pendiente' ?>
                    </div>
                    <div class="small text-muted fw-medium">
                        <?php if ($esSolvente): ?>
                            <i class="bi bi-shield-check text-success me-1"></i>Condominio al día
                        <?php else: ?>
                            <i class="bi bi-shield-exclamation text-danger me-1"></i>Requiere regularización
                        <?php endif; ?>
                    </div>
                </div>
                <div class="kpi-icon-box <?= $esSolvente ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                    <i class="bi <?= $esSolvente ? 'bi-shield-check' : 'bi-shield-exclamation' ?>"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- 2. Recibos Vigentes Sin Pagar -->
        <section class="col-12" aria-labelledby="recibos-pendientes">
            <div class="card-agora-form p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <div>
                        <h2 id="recibos-pendientes" class="fw-bold h4 text-dark mb-1">Recibos Vigentes (Por Pagar)</h2>
                        <span class="text-muted small">Listado de cuotas pendientes asociadas a tu inmueble.</span>
                    </div>
                    <!-- Botón de pagar en color verde -->
                    <a href="<?= site_url('residente/pago') ?>" class="btn btn-success text-white rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #198754; border-color: #198754;">
                        <i class="bi bi-plus-circle-fill fs-6"></i>
                        <span>Reportar Pago</span>
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-dark mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th scope="col" class="ps-3">N° Recibo</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Fecha Emisión</th>
                                <th scope="col" class="text-end">Monto ($)</th>
                                <th scope="col" class="text-center">Estatus</th>
                                <th scope="col" class="text-end pe-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (!empty($recibos_pendientes)): ?>
                                <?php foreach ($recibos_pendientes as $recibo): ?>
                                    <tr>
                                        <td class="ps-3 font-monospace fw-bold text-dark">
                                            #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">
                                                <?= htmlspecialchars(!empty($recibo['descripcion']) ? $recibo['descripcion'] : ('Cuota ' . str_pad($recibo['mes'], 2, '0', STR_PAD_LEFT) . '/' . $recibo['anio'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-secondary">
                                            <?php
                                                // Fecha real de aprobación o registro del pago (con fallback seguro a emisión)
                                                $fechaPagoRaw = $recibo['fecha_real_pago'] ?? $recibo['fecha_pago'] ?? $recibo['fecha_emision'] ?? null;
                                                $tsPago = (!empty($fechaPagoRaw) && $fechaPagoRaw !== '0000-00-00 00:00:00') ? strtotime($fechaPagoRaw) : false;
                                                echo ($tsPago && $tsPago > 0) ? date('d/m/Y', $tsPago) : sprintf('01/%02d/%04d', $recibo['mes'], $recibo['anio']);
                                            ?>
                                        </td>
                                        <td class="font-monospace fw-bold text-danger text-end fs-6">
                                            $<?= number_format($recibo['monto_total'], 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($recibo['estado_validacion_pago'] ?? '') === 'Por Validar'): ?>
                                                <span class="badge badge-soft-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5">
                                                    <i class="bi bi-clock-history"></i> Esperando validación
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-danger rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5">
                                                    <span class="badge-dot bg-danger"></span> Pendiente
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <?php if (($recibo['estado_validacion_pago'] ?? '') === 'Por Validar'): ?>
                                                <button class="btn btn-sm btn-light border text-muted rounded-pill px-3 py-1 disabled" title="Pago reportado en revisión por administración">
                                                    <i class="bi bi-hourglass-split me-1"></i>En revisión
                                                </button>
                                            <?php else: ?>
                                                <!-- Botón rápido Pagar en color verde con pre-carga de recibo_id y monto -->
                                                <a href="<?= site_url('residente/pago?recibo_id=' . $recibo['id'] . '&monto=' . urlencode($recibo['monto_total'])) ?>" 
                                                   class="btn btn-sm btn-success text-white rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm"
                                                   style="background-color: #198754; border-color: #198754;"
                                                   title="Pagar ahora este recibo">
                                                    <i class="bi bi-cash-stack"></i>
                                                    <span>Pagar</span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <div class="kpi-icon-box bg-success-subtle text-success mx-auto mb-2">
                                            <i class="bi bi-emoji-smile fs-4"></i>
                                        </div>
                                        <div class="fw-semibold text-dark mb-1">¡Estás al día con tus pagos!</div>
                                        <p class="small text-secondary mb-0">No tienes recibos vigentes por pagar en este momento.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 3. Historial de Recibo de Solvencia -->
        <section class="col-12" aria-labelledby="historial-pagados">
            <div class="card-agora-form p-4">
                <div class="mb-4">
                    <h2 id="historial-pagados" class="fw-bold h4 text-dark mb-1">Historial de Pagos y Solvencia</h2>
                    <span class="text-muted small">Registro histórico de pagos aprobados y certificados de solvencia.</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle text-dark mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th scope="col" class="ps-3">N° Recibo</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Fecha de Pago</th>
                                <th scope="col" class="text-end">Monto Pagado ($)</th>
                                <th scope="col" class="text-center">Estatus</th>
                                <th scope="col" class="text-center pe-3">Solvencia</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (!empty($recibos_pagados)): ?>
                                <?php foreach ($recibos_pagados as $recibo): ?>
                                    <tr>
                                        <td class="ps-3 font-monospace fw-bold text-dark">
                                            #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark">
                                                <?= htmlspecialchars(!empty($recibo['descripcion']) ? $recibo['descripcion'] : ('Cuota ' . str_pad($recibo['mes'], 2, '0', STR_PAD_LEFT) . '/' . $recibo['anio'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-secondary">
                                            <?php
                                                // Fecha real de aprobación o registro del pago (con fallback seguro a emisión)
                                                $fechaPagoRaw = $recibo['fecha_real_pago'] ?? $recibo['fecha_pago'] ?? $recibo['fecha_emision'] ?? null;
                                                $tsPago = (!empty($fechaPagoRaw) && $fechaPagoRaw !== '0000-00-00 00:00:00') ? strtotime($fechaPagoRaw) : false;
                                                echo ($tsPago && $tsPago > 0) ? date('d/m/Y', $tsPago) : sprintf('01/%02d/%04d', $recibo['mes'], $recibo['anio']);
                                            ?>
                                        </td>
                                        <td class="font-monospace fw-bold text-success text-end fs-6">
                                            $<?= number_format($recibo['monto_total'], 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5">
                                                <i class="bi bi-check-circle-fill"></i> Pagado
                                            </span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <!-- Botón PDF que abre el comprobante en una nueva pestaña -->
                                            <a href="<?= site_url('residente/recibo/pdf/' . $recibo['id']) ?>" 
                                               target="_blank" 
                                               class="btn btn-sm btn-pdf-agora rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5 shadow-2xs" 
                                               title="Ver y Descargar Comprobante de Pago en PDF">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-6"></i>
                                                <span>PDF</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-journal-x fs-3 d-block mb-2 text-secondary"></i>
                                        Aún no hay historial de recibos pagados registrados para este apartamento.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 4. Sección: Pagos Rechazados (con motivo detallado) -->
        <?php if (!empty($pagos_rechazados)): ?>
        <section class="col-12" aria-labelledby="pagos-rechazados">
            <div class="card-agora-form p-4 border border-danger border-opacity-25">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="kpi-icon-box bg-danger-subtle text-danger" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h2 id="pagos-rechazados" class="fw-bold h5 text-dark mb-0">Pagos Rechazados</h2>
                    <span class="badge badge-soft-danger rounded-pill ms-1"><?= count($pagos_rechazados) ?></span>
                </div>
                <p class="text-secondary small mb-3">
                    Los siguientes comprobantes fueron rechazados por el administrador. Revisa el motivo indicado y reporta un nuevo pago corregido.
                </p>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($pagos_rechazados as $pr): ?>
                    <div class="border border-danger border-opacity-25 rounded-3 p-3 bg-danger bg-opacity-10">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <span class="fw-bold text-dark">
                                    Recibo: <?= $pr['mes'] ?? '—' ?>/<?= $pr['anio'] ?? '—' ?>
                                </span>
                                <span class="ms-2 small text-muted">
                                    — Ref: <span class="font-monospace">#<?= htmlspecialchars($pr['referencia_transaccion'] ?? '—') ?></span>
                                </span>
                                <span class="ms-2 fw-bold text-dark font-monospace">
                                    $<?= number_format((float)($pr['monto_pagado'] ?? 0), 2) ?>
                                </span>
                                <div class="small text-muted mt-1">
                                    Enviado el <?= date('d/m/Y H:i', strtotime($pr['fecha_registro'])) ?>
                                    · Vía: <?= htmlspecialchars($pr['metodo_pago'] ?? '—') ?>
                                </div>
                            </div>
                            <span class="badge badge-soft-danger rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1">
                                <i class="bi bi-x-circle-fill"></i> Rechazado
                            </span>
                        </div>
                        <?php if (!empty($pr['motivo_rechazo'])): ?>
                        <div class="mt-3 p-3 bg-white rounded-3 border border-danger border-opacity-25 shadow-2xs">
                            <div class="small fw-semibold text-danger mb-1 d-flex align-items-center gap-1">
                                <i class="bi bi-chat-left-text-fill"></i> Motivo del rechazo indicado por administración:
                            </div>
                            <p class="mb-0 small text-dark" style="line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($pr['motivo_rechazo'])) ?>
                            </p>
                        </div>
                        <?php else: ?>
                        <div class="mt-2 small text-muted fst-italic">
                            <i class="bi bi-info-circle me-1"></i>No se proporcionó un motivo específico.
                        </div>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="<?= site_url('residente/pago') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-medium d-inline-flex align-items-center gap-1">
                                <i class="bi bi-arrow-repeat"></i> Reportar nuevo pago
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>
