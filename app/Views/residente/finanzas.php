<?php 
    $pagina_actual = 'residente_finanzas'; // Mapeo automático
    echo view('template/residente_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container my-5" role="main">
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= site_url('residente/dashboard') ?>" class="text-decoration-none text-secondary small d-inline-flex align-items-center mb-4">
                <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
            </a>
            <h1 class="fw-bold h3 text-dark">Mis Finanzas y Recibos</h1>
            <p class="text-secondary">Consulta tus recibos vigentes por pagar y tu historial de solvencia.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recibos Vigentes Sin Pagar -->
        <section class="col-12" aria-labelledby="recibos-pendientes">
            <div class="card-agora-form p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 id="recibos-pendientes" class="fw-bold h4 text-dark mb-0">Recibos Vigentes (Por Pagar)</h2>
                    <a href="<?= site_url('residente/pago') ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3"><i class="bi bi-cash-coin me-1"></i> Reportar Pago</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-dark">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th scope="col" class="ps-3">N° Recibo</th>
                                <th scope="col">Fecha Emisión</th>
                                <th scope="col">Monto ($)</th>
                                <th scope="col">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (!empty($recibos_pendientes)): ?>
                                <?php foreach ($recibos_pendientes as $recibo): ?>
                                    <tr>
                                        <td class="fw-bold ps-3">
                                            #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?>
                                            <?php if (!empty($recibo['descripcion'])): ?>
                                                <div class="text-muted fw-normal small mt-1" style="font-size: 0.8em;"><?= htmlspecialchars($recibo['descripcion']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($recibo['fecha_emision'] ?? $recibo['created_at'] ?? '')) ?></td>
                                        <td class="fw-bold text-danger">$<?= number_format($recibo['monto_total'], 2) ?></td>
                                        <td><span class="badge rounded-pill bg-danger px-2.5 py-1.5">Pendiente</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-emoji-smile fs-4 d-block mb-2 text-success"></i>
                                        Estás al día. No tienes recibos vigentes por pagar.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Historial de Recibo de Solvencia -->
        <section class="col-12" aria-labelledby="historial-pagados">
            <div class="card-agora-form p-4 mt-2">
                <h2 id="historial-pagados" class="fw-bold h4 text-dark mb-4">Historial de Pagos y Solvencia</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-dark">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th scope="col" class="ps-3">N° Recibo</th>
                                <th scope="col">Fecha Emisión</th>
                                <th scope="col">Monto Pagado ($)</th>
                                <th scope="col">Estatus</th>
                                <th scope="col" class="text-center">Solvencia</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (!empty($recibos_pagados)): ?>
                                <?php foreach ($recibos_pagados as $recibo): ?>
                                    <tr>
                                        <td class="fw-bold ps-3">
                                            #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?>
                                            <?php if (!empty($recibo['descripcion'])): ?>
                                                <div class="text-muted fw-normal small mt-1" style="font-size: 0.8em;"><?= htmlspecialchars($recibo['descripcion']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($recibo['fecha_emision'] ?? $recibo['created_at'] ?? '')) ?></td>
                                        <td class="fw-bold text-success">$<?= number_format($recibo['monto_total'], 2) ?></td>
                                        <td><span class="badge rounded-pill bg-success px-2.5 py-1.5">Pagado</span></td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-sm btn-light text-primary border rounded-pill px-3 disabled" title="Descargar PDF (Próximamente)">
                                                <i class="bi bi-download me-1"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aún no hay historial de recibos pagados registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>
