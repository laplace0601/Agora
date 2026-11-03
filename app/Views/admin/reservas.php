<?php 
    $pagina_actual = 'admin_reservas';
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="p-5" role="main">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-5 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1"><i class="bi bi-calendar-check me-2"></i>Reservas de Áreas Comunes</h1>
            <p class="text-secondary small mb-0">Visualiza y gestiona todas las reservas del condominio.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/reservas/nueva') ?>" class="btn btn-primary text-white rounded-pill px-4 py-2 small shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nueva Reserva
            </a>
            <a href="<?= site_url('admin/areas-comunes') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 small">
                <i class="bi bi-tree me-1"></i> Áreas Comunes
            </a>
        </div>
    </header>

    <!-- Tabla de reservas -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-secondary">ID</th>
                            <th class="text-secondary">Área</th>
                            <th class="text-secondary">Apartamento</th>
                            <th class="text-secondary">Fecha</th>
                            <th class="text-secondary">Horario</th>
                            <th class="text-secondary">Estado</th>
                            <th class="text-secondary text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $reservas = $reservas ?? [];
                        if (!empty($reservas)): 
                            foreach ($reservas as $reserva): 
                                $badgeEstado = match($reserva['estado']) {
                                    'Confirmada' => 'bg-success-subtle text-success',
                                    'Pendiente'  => 'bg-warning-subtle text-warning',
                                    'Cancelada'  => 'bg-danger-subtle text-danger',
                                    default      => 'bg-secondary-subtle text-secondary',
                                };
                        ?>
                            <tr>
                                <td class="fw-medium">#<?= $reserva['id'] ?></td>
                                <td><i class="bi bi-tree-fill text-success me-1"></i><?= htmlspecialchars($reserva['nombre_area']) ?></td>
                                <td><span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($reserva['nro_apartamento']) ?></span></td>
                                <td><?= date('d/m/Y', strtotime($reserva['fecha_reserva'])) ?></td>
                                <td>
                                    <i class="bi bi-clock me-1 text-muted"></i>
                                    <?= substr($reserva['hora_inicio'], 0, 5) ?> — <?= substr($reserva['hora_fin'], 0, 5) ?>
                                </td>
                                <td><span class="badge rounded-pill <?= $badgeEstado ?> px-3 py-1 small fw-semibold"><?= $reserva['estado'] ?></span></td>
                                <td class="text-end">
                                    <?php if ($reserva['estado'] !== 'Cancelada'): ?>
                                        <form action="<?= site_url('admin/reservas/cancelar') ?>" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Cancelar esta reserva?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                                    No hay reservas registradas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>

