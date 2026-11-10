<?php
$pagina_actual = 'residente_reportar_pago'; // Define el nombre exacto para conectar CSS y JS
echo view('template/residente_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container my-5" role="main">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <a href="<?= site_url('residente/dashboard') ?>" class="text-decoration-none text-secondary small d-inline-flex align-items-center mb-4">
                <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
            </a>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <h1 class="fw-bold h3 text-dark mb-1">Reportar Pago Mensual</h1>
                <p class="text-secondary">Transfiere a las cuentas oficiales del condominio y registra el comprobante a continuación.</p>
            </div>

            <!-- ======================================================== -->
            <!-- CARD DE DATOS DE PAGO DESTINO (MARCA BLANCA)             -->
            <!-- ======================================================== -->
            <?php
            $bancoDestino    = bancoPago();
            $cedulaDestino   = cedulaPago();
            $telefonoDestino = telefonoPago();
            $titularDestino  = nombreEmpresa();
            $hayDatosPago    = !empty($bancoDestino) || !empty($cedulaDestino) || !empty($telefonoDestino);
            ?>

            <?php if ($hayDatosPago): ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border-left: 5px solid var(--agora-primary, #007bff) !important; background: #fdfdfd;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 text-white shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: var(--agora-primary, #007bff);">
                                    <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">Datos de Pago Móvil / Transferencia</h5>
                                    <small class="text-secondary">Emite tu pago a los datos receptores autorizados</small>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 text-uppercase fw-semibold" style="background: rgba(0, 123, 255, 0.1); color: var(--agora-primary, #007bff);">
                                <i class="bi bi-shield-check me-1"></i> Cuenta Oficial
                            </span>
                        </div>

                        <div class="row g-3 pt-1">
                            <?php if (!empty($bancoDestino)): ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="bg-light p-3 rounded-3 border h-100">
                                        <div class="text-secondary small fw-semibold text-uppercase mb-1">
                                            <i class="bi bi-bank me-1" style="color: var(--agora-primary, #007bff);"></i> Banco Destino
                                        </div>
                                        <div class="fw-bold text-dark fs-6 user-select-all"><?= esc($bancoDestino) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($cedulaDestino)): ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="bg-light p-3 rounded-3 border h-100 position-relative">
                                        <div class="text-secondary small fw-semibold text-uppercase mb-1">
                                            <i class="bi bi-person-vcard me-1" style="color: var(--agora-primary, #007bff);"></i> Cédula / RIF
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-bold text-dark fs-6 user-select-all" id="txt-cedula-destino"><?= esc($cedulaDestino) ?></span>
                                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-secondary" onclick="copiarDato('txt-cedula-destino', this)" title="Copiar Cédula">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($telefonoDestino)): ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="bg-light p-3 rounded-3 border h-100 position-relative">
                                        <div class="text-secondary small fw-semibold text-uppercase mb-1">
                                            <i class="bi bi-phone me-1" style="color: var(--agora-primary, #007bff);"></i> Pago Móvil
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-bold text-dark fs-6 user-select-all" id="txt-telefono-destino"><?= esc($telefonoDestino) ?></span>
                                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-secondary" onclick="copiarDato('txt-telefono-destino', this)" title="Copiar Teléfono">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($titularDestino)): ?>
                                <div class="col-12">
                                    <div class="bg-light p-2 px-3 rounded-3 border small text-secondary d-flex align-items-center">
                                        <i class="bi bi-building me-2" style="color: var(--agora-primary, #007bff);"></i>
                                        <span><strong>Beneficiario:</strong> <?= esc($titularDestino) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Cuentas bancarias en configuración:</strong>
                        <div class="small text-muted">La administración del condominio aún no ha publicado los datos bancarios de destino. Si tienes dudas, contáctalos directamente.</div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ======================================================== -->
            <!-- FORMULARIO DE REPORTE DE PAGO                            -->
            <!-- ======================================================== -->
            <div class="card-agora-form p-4 p-md-5 bg-white shadow-sm rounded-4 border border-light">
                <h5 class="fw-bold text-dark mb-4">
                    <i class="bi bi-pencil-square me-2" style="color: var(--agora-primary, #007bff);"></i>Registrar Datos de tu Transacción
                </h5>

                <form action="<?= site_url('residente/pago/enviar') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="bancoOrigen" name="banco" required>
                                    <option value="" selected disabled>Selecciona tu banco</option>
                                    <option value="1">Banco de Venezuela (BDV)</option>
                                    <option value="2">Banesco Banco Universal</option>
                                    <option value="3">BBVA Provincial</option>
                                    <option value="4">Banco Nacional de Crédito (BNC)</option>
                                    <option value="5">Mercantil Banco Universal</option>
                                    <option value="6">Bancamiga Banco Universal</option>
                                    <option value="7">Banco del Tesoro</option>
                                    <option value="8">Banco Digital de los Trabajadores (BDT)</option>
                                    <option value="9">Bancaribe Banco Universal</option>
                                    <option value="10">Banco Exterior</option>
                                    <option value="11">Banco Fondo Común (BFC)</option>
                                    <option value="12">Banco Venezolano de Crédito</option>
                                    <option value="13">Banco Activo</option>
                                    <option value="14">Banco del Caribe</option>
                                    <option value="15">Banco Caroní</option>
                                    <option value="16">Bancrecer</option>
                                    <option value="17">Banplus</option>
                                    <option value="18">Banco Plaza</option>
                                    <option value="19">100% Banco</option>
                                    <option value="20">Del Sur Banco Universal</option>
                                    <option value="21">Banco Sofitasa</option>
                                    <option value="22">Banco de la Gente Emprendedora (Bangente)</option>
                                    <option value="23">Mi Banco</option>
                                    <option value="24">N58</option>
                                </select>
                                <label for="bancoOrigen">Banco Emisor (Tu Banco) *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="numReferencia" name="referencia" placeholder="Número completo de la transacción" required>
                                <label for="numReferencia">Número de Referencia Completo *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="recibo" name="recibo_id" required>
                                    <option value="" selected disabled>Selecciona el recibo a pagar</option>
                                    <?php if (!empty($recibos_pendientes)): ?>
                                        <?php foreach ($recibos_pendientes as $recibo): ?>
                                            <option value="<?= $recibo['id'] ?>">Recibo #<?= $recibo['id'] ?> - $<?= number_format($recibo['monto_total'], 2) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No hay recibos pendientes</option>
                                    <?php endif; ?>
                                </select>
                                <label for="recibo">Recibo Pendiente *</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" id="monto" name="monto" placeholder="Monto pagado" required>
                                <label for="monto">Monto Pagado ($) *</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn text-white w-100 py-3 rounded-pill fs-6 fw-bold shadow-sm" style="background: var(--agora-primary, #007bff); border: none;">
                                <i class="bi bi-send-check me-2"></i>Enviar Reporte de Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function copiarDato(elementId, btn) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const texto = el.textContent.trim();
    navigator.clipboard.writeText(texto).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check text-success fs-5"></i>';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 1500);
    });
}
</script>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>
