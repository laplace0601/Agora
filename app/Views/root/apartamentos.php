<?php
$pagina_actual = 'super_apartamentos'; // Conecta con public/css/super_apartamentos.css
echo view('template/super_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container py-5" role="main">

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

    <!-- Cabecera de la seccion -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-5">
        <div>
            <h1 class="fw-bold h3 mb-1" style="color:#0F172A;">Estructura Inmobiliaria</h1>
            <p class="text-secondary small mb-0">Registra y administra los condominios, torres y bloques de apartamentos.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-super-primary text-white rounded-pill px-4 py-2 shadow-sm fw-semibold" style="background-color: #0F172A; border-color: #0F172A;"
                data-bs-toggle="modal" data-bs-target="#Mcondominiotorre">
                <i class="bi bi-building-add me-1"></i> Registrar Condominio
            </button>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold"
                data-bs-toggle="modal" data-bs-target="#Mbloqueapartamento">
                <i class="bi bi-door-open me-1"></i> Registrar Apartamento
            </button>
        </div>
    </div>

    <!-- Panel de condominios registrados -->
    <div class="row g-4">
        <?php if (!empty($condominios)): ?>
            <?php foreach ($condominios as $condo): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background:rgba(15,23,42,0.08); width:48px; height:48px;">
                                    <i class="bi bi-buildings fs-4" style="color:#0F172A;"></i>
                                </div>
                                <div>
                                    <h2 class="h6 fw-bold mb-0" style="color:#0F172A;"><?= htmlspecialchars($condo['nombre_condominio']) ?></h2>
                                    <small class="text-muted"><?= htmlspecialchars($condo['propietario'] ?? '—') ?></small>
                                </div>
                            </div>
                            <?php if (!empty($condo['direccion'])): ?>
                                <p class="text-secondary small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($condo['direccion']) ?></p>
                            <?php endif; ?>
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1 small fw-semibold">
                                <i class="bi bi-check-circle me-1"></i>Activo
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                    <i class="bi bi-houses text-muted display-4 mb-3 d-block"></i>
                    <h2 class="h5 fw-bold mb-1" style="color:#0F172A;">Sin condominios registrados</h2>
                    <p class="text-secondary small mx-auto mb-4" style="max-width:380px;">
                        Usa el botón <strong>Registrar Condominio</strong> para agregar el primero.
                    </p>
                    <button type="button" class="btn btn-super-primary text-white rounded-pill px-4 py-2 shadow-sm fw-semibold" style="background-color: #0F172A; border-color: #0F172A;"
                        data-bs-toggle="modal" data-bs-target="#Mcondominiotorre">
                        <i class="bi bi-building-add me-2"></i>Registrar primer condominio
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

</main>

<div class="modal fade" id="Mcondominiotorre" tabindex="-1" aria-labelledby="McondominiotorreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="McondominiotorreLabel">Nuevo Registro de Condominio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('super/apartamentos/registrar-condominio') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="condo-name" name="nombre_condo" required placeholder="Nombre">
                        <label for="condo-name">Nombre del Condominio / Torre *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="condo-rif" name="rif_jurisdiccion" required placeholder="RIF">
                        <label for="condo-rif">RIF de Jurisdicción *</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-agora-admin text-white w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Guardar Condominio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="Mbloqueapartamento" tabindex="-1" aria-labelledby="MbloqueapartamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="MbloqueapartamentoLabel">Nuevo Registro de Apartamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('super/apartamentos/registrar-apartamento') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="apto-num" name="numero_apto" required placeholder="Apartamento">
                        <label for="apto-num">Identificador / Número de Apartamento *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="apto-dir" name="direccion_apto" style="height: 90px" required placeholder="Dirección"></textarea>
                        <label for="apto-dir">Ubicación Interna (Bloque/Piso) *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="apto-owner" name="propietario_apto" required placeholder="Propietario">
                        <label for="apto-owner">Nombre del Residente/Propietario *</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="alicuotaapartamento" name="alicuota_apto" step="any" min="0" required placeholder="Alícuota">
                        <label for="alicuotaapartamento">Alícuota Individual *</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-agora-admin text-white w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Guardar Apartamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo view('template/super_footer', ['pagina_actual' => $pagina_actual]); ?>