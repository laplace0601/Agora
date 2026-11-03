<?php 
    $pagina_actual = 'admin_morosidad';
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);

    $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
              7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
?>

<main class="p-5" role="main">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1"><i class="bi bi-exclamation-diamond me-2 text-danger"></i>Control de Morosidad</h1>
            <p class="text-secondary small mb-0">Recibos pendientes por apartamento, organizados por mes y año.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-cash-stack me-1"></i> Deuda total: $<?= number_format($total_deuda ?? 0, 2) ?>
            </span>
        </div>
    </header>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="<?= site_url('admin/finanzas/morosidad') ?>" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label small text-secondary fw-medium">Año</label>
                    <select name="anio" class="form-select form-select-sm">
                        <?php for ($y = ($anio_actual ?? date('Y')); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= ($filtro_anio ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small text-secondary fw-medium">Mes</label>
                    <select name="mes" class="form-select form-select-sm">
                        <option value="">Todos los meses</option>
                        <?php foreach ($meses as $num => $nombre): ?>
                            <option value="<?= $num ?>" <?= ($filtro_mes ?? '') == $num ? 'selected' : '' ?>><?= $nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                </div>
                <div class="col-auto">
                    <a href="<?= site_url('admin/finanzas/morosidad') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de morosos -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaMorosidad">
                    <thead class="table-light">
                        <tr>
                            <th class="text-secondary">Apartamento</th>
                            <th class="text-secondary">Torre / Edificio</th>
                            <th class="text-secondary">Residente</th>
                            <th class="text-secondary">Teléfono</th>
                            <th class="text-secondary">Período</th>
                            <th class="text-secondary text-end">Monto Pendiente</th>
                            <th class="text-secondary text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $morosos = $morosos ?? [];
                        if (!empty($morosos)):
                            foreach ($morosos as $m):
                        ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($m['nro_apartamento']) ?></span>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($m['nombre_edificio_torre'] ?? '—') ?></td>
                                <td class="fw-medium">
                                    <i class="bi bi-person text-muted me-1"></i>
                                    <?= htmlspecialchars($m['nombre_completo'] ?? 'Sin asignar') ?>
                                </td>
                                <td class="small"><?= htmlspecialchars($m['telefono'] ?? '—') ?></td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3">
                                        <?= $meses[$m['mes']] ?? $m['mes'] ?> <?= $m['anio'] ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-danger">$<?= number_format((float) $m['monto_total'], 2) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 small fw-semibold">
                                        <i class="bi bi-exclamation-circle me-1"></i>Pendiente
                                    </span>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-emoji-smile fs-1 d-block mb-2 text-success"></i>
                                    <p class="mb-0 fs-6">¡Sin morosidad! No hay recibos pendientes para el período seleccionado.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <?php if (!empty($morosos)): ?>
    <div class="row mt-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                <div class="card-body text-center p-3">
                    <p class="text-danger small fw-bold mb-1">TOTAL ADEUDADO</p>
                    <h3 class="fw-bold text-danger mb-0">$<?= number_format($total_deuda ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                <div class="card-body text-center p-3">
                    <p class="text-warning small fw-bold mb-1">RECIBOS PENDIENTES</p>
                    <h3 class="fw-bold text-warning mb-0"><?= count($morosos) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
                <div class="card-body text-center p-3">
                    <p class="text-info small fw-bold mb-1">APTOS. MOROSOS</p>
                    <h3 class="fw-bold text-info mb-0"><?= count(array_unique(array_column($morosos, 'nro_apartamento'))) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>

