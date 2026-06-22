<?php 
    /**
     * @file planes.php
     * @description Interfaz de gestión de suscripciones para el administrador del condominio.
     * @framework Mapeo de cabecera común inyectando la hoja de estilos 'admin_planes'.
     */
    
    // Inclusión simulada/real del controlador de planes para obtener el plan activo actual
    // Nota: Por defecto simularemos que el condominio tiene contratado el "Plan Bronce"
    $plan_actual = 'Bronce'; 
    
    echo view('template/super_header', ['pagina_actual' => 'admin_planes']); 
?>

<main class="p-5" role="main">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-5 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1">Mi Plan y Suscripción</h1>
            <p class="text-secondary small mb-0">Visualiza las capacidades de tu plan actual y gestiona escalabilidades o cambios de servicio.</p>
        </div>
        <div>
            <span class="badge text-bg-success px-3 py-2 rounded-pill shadow-sm small fw-semibold">
                <i class="bi bi-check-circle-fill me-1"></i> Suscripción Al Día
            </span>
        </div>
    </div>

    <section class="row row-cols-1 row-cols-md-3 g-4 justify-content-center" aria-label="Planes disponibles para escalabilidad">
        
        <div class="col">
            <div class="card h-100 text-center rounded-4 shadow-sm card-plan bg-white <?= ($plan_actual === 'Bronce') ? 'plan-activo border-success-custom' : 'border-light-custom'; ?>">
                <div class="card-header py-4 bg-transparent border-0 position-relative">
                    <?php if ($plan_actual === 'Bronce'): ?>
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm">CONTRATADO</span>
                    <?php endif; ?>
                    <h4 class="my-0 fw-bold text-bronze">🥉 Plan Bronce</h4>
                    <p class="text-secondary small mt-1 mb-0">Para edificios pequeños</p>
                </div>
                <div class="card-body d-flex flex-column pt-0">
                    <h1 class="card-title pricing-card-title fw-bold mb-4 text-dark">$15<small class="text-secondary fw-light fs-6">/mes</small></h1>
                    <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small text-secondary">
                        <li class="mb-2"><i class="bi bi-building me-2 text-agora-teal"></i>Hasta <strong>40</strong> Apartamentos</li>
                        <li class="mb-2"><i class="bi bi-graph-up me-2 text-agora-teal"></i>Control Financiero Básico</li>
                        <li class="mb-2"><i class="bi bi-megaphone me-2 text-agora-teal"></i>Cartelera Digital Oficial</li>
                        <li class="mb-2"><i class="bi bi-wrench me-2 text-agora-teal"></i>Buzón de Incidencias Básico</li> 
                    </ul>
                    
                    <?php if ($plan_actual === 'Bronce'): ?>
                        <button type="button" class="w-100 btn btn-success btn-lg mt-auto rounded-pill disabled fw-semibold py-2.5">
                            <i class="bi bi-check2-all me-1"></i> Tu Plan Actual
                        </button>
                    <?php else: ?>
                        <button type="button" class="w-100 btn btn-outline-agora-admin btn-lg mt-auto rounded-pill fw-semibold py-2.5" data-bs-toggle="modal" data-bs-target="#modalCambioPlan" onclick="setPlanDestino('Bronce')">
                            <i class="bi bi-arrow-left-right me-1"></i> Cambiar a Bronce
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
            
        <div class="col">
            <div class="card h-100 text-center rounded-4 shadow card-plan bg-white position-relative <?= ($plan_actual === 'Plata') ? 'plan-activo border-success-custom shadow-md' : 'border-light-custom'; ?>">
                <div class="card-header py-4 bg-transparent border-0">
                    <?php if ($plan_actual === 'Plata'): ?>
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm">CONTRATADO</span>
                    <?php else: ?>
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill text-bg-agora px-3 py-1.5 fs-7 shadow-sm">RECOMENDADO</span>
                    <?php endif; ?>
                    <h4 class="my-0 fw-bold text-agora-teal-accent">🥈 Plan Plata</h4>
                    <p class="text-secondary small mt-1 mb-0">Para condominios medianos</p>
                </div>
                <div class="card-body d-flex flex-column pt-0">
                    <h1 class="card-title pricing-card-title fw-bold mb-4 text-dark">$30<small class="text-secondary fw-light fs-6">/mes</small></h1>
                    <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small text-secondary">
                        <li class="mb-2"><i class="bi bi-building-fill text-agora-teal me-2"></i>Hasta <strong>120</strong> Apartamentos</li>
                        <li class="mb-2"><i class="bi bi-graph-up-arrow text-agora-teal me-2"></i>Control Financiero Completo</li>
                        <li class="mb-2"><i class="bi bi-megaphone-fill text-agora-teal me-2"></i>Cartelera Digital Oficial</li>
                        <li class="mb-2"><i class="bi bi-wrench-adjustable text-agora-teal me-2"></i>Buzón de Incidencias Completo</li>
                    </ul>
                    
                    <?php if ($plan_actual === 'Plata'): ?>
                        <button type="button" class="w-100 btn btn-success btn-lg mt-auto rounded-pill disabled fw-semibold py-2.5">
                            <i class="bi bi-check2-all me-1"></i> Tu Plan Actual
                        </button>
                    <?php else: ?>
                        <button type="button" class="w-100 btn btn-agora-admin btn-lg mt-auto rounded-pill text-white shadow-sm fw-semibold py-2.5" data-bs-toggle="modal" data-bs-target="#modalCambioPlan" onclick="setPlanDestino('Plata')">
                            <i class="bi bi-arrow-left-right me-1"></i> Cambiar a Plata
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 text-center rounded-4 shadow-sm card-plan bg-white <?= ($plan_actual === 'Oro') ? 'plan-activo border-success-custom' : 'border-light-custom'; ?>">
                <div class="card-header py-4 bg-transparent border-0 position-relative">
                    <?php if ($plan_actual === 'Oro'): ?>
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm">CONTRATADO</span>
                    <?php endif; ?>
                    <h4 class="my-0 fw-bold text-gold">🥇 Plan Oro</h4>
                    <p class="text-secondary small mt-1 mb-0">Para grandes residencias</p>
                </div>
                <div class="card-body d-flex flex-column pt-0">
                    <h1 class="card-title pricing-card-title fw-bold mb-4 text-dark">$50<small class="text-secondary fw-light fs-6">/mes</small></h1>
                    <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small text-secondary">
                        <li class="mb-2"><i class="bi bi-buildings text-agora-teal me-2"></i>Apartamentos <strong>Ilimitados</strong></li>
                        <li class="mb-2"><i class="bi bi-patch-check text-agora-teal me-2"></i>Todo lo del Plan Plata</li>
                        <li class="mb-2"><i class="bi bi-speedometer text-agora-teal me-2"></i>Servidor de alta prioridad</li>
                        <li class="mb-2"><i class="bi bi-headset text-agora-teal me-2"></i>Soporte Técnico 24/7</li>
                    </ul>
                    
                    <?php if ($plan_actual === 'Oro'): ?>
                        <button type="button" class="w-100 btn btn-success btn-lg mt-auto rounded-pill disabled fw-semibold py-2.5">
                            <i class="bi bi-check2-all me-1"></i> Tu Plan Actual
                        </button>
                    <?php else: ?>
                        <button type="button" class="w-100 btn btn-outline-agora-admin btn-lg mt-auto rounded-pill fw-semibold py-2.5" data-bs-toggle="modal" data-bs-target="#modalCambioPlan" onclick="setPlanDestino('Oro')">
                            <i class="bi bi-rocket-takeoff me-1"></i> Solicitar Upgrade Oro
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </section>
</main>

<div class="modal fade" id="modalCambioPlan" tabindex="-1" aria-labelledby="modalCambioPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pt-4 px-4 modal-header-custom text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="modalCambioPlanLabel">🔄 Confirmar Solicitud de Cambio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formActivarLicencia" action="<?= site_url('crm/licencia/activar') ?>" method="POST">
                <div class="modal-body px-4 pt-4">
                    <p class="text-dark small lh-base">Introduce la clave de activación para hacer Upgrade al nivel de licencia de la plataforma Ágora.</p>
                    
                    <div class="bg-light p-3 rounded-3 border mb-3 text-center d-flex align-items-center justify-content-center gap-3">
                        <div>
                            <span class="text-secondary small d-block">Plan Actual</span>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 fw-bold rounded-pill"><?= $plan_actual ?></span>
                        </div>
                        <i class="bi bi-arrow-right text-muted fs-4"></i>
                        <div>
                            <span class="text-secondary small d-block">Upgrade Solicitado</span>
                            <span id="label_plan_destino" class="badge text-bg-agora px-3 py-1.5 fw-bold rounded-pill">---</span>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="codigo_activacion" name="codigo_activacion" required placeholder="Clave de Activación">
                        <label for="codigo_activacion">Clave de Activación *</label>
                    </div>

                    <div id="licencia-alert" class="alert d-none small mb-0"></div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-3 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSubmitLicencia" class="btn btn-agora-admin text-white rounded-pill px-4 py-2 small shadow-sm">
                        <i class="bi bi-key me-1"></i> Activar Licencia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
    echo view('template/super_footer', ['pagina_actual' => 'admin_planes']); 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formActivarLicencia');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const alertBox = document.getElementById('licencia-alert');
            const btnSubmit = document.getElementById('btnSubmitLicencia');
            
            // Reiniciar estado
            alertBox.classList.add('d-none');
            alertBox.classList.remove('alert-danger', 'alert-success');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Validando...';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.error || 'Error al validar la licencia');
                }
                return data;
            })
            .then(data => {
                alertBox.classList.remove('d-none');
                alertBox.classList.add('alert-success');
                alertBox.innerHTML = '<i class="bi bi-check-circle me-1"></i> ' + data.message;
                setTimeout(() => window.location.reload(), 2000);
            })
            .catch(error => {
                alertBox.classList.remove('d-none');
                alertBox.classList.add('alert-danger');
                alertBox.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> ' + error.message;
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="bi bi-key me-1"></i> Activar Licencia';
            });
        });
    }
});
</script>