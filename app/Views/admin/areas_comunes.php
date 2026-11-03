<?php 
    $pagina_actual = 'admin_areas_comunes';
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="p-5" role="main">

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

    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-5 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1"><i class="bi bi-tree me-2"></i>Áreas Comunes</h1>
            <p class="text-secondary small mb-0">Gestiona las áreas verdes, salones y espacios compartidos del condominio.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary text-white rounded-pill px-4 py-2 small shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaArea">
                <i class="bi bi-plus-lg me-1"></i> Registrar Área
            </button>
            <a href="<?= site_url('admin/reservas') ?>" class="btn btn-outline-success rounded-pill px-4 py-2 small">
                <i class="bi bi-calendar-check me-1"></i> Ver Reservas
            </a>
        </div>
    </header>

    <!-- Listado de Áreas Comunes -->
    <section class="row g-4">
        <?php 
        $areas = $areas ?? [];
        if (!empty($areas)): 
            foreach ($areas as $area): 
                $badgeClass = match($area['estado']) {
                    'Activo'        => 'bg-success-subtle text-success',
                    'Mantenimiento' => 'bg-warning-subtle text-warning',
                    'Inactivo'      => 'bg-danger-subtle text-danger',
                    default         => 'bg-secondary-subtle text-secondary',
                };
        ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background:rgba(34,197,94,0.1); width:48px; height:48px;">
                                <i class="bi bi-tree-fill fs-4 text-success"></i>
                            </div>
                            <div>
                                <h2 class="h6 fw-bold mb-0 text-dark"><?= htmlspecialchars($area['nombre']) ?></h2>
                                <?php if (!empty($area['capacidad_maxima'])): ?>
                                    <small class="text-muted"><i class="bi bi-people me-1"></i>Capacidad: <?= $area['capacidad_maxima'] ?> personas</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($area['descripcion'])): ?>
                            <p class="text-secondary small mb-3"><?= htmlspecialchars($area['descripcion']) ?></p>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-1 small fw-semibold">
                                <?= $area['estado'] ?>
                            </span>
                            <?php if ($area['estado'] !== 'Inactivo'): ?>
                                <a href="<?= site_url('admin/areas-comunes/eliminar/' . $area['id']) ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                   onclick="return confirm('¿Desactivar esta área común?');">
                                    <i class="bi bi-x-circle me-1"></i> Desactivar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            endforeach;
        else: 
        ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-tree text-muted fs-1 d-block mb-2"></i>
                <p class="text-secondary fs-5">No hay áreas comunes registradas.</p>
                <button type="button" class="btn btn-primary text-white rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaArea">
                    <i class="bi bi-plus-lg me-1"></i> Registrar la primera área
                </button>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Modal: Nueva Área Común -->
<div class="modal fade" id="modalNuevaArea" tabindex="-1" aria-labelledby="modalNuevaAreaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalNuevaAreaLabel">Registrar Área Común</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/areas-comunes/guardar') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="area-nombre" name="nombre" required placeholder="Nombre">
                        <label for="area-nombre">Nombre del Área *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="area-descripcion" name="descripcion" style="height: 90px" placeholder="Descripción"></textarea>
                        <label for="area-descripcion">Descripción (opcional)</label>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="area-capacidad" name="capacidad_maxima" min="1" placeholder="20">
                                <label for="area-capacidad">Capacidad máxima</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <select class="form-select" id="area-estado" name="estado">
                                    <option value="Activo" selected>Activo</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                <label for="area-estado">Estado</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-primary text-white w-100 py-2.5 rounded-3 fw-medium">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Guardar Área Común
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>

