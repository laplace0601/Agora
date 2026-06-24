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
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 px-md-5">
                    <ul class="nav nav-tabs card-header-tabs" id="cobroTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold text-dark" id="form-tab" data-bs-toggle="tab" data-bs-target="#form-pane" type="button" role="tab" aria-controls="form-pane" aria-selected="true">
                                <i class="bi bi-file-earmark-text me-2"></i>Formulario
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-success d-none" id="success-tab" data-bs-toggle="tab" data-bs-target="#success-pane" type="button" role="tab" aria-controls="success-pane" aria-selected="false">
                                <i class="bi bi-check-circle me-2"></i>Éxito
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold text-danger d-none" id="error-tab" data-bs-toggle="tab" data-bs-target="#error-pane" type="button" role="tab" aria-controls="error-pane" aria-selected="false">
                                <i class="bi bi-exclamation-triangle me-2"></i>Error
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <div class="tab-content" id="cobroTabsContent">
                        
                        <!-- PESTAÑA: FORMULARIO -->
                        <div class="tab-pane fade show active" id="form-pane" role="tabpanel" aria-labelledby="form-tab">
                            <div class="text-center mb-4">
                                <h1 class="fw-bold h3 text-dark mb-2">Generar Cobro</h1>
                                <p class="text-secondary small">Rellene los datos para efectuar la facturación masiva del mes.</p>
                            </div>
                            
                            <form id="formFacturacion" action="<?= site_url('admin/finanzas/facturar') ?>" method="POST">
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
                                        <option value="" disabled selected>Selecciona el mes</option>
                                        <option value="1"  <?= (($mes_actual ?? 0) == 1  ? 'selected' : '') ?>>Enero</option>
                                        <option value="2"  <?= (($mes_actual ?? 0) == 2  ? 'selected' : '') ?>>Febrero</option>
                                        <option value="3"  <?= (($mes_actual ?? 0) == 3  ? 'selected' : '') ?>>Marzo</option>
                                        <option value="4"  <?= (($mes_actual ?? 0) == 4  ? 'selected' : '') ?>>Abril</option>
                                        <option value="5"  <?= (($mes_actual ?? 0) == 5  ? 'selected' : '') ?>>Mayo</option>
                                        <option value="6"  <?= (($mes_actual ?? 0) == 6  ? 'selected' : '') ?>>Junio</option>
                                        <option value="7"  <?= (($mes_actual ?? 0) == 7  ? 'selected' : '') ?>>Julio</option>
                                        <option value="8"  <?= (($mes_actual ?? 0) == 8  ? 'selected' : '') ?>>Agosto</option>
                                        <option value="9"  <?= (($mes_actual ?? 0) == 9  ? 'selected' : '') ?>>Septiembre</option>
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
                                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ej. Gasto de mantenimiento ordinario - Junio" style="height: 100px"></textarea>
                                    <label for="descripcion">Descripción del Cobro (Opcional)</label>
                                </div>

                                <button type="submit" id="btnSubmit" class="btn btn-agora-admin w-100 py-3 rounded-3 fw-medium fs-5 text-white shadow-sm">
                                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="btnSpinner"></span>
                                    <i class="bi bi-wallet2 me-2" id="btnIcon"></i> <span id="btnText">Efectuar Facturación Masiva</span>
                                </button>
                            </form>
                        </div>
                        
                        <!-- PESTAÑA: ÉXITO -->
                        <div class="tab-pane fade text-center py-4" id="success-pane" role="tabpanel" aria-labelledby="success-tab">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 text-dark fw-bold" id="successTitle">Facturación Exitosa</h3>
                            <p class="text-secondary" id="successMessage"></p>
                            
                            <div class="bg-light rounded-3 p-3 mt-4 text-start">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="bi bi-receipt me-2 text-primary"></i> <strong id="successEmitidos">0</strong> recibos generados</li>
                                    <li class="mb-2"><i class="bi bi-building me-2 text-primary"></i> <strong id="successTotal">0</strong> apartamentos evaluados</li>
                                    <li><i class="bi bi-info-circle me-2 text-primary"></i> <strong id="successExistentes">0</strong> recibos ya existían y fueron omitidos</li>
                                </ul>
                            </div>
                            
                            <button type="button" class="btn btn-outline-success mt-4 px-4 py-2 rounded-pill" onclick="resetForm()">
                                <i class="bi bi-arrow-repeat me-2"></i>Realizar otro cobro
                            </button>
                        </div>
                        
                        <!-- PESTAÑA: ERROR -->
                        <div class="tab-pane fade text-center py-4" id="error-pane" role="tabpanel" aria-labelledby="error-tab">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                            <h3 class="mt-3 text-dark fw-bold">Error en la Facturación</h3>
                            <p class="text-danger mt-3 fw-medium p-3 bg-danger bg-opacity-10 rounded-3" id="errorMessage"></p>
                            
                            <button type="button" class="btn btn-outline-danger mt-3 px-4 py-2 rounded-pill" onclick="resetForm()">
                                <i class="bi bi-arrow-left me-2"></i>Volver al formulario
                            </button>
                        </div>

                    </div>
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
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Estado de carga
        btnSpinner.classList.remove('d-none');
        btnIcon.classList.add('d-none');
        btnText.textContent = 'Procesando...';
        btnSubmit.disabled = true;
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Restaurar botón
            btnSpinner.classList.add('d-none');
            btnIcon.classList.remove('d-none');
            btnText.textContent = 'Efectuar Facturación Masiva';
            btnSubmit.disabled = false;
            
            if (data.status === 'success') {
                // Mostrar resultados
                document.getElementById('successMessage').textContent = data.message;
                if(data.data) {
                    document.getElementById('successEmitidos').textContent = data.data.recibos_emitidos || 0;
                    document.getElementById('successTotal').textContent = data.data.total_apartamentos || 0;
                    document.getElementById('successExistentes').textContent = data.data.recibos_ya_existian || 0;
                }
                
                // Cambiar a pestaña de éxito
                const tab = new bootstrap.Tab(document.getElementById('success-tab'));
                document.getElementById('success-tab').classList.remove('d-none');
                document.getElementById('error-tab').classList.add('d-none');
                document.getElementById('form-tab').classList.add('d-none');
                tab.show();
                
            } else {
                // Mostrar error
                document.getElementById('errorMessage').textContent = data.message;
                
                // Cambiar a pestaña de error
                const tab = new bootstrap.Tab(document.getElementById('error-tab'));
                document.getElementById('error-tab').classList.remove('d-none');
                document.getElementById('success-tab').classList.add('d-none');
                document.getElementById('form-tab').classList.add('d-none');
                tab.show();
            }
        })
        .catch(error => {
            // Restaurar botón
            btnSpinner.classList.add('d-none');
            btnIcon.classList.remove('d-none');
            btnText.textContent = 'Efectuar Facturación Masiva';
            btnSubmit.disabled = false;
            
            document.getElementById('errorMessage').textContent = 'Error de conexión con el servidor.';
            const tab = new bootstrap.Tab(document.getElementById('error-tab'));
            document.getElementById('error-tab').classList.remove('d-none');
            document.getElementById('success-tab').classList.add('d-none');
            document.getElementById('form-tab').classList.add('d-none');
            tab.show();
        });
    });
});

function resetForm() {
    const tab = new bootstrap.Tab(document.getElementById('form-tab'));
    document.getElementById('form-tab').classList.remove('d-none');
    document.getElementById('success-tab').classList.add('d-none');
    document.getElementById('error-tab').classList.add('d-none');
    document.getElementById('formFacturacion').reset();
    tab.show();
}
</script>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>
