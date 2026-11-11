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
                $borderClass = match($area['estado']) {
                    'Activo'        => 'border-start border-success border-4',
                    'Mantenimiento' => 'border-start border-warning border-4',
                    'Inactivo'      => 'border-start border-danger border-4',
                    default         => '',
                };
        ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 bg-white <?= $borderClass ?>">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background:rgba(34,197,94,0.1); width:48px; height:48px;">
                                <i class="bi bi-tree-fill fs-4 text-success"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="h6 fw-bold mb-0 text-dark"><?= htmlspecialchars($area['nombre']) ?></h2>
                                <?php if (!empty($area['capacidad_maxima'])): ?>
                                    <small class="text-muted"><i class="bi bi-people me-1"></i>Capacidad: <?= $area['capacidad_maxima'] ?> personas</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($area['descripcion'])): ?>
                            <p class="text-secondary small mb-3 flex-grow-1"><?= htmlspecialchars($area['descripcion']) ?></p>
                        <?php else: ?>
                            <div class="flex-grow-1"></div>
                        <?php endif; ?>

                        <!-- Acciones: Selector de Estado (3 opciones) y Edición -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto gap-2">
                            <!-- Dropdown de Estado -->
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle rounded-pill px-3 py-1 fw-semibold <?= $badgeClass ?> border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Cambiar estado del área">
                                    <?= $area['estado'] ?>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                    <li><h6 class="dropdown-header small text-uppercase text-muted">Seleccionar Estado</h6></li>
                                    <li>
                                        <form action="<?= site_url('admin/areas-comunes/cambiar-estado') ?>" method="POST" class="m-0">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                            <input type="hidden" name="estado" value="Activo">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $area['estado'] === 'Activo' ? 'active fw-bold' : '' ?>">
                                                <i class="bi bi-check-circle-fill text-success"></i> Activo
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="<?= site_url('admin/areas-comunes/cambiar-estado') ?>" method="POST" class="m-0">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                            <input type="hidden" name="estado" value="Mantenimiento">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $area['estado'] === 'Mantenimiento' ? 'active fw-bold' : '' ?>">
                                                <i class="bi bi-tools text-warning"></i> Mantenimiento
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="<?= site_url('admin/areas-comunes/cambiar-estado') ?>" method="POST" class="m-0">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                            <input type="hidden" name="estado" value="Inactivo">
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $area['estado'] === 'Inactivo' ? 'active fw-bold' : '' ?>">
                                                <i class="bi bi-x-circle-fill text-danger"></i> Inactivo
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <!-- Botón Editar -->
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-editar-area"
                                    data-id="<?= $area['id'] ?>"
                                    data-nombre="<?= htmlspecialchars($area['nombre'], ENT_QUOTES) ?>"
                                    data-descripcion="<?= htmlspecialchars($area['descripcion'] ?? '', ENT_QUOTES) ?>"
                                    data-capacidad="<?= $area['capacidad_maxima'] ?? '' ?>"
                                    data-estado="<?= $area['estado'] ?>"
                                    title="Editar datos del área">
                                <i class="bi bi-pencil-square me-1"></i> Editar
                            </button>
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

<!-- Modal: Editar Área Común -->
<div class="modal fade" id="modalEditarArea" tabindex="-1" aria-labelledby="modalEditarAreaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditarAreaLabel">Editar Área Común</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/areas-comunes/guardar') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" id="edit-area-id" name="id">
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="edit-area-nombre" name="nombre" required placeholder="Nombre">
                        <label for="edit-area-nombre">Nombre del Área *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="edit-area-descripcion" name="descripcion" style="height: 90px" placeholder="Descripción"></textarea>
                        <label for="edit-area-descripcion">Descripción (opcional)</label>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="edit-area-capacidad" name="capacidad_maxima" min="1" placeholder="20">
                                <label for="edit-area-capacidad">Capacidad máxima</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <select class="form-select" id="edit-area-estado" name="estado">
                                    <option value="Activo">Activo</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                <label for="edit-area-estado">Estado *</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-primary text-white w-100 py-2.5 rounded-3 fw-medium">
                        <i class="bi bi-check-circle me-1"></i> Actualizar Área Común
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarArea'));
    
    document.querySelectorAll('.btn-editar-area').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-area-id').value = this.dataset.id;
            document.getElementById('edit-area-nombre').value = this.dataset.nombre;
            document.getElementById('edit-area-descripcion').value = this.dataset.descripcion;
            document.getElementById('edit-area-capacidad').value = this.dataset.capacidad;
            document.getElementById('edit-area-estado').value = this.dataset.estado;
            
            modalEditar.show();
        });
    });
});
</script>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>
