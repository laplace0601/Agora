<?php 
    $pagina_actual = 'admin_soporte'; 
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container my-5" role="main">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold h3 text-dark mb-0">Gestión de Tickets de Soporte</h1>
                <p class="text-secondary mt-1">Revisa las incidencias reportadas por los residentes y actualiza su estatus.</p>
            </div>
        </div>
    </div>

    <!-- Filtros Rápidos (Opcional, de momento solo informativo) -->
    <div class="row mb-4">
        <div class="col-12 d-flex gap-2">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-clock me-1"></i> Abiertos</span>
            <span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="bi bi-gear-fill me-1"></i> En Proceso</span>
            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Resueltos</span>
        </div>
    </div>

    <!-- Lista de Tickets -->
    <div class="row">
        <div class="col-12">
            <div class="row g-4">
                <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="col-12">
                            <div class="card-agora-form p-4">
                                <div class="row align-items-center">
                                    
                                    <!-- Info Ticket -->
                                    <div class="col-12 col-md-5 mb-3 mb-md-0">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3 mt-1">
                                                <?php if ($ticket['estado'] === 'Abierto'): ?>
                                                    <i class="bi bi-exclamation-circle text-warning fs-3"></i>
                                                <?php elseif ($ticket['estado'] === 'En Proceso'): ?>
                                                    <i class="bi bi-gear text-info fs-3"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-check-circle text-success fs-3"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="fw-bold text-dark">#<?= str_pad($ticket['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><?= esc($ticket['categoria']) ?></span>
                                                    <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><?= date('d/m/Y h:i A', strtotime($ticket['fecha_creacion'])) ?></small>
                                                </div>
                                                <h5 class="fw-bold text-dark mb-1"><?= esc($ticket['asunto']) ?></h5>
                                                <p class="text-secondary small mb-0"><?= nl2br(esc($ticket['detalle'])) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info Solicitante -->
                                    <div class="col-12 col-md-4 mb-3 mb-md-0 border-start border-end px-md-4">
                                        <h6 class="fw-semibold text-dark mb-2 small text-uppercase">Solicitante</h6>
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-person-fill text-muted me-2"></i>
                                            <span class="fw-medium text-dark"><?= esc($ticket['nombre_completo'] ?? 'Residente Desconocido') ?></span>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-envelope-fill text-muted me-2"></i>
                                            <a href="mailto:<?= esc($ticket['correo']) ?>" class="text-decoration-none small"><?= esc($ticket['correo']) ?></a>
                                        </div>
                                        <?php if (!empty($ticket['telefono'])): ?>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-telephone-fill text-muted me-2"></i>
                                            <a href="tel:<?= esc($ticket['telefono']) ?>" class="text-decoration-none small text-secondary"><?= esc($ticket['telefono']) ?></a>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="col-12 col-md-3 text-md-end">
                                        <form action="<?= site_url('admin/soporte/validar') ?>" method="POST" class="d-inline-flex flex-column align-items-md-end w-100">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                            
                                            <label class="small fw-semibold text-muted mb-2 text-start text-md-end w-100">Actualizar Estado:</label>
                                            <div class="input-group input-group-sm mb-2 w-100" style="max-width: 200px;">
                                                <select class="form-select" name="estado">
                                                    <option value="Abierto" <?= $ticket['estado'] === 'Abierto' ? 'selected' : '' ?>>Abierto</option>
                                                    <option value="En Proceso" <?= $ticket['estado'] === 'En Proceso' ? 'selected' : '' ?>>En Proceso</option>
                                                    <option value="Resuelto" <?= $ticket['estado'] === 'Resuelto' ? 'selected' : '' ?>>Resuelto</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary px-3">Guardar</button>
                                            </div>
                                            <?php if ($ticket['estado'] === 'Resuelto' && !empty($ticket['fecha_resolucion'])): ?>
                                                <small class="text-success"><i class="bi bi-check2-all me-1"></i> Resuelto el <?= date('d/m/Y', strtotime($ticket['fecha_resolucion'])) ?></small>
                                            <?php endif; ?>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-secondary fw-medium">No hay tickets de soporte reportados actualmente.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>
