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
            <button type="button" class="btn btn-success text-white rounded-pill px-4 py-2 small shadow-sm" style="background-color: #198754; border-color: #198754;" data-bs-toggle="modal" data-bs-target="#modalNuevoAnuncio">
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

                    <!-- Imagen adjunta con lightbox -->
                    <?php if (!empty($anuncio['imagen_url'])): ?>
                        <div class="ratio ratio-16x9 bg-light">
                            <img src="<?= base_url($anuncio['imagen_url']) ?>" 
                                 alt="Imagen del anuncio" 
                                 class="card-img-top img-anuncio-zoom" 
                                 style="object-fit: cover; cursor: pointer;"
                                 role="button"
                                 tabindex="0"
                                 title="Clic para ampliar"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#modalImagenZoom"
                                 data-img-src="<?= base_url($anuncio['imagen_url']) ?>"
                                 data-img-alt="<?= htmlspecialchars($anuncio['titulo']) ?>">
                        </div>
                    <?php endif; ?>

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

<!-- ============================================================= -->
<!-- Modal: Lightbox / Zoom de Imagen                              -->
<!-- ============================================================= -->
<div class="modal fade" id="modalImagenZoom" tabindex="-1" aria-labelledby="modalImagenZoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white fw-bold" id="modalImagenZoomLabel">Imagen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="zoomImgSrc" src="" alt="" 
                     class="img-fluid rounded-4 shadow-lg" 
                     style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Anuncio (ETAPA 1: con enctype multipart y campo de imagen) -->
<div class="modal fade" id="modalNuevoAnuncio" tabindex="-1" aria-labelledby="modalNuevoAnuncioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalNuevoAnuncioLabel">Publicar Nuevo Anuncio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/cartelera/publicar') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="titulo" name="titulo" required placeholder="Título">
                        <label for="titulo">Título del Anuncio *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="descripcion" name="descripcion" style="height: 120px" required placeholder="Descripción"></textarea>
                        <label for="descripcion">Descripción detallada *</label>
                    </div>

                    <!-- Campo de imagen adjunta -->
                    <div class="mb-2">
                        <label for="imagen" class="form-label small text-secondary">
                            <i class="bi bi-image me-1"></i> Imagen adjunta (opcional — JPG, PNG o WebP, máx 2 MB)
                        </label>
                        <input type="file" class="form-control" id="imagen" name="imagen" 
                               accept="image/jpeg,image/png,image/webp">
                        <!-- Preview de la imagen seleccionada -->
                        <div id="imagenPreview" class="mt-2 d-none">
                            <img id="imagenPreviewImg" src="" alt="Vista previa" 
                                 class="img-fluid rounded-3 border" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-success text-white w-100 py-2.5 rounded-3 fw-medium" style="background-color: #198754; border-color: #198754;">
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

<!-- Scripts: Vista previa de imagen + Lightbox -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Vista previa de imagen al crear anuncio ---
    const inputImagen = document.getElementById('imagen');
    const preview = document.getElementById('imagenPreview');
    const previewImg = document.getElementById('imagenPreviewImg');

    if (inputImagen) {
        inputImagen.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.classList.add('d-none');
            }
        });
    }

    // --- Lightbox: Al hacer clic en una imagen del feed, cargarla en el modal ---
    const modalZoom = document.getElementById('modalImagenZoom');
    if (modalZoom) {
        modalZoom.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            const imgSrc = trigger.getAttribute('data-img-src');
            const imgAlt = trigger.getAttribute('data-img-alt') || 'Imagen ampliada';
            
            document.getElementById('zoomImgSrc').src = imgSrc;
            document.getElementById('zoomImgSrc').alt = imgAlt;
            document.getElementById('modalImagenZoomLabel').textContent = imgAlt;
        });

        // Limpiar imagen al cerrar el modal para evitar flash del anterior
        modalZoom.addEventListener('hidden.bs.modal', function() {
            document.getElementById('zoomImgSrc').src = '';
        });
    }
});
</script>

<!-- Estilo adicional para el fondo oscuro del lightbox -->
<style>
    #modalImagenZoom .modal-dialog {
        max-width: 90vw;
    }
    #modalImagenZoom .modal-content {
        background: rgba(0, 0, 0, 0.92) !important;
    }
    .img-anuncio-zoom:hover {
        opacity: 0.85;
        transition: opacity 0.2s ease;
    }
    .img-anuncio-zoom {
        transition: opacity 0.2s ease;
    }
</style>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>
