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

            <div class="mb-4">
                <h1 class="fw-bold h3 text-dark">Reportar Pago Mensual</h1>
                <p class="text-secondary">Adjunta la información correspondiente a tu transferencia bancaria.</p>
            </div>

            <div class="card-agora-form p-4 p-md-5">
                <form action="<?= site_url('residente/pago/enviar') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="bancoOrigen" name="banco" required>
                                    <option value="" selected disabled>Selecciona tu banco</option>
                                    <option value="1">Ágora Digital Bank</option>
                                </select>
                                <label for="bancoOrigen">Banco Emisor</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="numReferencia" name="referencia" placeholder="0000" required>
                                <label for="numReferencia">Número de Referencia</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="dropzone-agora p-4 text-center" id="dropzoneContainer">
                                <i class="bi bi-cloud-arrow-up fs-1 text-secondary mb-2 d-block"></i>
                                <span class="d-block text-dark small fw-medium" id="dropzoneText">Arrastra tu captura aquí o haz clic para buscar</span>
                                <input type="file" id="fileComprobante" name="comprobante" class="d-none" accept="image/*,application/pdf" required>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-agora-primary w-100 py-3 rounded-3 fs-5">Enviar Reporte de Pago</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>