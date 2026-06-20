<?php 
    $pagina_actual = 'admin_cartelera'; // Llama a public/css/admin_cartelera.css
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
            <h1 class="fw-bold h3 text-dark mb-1">Tablón de Anuncios</h1>
            <p class="text-secondary small mb-0">Gestiona los comunicados activos que visualizan los residentes.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary text-white rounded-pill px-4 py-2 small shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoAnuncio">
                <i class="bi bi-plus-lg me-1"></i> Añadir Anuncio
            </button>
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 small" data-bs-toggle="modal" data-bs-target="#modalHistorial">
                <i class="bi bi-clock-history me-1"></i> Ver Historial
            </button>
        </div>
    </header>

    <section class="row g-4" aria-label="Anuncios activos">
        <?php 
        // $anuncios_activos es pasado por AdminController::cartelera()
        $anuncios_activos = $anuncios_activos ?? [];
        if (!empty($anuncios_activos)): 
            foreach ($anuncios_activos as $anuncio): 
        ?>
            <div class="col-12 col-md-6 col-lg-4">
                <article class="card h-100 anuncio-card border-0 border-start border-warning border-3 shadow-sm bg-white rounded-4 overflow-hidden">
                    <a href="<?= site_url('admin/cartelera/eliminar/' . $anuncio['id']) ?>" 
                       class="btn-eliminar" 
                       title="Eliminar anuncio"
                       onclick="return confirm('¿Eliminar este anuncio?');">&times;</a>
                    <div class="card-body pt-4 pe-4">
                        <h2 class="h5 fw-bold text-dark pe-3 mb-2"><?= htmlspecialchars($anuncio['titulo']) ?></h2>
                        <p class="text-secondary small lh-base"><?= nl2br(htmlspecialchars($anuncio['contenido'])) ?></p>
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($anuncio['fecha_publicacion'])) ?></small>
                    </div>
                </article>
            </div>
        <?php 
            endforeach;
        else: 
        ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-megaphone text-muted fs-1 d-block mb-2"></i>
                <p class="text-secondary fs-5">No hay anuncios publicados en este momento.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Modal: Nuevo Anuncio -->
<div class="modal fade" id="modalNuevoAnuncio" tabindex="-1" aria-labelledby="modalNuevoAnuncioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalNuevoAnuncioLabel">Publicar Nuevo Anuncio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/cartelera/publicar') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="titulo" name="titulo" required placeholder="Título">
                        <label for="titulo">Título del Anuncio *</label>
                    </div>
                    <div class="form-floating mb-2">
                        <textarea class="form-control" id="descripcion" name="descripcion" style="height: 120px" required placeholder="Descripción"></textarea>
                        <label for="descripcion">Descripción detallada *</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-primary text-white w-100 py-2.5 rounded-3 fw-medium">
                        <i class="bi bi-send me-1"></i> Publicar en Cartelera
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Historial -->
<div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalHistorialLabel">Historial de Anuncios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <?php 
                $historial_anuncios = $historial_anuncios ?? [];
                if (empty($historial_anuncios)): ?>
                    <p class="text-muted text-center py-3">No hay registros en el historial.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($historial_anuncios as $anuncio): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                <span class="text-truncate text-dark small fw-medium" style="max-width: 70%;">
                                    <?= htmlspecialchars($anuncio['titulo']) ?>
                                </span>
                                <?php if (($anuncio['estado'] ?? 'publicado') === 'publicado'): ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success px-2.5 py-1 small">Activo</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-2.5 py-1 small">Borrado</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>