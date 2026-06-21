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
                                <label for="bancoOrigen">Banco Emisor</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="numReferencia" name="referencia" placeholder="Número completo de la transacción" required>
                                <label for="numReferencia">Número de Referencia Completo:</label>
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
                                <label for="recibo">Recibo Pendiente</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control" id="monto" name="monto" placeholder="Monto pagado" required>
                                <label for="monto">Monto Pagado ($)</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-success w-100 py-3 rounded-3 fs-5">Enviar Reporte de Pago</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>