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
                                            <strong>Método:</strong> <?= htmlspecialchars($pago['metodo_pago'] ?? '—') ?><br>
                                            <strong>Ref:</strong> #<?= htmlspecialchars($pago['referencia_transaccion'] ?? '—') ?><br>
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
                                            <!-- Formulario: Aprobar -->
                                            <form action="<?= site_url('admin/finanzas/validar-pago') ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="pago_id" value="<?= $pago['id'] ?>">
                                                <input type="hidden" name="accion" value="aprobar">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-medium shadow-sm">
                                                    <i class="bi bi-check-lg me-1"></i> Aprobar
                                                </button>
                                            </form>
                                            <!-- Formulario: Rechazar -->
                                            <form action="<?= site_url('admin/finanzas/validar-pago') ?>" method="POST" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="pago_id" value="<?= $pago['id'] ?>">
                                                <input type="hidden" name="accion" value="rechazar">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-medium"
                                                        onclick="return confirm('¿Rechazar este pago?');">
                                                    <i class="bi bi-x-lg me-1"></i> Rechazar
                                                </button>
                                            </form>
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
</main>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>