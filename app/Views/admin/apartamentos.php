<?php
$pagina_actual = 'admin_apartamentos'; // Conecta con public/css/admin_apartamentos.css
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
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-5 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1">Estructura Inmobiliaria</h1>
            <p class="text-secondary small mb-0">Registra y administra los condominios, torres y bloques de apartamentos.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-agora-admin text-white rounded-pill px-4 py-2 small shadow-sm" data-bs-toggle="modal" data-bs-target="#Mcondominiotorre">
                <i class="bi bi-building-add me-1"></i> Registrar Condominio
            </button>
            <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 small" data-bs-toggle="modal" data-bs-target="#Mbloqueapartamento">
                <i class="bi bi-door-open me-1"></i> Registrar Apartamento
            </button>
        </div>
    </div>

    <section class="text-center py-5 bg-white rounded-4 shadow-sm border">
        <i class="bi bi-houses text-muted display-4 mb-3 d-block"></i>
        <h2 class="h5 fw-bold text-dark mb-1">Módulo Inmobiliario Activo</h2>
        <p class="text-secondary small mx-auto" style="max-width: 400px;">Utiliza los controles superiores para ingresar nuevos registros a la base de datos simulada del sistema.</p>
    </section>
</main>

<div class="modal fade" id="Mcondominiotorre" tabindex="-1" aria-labelledby="McondominiotorreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="McondominiotorreLabel">Nuevo Registro de Condominio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/apartamentos/registrar-condominio') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="condo-name" name="nombre_condo" required placeholder="Nombre">
                        <label for="condo-name">Nombre del Condominio / Torre *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="condo-dir" name="direccion_condo" style="height: 90px" required placeholder="Dirección"></textarea>
                        <label for="condo-dir">Dirección Geográfica *</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="condo-owner" name="propietario_condo" required placeholder="Propietario">
                        <label for="condo-owner">Nombre del Propietario General *</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="alicuota" name="alicuota_condo" step="any" min="0" required placeholder="Alícuota">
                        <label for="alicuota">Alícuota Base *</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="submit" class="btn btn-agora-admin text-white w-100 py-2.5 rounded-3 fw-medium">
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
            <form action="<?= site_url('admin/apartamentos/registrar-apartamento') ?>" method="POST">
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
                    <button type="submit" class="btn btn-agora-admin text-white w-100 py-2.5 rounded-3 fw-medium">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Guardar Apartamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>