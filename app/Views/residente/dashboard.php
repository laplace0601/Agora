<?php
$pagina_actual = 'residente_dashboard'; // Conecta automáticamente con dashboard.css y dashboard.js
echo view('template/residente_header', ['pagina_actual' => $pagina_actual, 'apartamentos' => $apartamentos ?? []]);
?>

<main class="container my-5" role="main">
    <div class="row g-5">

        <section class="col-12 col-lg-8" aria-labelledby="titulo-cartelera">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 id="titulo-cartelera" class="fw-bold h3 text-dark">Cartelera Comunitaria</h1>
                    <p class="text-secondary mb-0">Entérate de las últimas novedades de tu edificio.</p>
                </div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="bi bi-filter me-1"></i> Ver todo</button>
            </div>

            <div class="d-flex flex-column gap-4">

                <?php
                $comunicados = $comunicados ?? [];
                if (!empty($comunicados)):
                    foreach ($comunicados as $comunicado):
                ?>
                        <article class="card-comunicado p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-comunidad bg-primary px-3 py-2 rounded-pill fw-semibold small text-white">Anuncio</span>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y h:i A', strtotime($comunicado['fecha_publicacion'])) ?></small>
                            </div>
                            <h2 class="h5 fw-bold text-dark mb-2"><?= htmlspecialchars($comunicado['titulo']) ?></h2>
                            <p class="text-secondary small lh-base">
                                <?= nl2br(htmlspecialchars($comunicado['contenido'])) ?>
                            </p>
                            <hr class="text-light-grid my-3">
                            <span class="text-dark small fw-medium"><i class="bi bi-person me-1"></i> <?= htmlspecialchars($comunicado['correo_autor'] ?? 'Administración') ?></span>
                        </article>
                    <?php
                    endforeach;
                else:
                    ?>
                    <div class="text-center py-5 bg-white rounded-4 border border-light shadow-sm">
                        <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                        <p class="text-secondary mb-0">No hay comunicados activos en este momento.</p>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <aside class="col-12 col-lg-4" aria-labelledby="titulo-accesos">
            <h2 id="titulo-accesos" class="fw-bold h4 text-dark mb-4">Trámites y Consultas</h2>

            <div class="d-flex flex-column gap-3">
                <a href="<?= site_url('residente/pago') ?>" class="btn-acceso-rapido text-decoration-none d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-cash-coin me-2 text-warning"></i> Reportar un Pago</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>

                <a href="<?= site_url('residente/soporte') ?>" class="btn-acceso-rapido text-decoration-none d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-exclamation-triangle me-2 text-danger"></i> Crear Ticket de Soporte</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>

                <a href="<?= site_url('residente/finanzas') ?>" class="btn-acceso-rapido text-decoration-none d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-file-earmark-text me-2 text-info"></i> Mis Recibos y Solvencias</span>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </a>
            </div>

            <div class="card-estado-cuenta mt-4 p-4 text-white">
                <p class="text-uppercase small fw-bold text-white-50 mb-1">Tu Estado de Cuenta</p>
                <h3 class="fw-bold mb-3" style="color: #F59E0B;">$<?= number_format($deuda_total ?? 0, 2) ?></h3>
                <?php if (($deuda_total ?? 0) <= 0): ?>
                    <span class="badge bg-success rounded-pill px-3 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Al día</span>
                <?php else: ?>
                    <span class="badge bg-danger rounded-pill px-3 py-1.5"><i class="bi bi-exclamation-triangle-fill me-1"></i> Con deuda</span>
                <?php endif; ?>
            </div>
        </aside>

    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>