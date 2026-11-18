<?php
$pagina_actual = 'admin_finanzas_cobro'; // Conecta con public/css/admin_finanzas_cobro.css
echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="p-5" role="main">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 px-md-5">
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- FORMULARIO -->
                    <div class="text-center mb-4">
                        <h1 class="fw-bold h3 text-dark mb-2">Generar Cobro</h1>
                        <p class="text-secondary small">Rellene los datos para efectuar la creación de recibos del mes.</p>
                    </div>

                            <form id="formFacturacion" action="<?= base_url('admin/finanzas/facturar') ?>" method="POST">
                                <?= csrf_field() ?>

                                <!-- Selector de Condominio (datos reales desde BD) -->
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="condominio_id" name="condominio_id" required>
                                        <option value="" disabled selected>Selecciona el condominio</option>
                                        <?php
                                        $condominios = $condominios ?? [];
                                        foreach ($condominios as $condo): ?>
                                            <option value="<?= $condo['id'] ?>">
                                                <?= htmlspecialchars($condo['nombre_condominio']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="condominio_id">Condominio / Edificio *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" id="mes" name="mes" required>
                                        <option value="" disabled <?= empty($mes_actual) ? 'selected' : '' ?>>Selecciona el mes</option>
                                        <option value="1" <?= (($mes_actual ?? 0) == 1  ? 'selected' : '') ?>>Enero</option>
                                        <option value="2" <?= (($mes_actual ?? 0) == 2  ? 'selected' : '') ?>>Febrero</option>
                                        <option value="3" <?= (($mes_actual ?? 0) == 3  ? 'selected' : '') ?>>Marzo</option>
                                        <option value="4" <?= (($mes_actual ?? 0) == 4  ? 'selected' : '') ?>>Abril</option>
                                        <option value="5" <?= (($mes_actual ?? 0) == 5  ? 'selected' : '') ?>>Mayo</option>
                                        <option value="6" <?= (($mes_actual ?? 0) == 6  ? 'selected' : '') ?>>Junio</option>
                                        <option value="7" <?= (($mes_actual ?? 0) == 7  ? 'selected' : '') ?>>Julio</option>
                                        <option value="8" <?= (($mes_actual ?? 0) == 8  ? 'selected' : '') ?>>Agosto</option>
                                        <option value="9" <?= (($mes_actual ?? 0) == 9  ? 'selected' : '') ?>>Septiembre</option>
                                        <option value="10" <?= (($mes_actual ?? 0) == 10 ? 'selected' : '') ?>>Octubre</option>
                                        <option value="11" <?= (($mes_actual ?? 0) == 11 ? 'selected' : '') ?>>Noviembre</option>
                                        <option value="12" <?= (($mes_actual ?? 0) == 12 ? 'selected' : '') ?>>Diciembre</option>
                                    </select>
                                    <label for="mes">Mes de Cobro *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="anio" name="anio"
                                        min="2024" max="2035"
                                        value="<?= $anio_actual ?? date('Y') ?>" required>
                                    <label for="anio">Año *</label>
                                </div>

                                <div class="input-group mb-4">
                                    <span class="input-group-text bg-light text-secondary border-end-0" style="border-radius: 10px 0 0 10px;">$</span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="number" step="0.01" class="form-control border-start-0"
                                            id="monto_global_gastos" name="monto_global_gastos" placeholder="0.00"
                                            style="border-radius: 0 10px 10px 0;" required>
                                        <label for="monto_global_gastos">Monto Global de Gastos *</label>
                                    </div>
                                </div>

                                <div class="form-floating mb-4">
                                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ej. Gasto de mantenimiento ordinario - Junio" style="height: 100px" required></textarea>
                                    <label for="descripcion">Descripción del Cobro *</label>
                                </div>

                                <button type="submit" id="btnSubmit" class="btn btn-agora-admin w-100 py-3 rounded-3 fw-medium fs-5 text-white shadow-sm">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="btnSpinner"></span>
                                    <i class="bi bi-wallet2 me-2" id="btnIcon"></i> <span id="btnText">Generar Recibos</span>
                                </button>
                            </form>
                </div>
            </div>

        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('formFacturacion');
        const btnSubmit = document.getElementById('btnSubmit');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnIcon = document.getElementById('btnIcon');
        const btnText = document.getElementById('btnText');

        if (form && btnSubmit) {
            form.addEventListener('submit', function() {
                if (btnSpinner) btnSpinner.classList.remove('d-none');
                if (btnIcon) btnIcon.classList.add('d-none');
                if (btnText) btnText.textContent = 'Procesando...';
                btnSubmit.disabled = true;
            });
        }
    });
</script>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>