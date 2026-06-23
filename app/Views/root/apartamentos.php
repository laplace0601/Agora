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
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1 small fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i>Activo
                                </span>
                                <button type="button" class="btn btn-sm btn-danger text-white btn-delete-condo fw-bold shadow-sm" data-id="<?= $condo['id'] ?>" style="transition: all 0.3s ease;">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </button>
                            </div>
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

    <!-- Gestión de Apartamentos -->
    <div class="row mt-5 mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold h4 text-dark"><i class="bi bi-door-open-fill me-2 text-primary"></i>Gestión de Apartamentos</h2>
            <p class="text-secondary small">Listado de todos los apartamentos registrados y sus datos.</p>
        </div>
    </div>

    <div class="card card-agora-form border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabla-apartamentos">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-secondary">ID</th>
                            <th scope="col" class="text-secondary">Condominio</th>
                            <th scope="col" class="text-secondary">Número/Identificador</th>
                            <th scope="col" class="text-secondary">Residente</th>
                            <th scope="col" class="text-secondary">Metros Cuadrados</th>
                            <th scope="col" class="text-secondary">Alícuota</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-apartamentos">
                        <?php if (!empty($apartamentos)): ?>
                            <?php foreach ($apartamentos as $apto): ?>
                                <tr>
                                    <td class="fw-bold text-muted">#<?= $apto['id'] ?></td>
                                    <td class="fw-medium"><?= htmlspecialchars($apto['nombre_condominio']) ?></td>
                                    <td><span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($apto['nro_apartamento'] ?? '') ?></span></td>
                                    <td>
                                        <?php if (!empty($apto['nombre_residente'])): ?>
                                            <span class="text-dark fw-medium"><i class="bi bi-person-fill text-muted me-1"></i><?= htmlspecialchars($apto['nombre_residente']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small fs-7"><em>Sin asignar</em></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format((float)($apto['metros_cuadrados'] ?? 0), 2) ?> m²</td>
                                    <td><span class="badge bg-info rounded-pill"><?= number_format((float)($apto['alicuota'] ?? 0), 4) ?>%</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No hay apartamentos registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
                        <input type="text" class="form-control" id="condo-rif" name="rif_jurisdiccion" placeholder="RIF">
                        <label for="condo-rif">RIF de Jurisdicción (opcional)</label>
                    </div>


                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" min="0.01" class="form-control" id="condo-metros" name="total_metros_cuadrados" required placeholder="Ej. 1200.50">
                        <label for="condo-metros">Total de Metros Cuadrados del Condominio *</label>
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

                    <!-- 1. Condominio -->
                    <div class="form-floating mb-3">
                        <select class="form-select" id="apto-condo" name="condominio_id" required>
                            <option value="" data-metros="0">Selecciona un condominio...</option>
                            <?php foreach ($condominios as $c): ?>
                                <option value="<?= $c['id'] ?>" data-metros="<?= (float)($c['total_metros_cuadrados'] ?? 0) ?>">
                                    <?= htmlspecialchars($c['nombre_condominio']) ?>
                                    (<?= number_format((float)($c['total_metros_cuadrados'] ?? 0), 2) ?> m²)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="apto-condo">Condominio / Torre *</label>
                    </div>

                    <!-- 2. Número de apartamento -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="apto-num" name="numero_apto" required placeholder="Ej: 4-B">
                        <label for="apto-num">Identificador / Número de Apartamento *</label>
                    </div>

                    <!-- 3. Metros cuadrados del apartamento -->
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="apto-metros" name="metros_cuadrados_apto"
                               step="0.01" min="0.01" required placeholder="Ej: 85.50">
                        <label for="apto-metros">Metros Cuadrados del Apartamento *</label>
                    </div>

                    <!-- Preview de alícuota calculada -->
                    <div id="alicuota-preview" class="alert alert-info border-0 rounded-3 py-2 px-3 small d-none mt-2">
                        <i class="bi bi-calculator me-1"></i>
                        Alícuota estimada: <strong id="alicuota-valor">—</strong>%
                        <span class="text-muted ms-1">(m² apto / m² condominio × 100)</span>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ── Calculadora de alícuota en tiempo real ─────────────────────────
        const selectCondo  = document.getElementById('apto-condo');
        const inputMetros  = document.getElementById('apto-metros');
        const previewDiv   = document.getElementById('alicuota-preview');
        const valorSpan    = document.getElementById('alicuota-valor');

        function actualizarAlicuota() {
            const totalMetros = parseFloat(selectCondo.selectedOptions[0]?.getAttribute('data-metros') ?? 0);
            const aptoMetros  = parseFloat(inputMetros.value) || 0;

            if (totalMetros > 0 && aptoMetros > 0) {
                const alicuota = ((aptoMetros / totalMetros) * 100).toFixed(4);
                valorSpan.textContent = alicuota;
                previewDiv.classList.remove('d-none');
            } else {
                previewDiv.classList.add('d-none');
            }
        }

        if (selectCondo) selectCondo.addEventListener('change', actualizarAlicuota);
        if (inputMetros) inputMetros.addEventListener('input', actualizarAlicuota);
        // ──────────────────────────────────────────────────────────────────

        document.querySelectorAll('.btn-delete-condo').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                let step = 1;

                const colors = [
                    '', // 0: n/a
                    '#ffc107', // 1: Bootstrap Warning
                    '#fd7e14', // 2: Orange
                    '#e8590c', // 3: Darker orange
                    '#d9480f', // 4: Red-orange
                    '#c92a2a' // 5: Dark red
                ];

                Swal.fire({
                    title: '¿Estás completamente seguro?',
                    html: '<p class="text-danger fw-bold mb-0">Esta acción es irreversible. Se eliminará el condominio y no podrá recuperarse.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: colors[1],
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Confirma el paso 1 de 5`,
                    cancelButtonText: 'Cancelar',
                    allowOutsideClick: false,
                    preConfirm: async () => {
                        if (step < 5) {
                            step++;
                            const confirmBtn = Swal.getConfirmButton();

                            // Actualizar texto y color visualmente
                            confirmBtn.textContent = `Confirma el paso ${step} de 5`;
                            confirmBtn.style.backgroundColor = colors[step];
                            confirmBtn.style.borderColor = colors[step];

                            // Retornar false previene que el modal se cierre
                            return false;
                        } else {
                            // Paso 5: Ejecutar la petición
                            Swal.showLoading();
                            const formData = new FormData();
                            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                            try {
                                const response = await fetch(`<?= site_url('super/apartamentos/condominios/delete/') ?>${id}`, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                                const result = await response.json();

                                if (response.ok && result.status === 'success') {
                                    return true; // Cierra el modal y pasa al .then
                                } else {
                                    Swal.showValidationMessage(result.error || "Error de seguridad al eliminar el condominio.");
                                    return false;
                                }
                            } catch (e) {
                                Swal.showValidationMessage("Error de red al intentar conectar con el servidor.");
                                return false;
                            }
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar Toast de éxito
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Condominio eliminado correctamente'
                        });

                        // Eliminar la tarjeta del DOM con una transición suave
                        const card = btn.closest('.col-12.col-md-6.col-lg-4');
                        if (card) {
                            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => card.remove(), 500);
                        } else {
                            window.location.reload();
                        }
                    }
                });
            });
        });
    });
</script>

<?php echo view('template/super_footer', ['pagina_actual' => $pagina_actual ?? 'super_apartamentos']); ?>