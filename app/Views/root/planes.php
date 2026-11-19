<?php

/**
 * @file planes.php
 * @description Interfaz de gestión de suscripciones para el administrador del condominio.
 * @framework Mapeo de cabecera común inyectando la hoja de estilos 'admin_planes'.
 */

// Inclusión simulada/real del controlador de planes para obtener el plan activo actual
$plan_actual = $plan_actual ?? 'Bronce';

echo view('template/super_header', ['pagina_actual' => 'admin_planes']);
?>

<main class="p-5" role="main">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-5 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1">Mi Plan</h1>
            <p class="text-secondary small mb-0">Visualiza las capacidades de tu plan actual y gestiona escalabilidades o cambios de servicio.</p>
        </div>
        <div>
            <span class="badge text-bg-success px-3 py-2 rounded-pill shadow-sm small fw-semibold">
                <i class="bi bi-check-circle-fill me-1"></i> Expande tu Comunidad
            </span>
        </div>
    </div>

    <section class="row row-cols-1 row-cols-md-3 g-4 justify-content-center" aria-label="Planes disponibles para escalabilidad">

        <div class="col" data-plan-col="bronce">
            <div class="card h-100 text-center rounded-4 shadow-sm card-plan bg-white <?= ($plan_actual === 'Bronce') ? 'plan-activo border-success-custom' : 'border-light-custom'; ?>" data-plan-card="bronce">
                <div class="card-header py-4 bg-transparent border-0 position-relative">
                    <?php if ($plan_actual === 'Bronce'): ?>
                        <span class="badge-contratado position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm" style="transition: opacity 0.4s ease;">CONTRATADO</span>
                    <?php else: ?>
                        <span class="badge-contratado position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm d-none" style="transition: opacity 0.4s ease;">CONTRATADO</span>
                    <?php endif; ?>
                    <h4 class="my-0 fw-bold text-bronze">🥉 Plan Bronce</h4>
                    <p class="text-secondary small mt-1 mb-0">Para edificios pequeños</p>
                </div>
                <div class="card-body d-flex flex-column pt-0">
                    <h1 class="card-title pricing-card-title fw-bold mb-4 text-dark">$100<small class="text-secondary fw-light fs-6">pago unico</small></h1>
                    <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small text-secondary">
                        <li class="mb-2"><i class="bi bi-building me-2 text-agora-teal"></i>Hasta <strong>40</strong> Apartamentos</li>
                        <li class="mb-2"><i class="bi bi-graph-up me-2 text-agora-teal"></i>Control Financiero Básico</li>
                        <li class="mb-2"><i class="bi bi-megaphone me-2 text-agora-teal"></i>Cartelera Digital Oficial</li>
                        <li class="mb-2"><i class="bi bi-wrench me-2 text-agora-teal"></i>Buzón de Incidencias Básico</li>
                    </ul>
                    <?php if ($plan_actual === 'Bronce'): ?>
                        <button type="button"
                            class="btn-plan w-100 btn btn-success btn-lg mt-auto rounded-pill fw-semibold disabled"
                            data-plan-btn="bronce"
                            data-plan-label="Bronce"
                            data-original-class="btn-outline-agora-admin"
                            data-original-html='<i class="bi bi-arrow-left-right me-1"></i> Cambiar a Bronce'>
                            <i class="bi bi-check2-all me-1"></i> Tu Plan Actual
                        </button>
                    <?php else: ?>
                        <button type="button"
                            class="btn-plan w-100 btn btn-outline-agora-admin btn-lg mt-auto rounded-pill fw-semibold"
                            data-plan-btn="bronce"
                            data-plan-label="Bronce"
                            data-original-class="btn-outline-agora-admin"
                            data-original-html='<i class="bi bi-arrow-left-right me-1"></i> Cambiar a Bronce'
                            data-bs-toggle="modal" data-bs-target="#modalCambioPlan"
                            onclick="setPlanDestino('Bronce')">
                            <i class="bi bi-arrow-left-right me-1"></i> Cambiar a Bronce
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col" data-plan-col="plata">
            <div class="card h-100 text-center rounded-4 shadow card-plan bg-white position-relative <?= ($plan_actual === 'Plata') ? 'plan-activo border-success-custom shadow-md' : 'border-light-custom'; ?>" data-plan-card="plata">
                <div class="card-header py-4 bg-transparent border-0">
                    <?php if ($plan_actual === 'Plata'): ?>
                        <span class="badge-contratado position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm" style="transition: opacity 0.4s ease;">CONTRATADO</span>
                    <?php else: ?>
                        <span class="badge-contratado position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm d-none" style="transition: opacity 0.4s ease;">CONTRATADO</span>
                    <?php endif; ?>
                    <h4 class="my-0 fw-bold text-agora-teal-accent">🥈 Plan Plata</h4>
                    <p class="text-secondary small mt-1 mb-0">Para condominios medianos</p>
                </div>
                <div class="card-body d-flex flex-column pt-0">
                    <h1 class="card-title pricing-card-title fw-bold mb-4 text-dark">$150<small class="text-secondary fw-light fs-6">/pago unico</small></h1>
                    <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small text-secondary">
                        <li class="mb-2"><i class="bi bi-building-fill text-agora-teal me-2"></i>Hasta <strong>120</strong> Apartamentos</li>
                        <li class="mb-2"><i class="bi bi-graph-up-arrow text-agora-teal me-2"></i>Control Financiero Completo</li>
                        <li class="mb-2"><i class="bi bi-megaphone-fill text-agora-teal me-2"></i>Cartelera Digital Oficial</li>
                        <li class="mb-2"><i class="bi bi-wrench-adjustable text-agora-teal me-2"></i>Buzón de Incidencias Completo</li>
                    </ul>
                    <?php if ($plan_actual === 'Plata'): ?>
                        <button type="button"
                            class="btn-plan w-100 btn btn-success btn-lg mt-auto rounded-pill fw-semibold disabled"
                            data-plan-btn="plata"
                            data-plan-label="Plata"
                            data-original-class="btn-agora-admin text-white shadow-sm"
                            data-original-html='<i class="bi bi-arrow-left-right me-1"></i> Cambiar a Plata'>
                            <i class="bi bi-check2-all me-1"></i> Tu Plan Actual
                        </button>
                    <?php else: ?>
                        <button type="button"
                            class="btn-plan w-100 btn btn-agora-admin btn-lg mt-auto rounded-pill text-white shadow-sm fw-semibold"
                            data-plan-btn="plata"
                            data-plan-label="Plata"
                            data-original-class="btn-agora-admin text-white shadow-sm"
                            data-original-html='<i class="bi bi-arrow-left-right me-1"></i> Cambiar a Plata'
                            data-bs-toggle="modal" data-bs-target="#modalCambioPlan"
                            onclick="setPlanDestino('Plata')">
                            <i class="bi bi-arrow-left-right me-1"></i> Cambiar a Plata
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col" data-plan-col="oro">
            <div class="card h-100 text-center rounded-4 shadow-sm card-plan bg-white <?= ($plan_actual === 'Oro') ? 'plan-activo border-success-custom' : 'border-light-custom'; ?>" data-plan-card="oro">
                <div class="card-header py-4 bg-transparent border-0 position-relative">
                    <?php if ($plan_actual === 'Oro'): ?>
                        <span class="badge-contratado position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm" style="transition: opacity 0.4s ease;">CONTRATADO</span>
                    <?php else: ?>
                        <span class="badge-contratado position-absolute top-0 start-50 translate-middle badge rounded-pill bg-success px-3 py-1.5 fs-7 shadow-sm d-none" style="transition: opacity 0.4s ease;">CONTRATADO</span>
                    <?php endif; ?>
                    <h4 class="my-0 fw-bold text-gold">🥇 Plan Oro</h4>
                    <p class="text-secondary small mt-1 mb-0">Para grandes residencias</p>
                </div>
                <div class="card-body d-flex flex-column pt-0">
                    <h1 class="card-title pricing-card-title fw-bold mb-4 text-dark">$200<small class="text-secondary fw-light fs-6">/pago unico</small></h1>
                    <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small text-secondary">
                        <li class="mb-2"><i class="bi bi-buildings text-agora-teal me-2"></i>Apartamentos <strong>Ilimitados</strong></li>
                        <li class="mb-2"><i class="bi bi-patch-check text-agora-teal me-2"></i>Todo lo del Plan Plata</li>
                        <li class="mb-2"><i class="bi bi-speedometer text-agora-teal me-2"></i>Servidor de alta prioridad</li>
                        <li class="mb-2"><i class="bi bi-headset text-agora-teal me-2"></i>Soporte Técnico 24/7</li>
                    </ul>
                    <?php if ($plan_actual === 'Oro'): ?>
                        <button type="button"
                            class="btn-plan w-100 btn btn-success btn-lg mt-auto rounded-pill fw-semibold disabled"
                            data-plan-btn="oro"
                            data-plan-label="Oro"
                            data-original-class="btn-outline-agora-admin"
                            data-original-html='<i class="bi bi-rocket-takeoff me-1"></i> Solicitar Upgrade Oro'>
                            <i class="bi bi-check2-all me-1"></i> Tu Plan Actual
                        </button>
                    <?php else: ?>
                        <button type="button"
                            class="btn-plan w-100 btn btn-outline-agora-admin btn-lg mt-auto rounded-pill fw-semibold"
                            data-plan-btn="oro"
                            data-plan-label="Oro"
                            data-original-class="btn-outline-agora-admin"
                            data-original-html='<i class="bi bi-rocket-takeoff me-1"></i> Solicitar Upgrade Oro'
                            data-bs-toggle="modal" data-bs-target="#modalCambioPlan"
                            onclick="setPlanDestino('Oro')">
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
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom-0 pt-4 px-4 modal-header-custom text-white">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2" id="modalCambioPlanLabel">
                    <i class="bi bi-arrow-repeat"></i> Confirmar Solicitud de Cambio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formActivarLicencia" action="<?= base_url('crm/licencia/activar') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="plan_destino" id="input_plan_destino" value="">
                <div class="modal-body px-4 pt-4">
                    <p class="text-secondary small lh-base mb-3">Introduce la clave de activación correspondiente al plan seleccionado para actualizar la plataforma Ágora.</p>

                    <div class="bg-light p-3 rounded-4 border mb-3 text-center d-flex align-items-center justify-content-center gap-3">
                        <div>
                            <span class="text-secondary small d-block mb-1 fw-semibold">Plan Actual</span>
                            <span id="badge_plan_actual" class="badge px-3 py-2 fw-bold rounded-pill text-white shadow-sm" style="<?= ($plan_actual === 'Bronce') ? 'background-color: #CD7F32;' : (($plan_actual === 'Oro') ? 'background-color: #B45309;' : 'background-color: #475569;') ?>">
                                Plan <?= esc($plan_actual) ?>
                            </span>
                        </div>
                        <i class="bi bi-arrow-right text-muted fs-4"></i>
                        <div>
                            <span class="text-secondary small d-block mb-1 fw-semibold">Upgrade Solicitado</span>
                            <span id="label_plan_destino" class="badge px-3 py-2 fw-bold rounded-pill text-white shadow-sm" style="background-color: #0F172A;">
                                ---
                            </span>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="codigo_activacion" name="codigo_activacion" required placeholder="Clave de Activación">
                        <label for="codigo_activacion" class="text-secondary">Clave de Activación *</label>
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

        // ── Estado de sesión del plan activo en el cliente ─────────────────────
        // Se inicializa con el valor que renderizó PHP y se actualiza sin reload.
        let planActivoActual = '<?= strtolower($plan_actual) ?>';

        // Plan destino elegido desde los botones de tarjeta
        let planDestinoActual = '';

        // ────────────────────────────────────────────────────────────────────────
        // setPlanDestino: llamada desde los onclick de cada tarjeta
        // ────────────────────────────────────────────────────────────────────────
        window.setPlanDestino = function(planLabel) {
            planDestinoActual = planLabel;
            const labelEl = document.getElementById('label_plan_destino');
            if (labelEl) {
                labelEl.textContent = 'Plan ' + planLabel;
                const p = planLabel.toLowerCase();
                if (p === 'bronce') {
                    labelEl.style.backgroundColor = '#CD7F32';
                } else if (p === 'plata') {
                    labelEl.style.backgroundColor = '#475569';
                } else if (p === 'oro') {
                    labelEl.style.backgroundColor = '#B45309';
                } else {
                    labelEl.style.backgroundColor = '#0F172A';
                }
                labelEl.style.color = '#ffffff';
            }
            const inputDestino = document.getElementById('input_plan_destino');
            if (inputDestino) {
                inputDestino.value = planLabel;
            }
            // Limpiar el campo de clave al reabrir
            const inputCodigo = document.getElementById('codigo_activacion');
            if (inputCodigo) inputCodigo.value = '';
            const alertBox = document.getElementById('licencia-alert');
            if (alertBox) {
                alertBox.classList.add('d-none');
                alertBox.classList.remove('alert-danger', 'alert-success');
            }
        };

        // ────────────────────────────────────────────────────────────────────────
        // actualizarInterfazPlan(nuevoPlan)
        // nuevoPlan: string en minúsculas ('bronce' | 'plata' | 'oro')
        // ────────────────────────────────────────────────────────────────────────
        function actualizarInterfazPlan(nuevoPlan) {
            nuevoPlan = nuevoPlan.toLowerCase();

            const ACTIVE_CARD_CLASSES = ['plan-activo', 'border-success-custom'];
            const INACTIVE_CARD_CLASS = 'border-light-custom';
            const ACTIVE_BTN_CLASSES = ['btn-success', 'disabled'];
            const ACTIVE_BTN_HTML = '<i class="bi bi-check2-all me-1"></i> Tu Plan Actual';

            // ── 1. Desactivar la tarjeta que estaba activa ─────────────────────
            const anteriorCard = document.querySelector(`[data-plan-card="${planActivoActual}"]`);
            if (anteriorCard) {
                // Quitar clases de activo
                ACTIVE_CARD_CLASSES.forEach(c => anteriorCard.classList.remove(c));
                anteriorCard.classList.add(INACTIVE_CARD_CLASS);

                // Ocultar badge CONTRATADO con fade
                const anteriorBadge = anteriorCard.querySelector('.badge-contratado');
                if (anteriorBadge) {
                    anteriorBadge.style.opacity = '0';
                    setTimeout(() => anteriorBadge.classList.add('d-none'), 400);
                }

                // Restaurar el botón anterior a su estado original
                const anteriorBtn = anteriorCard.querySelector(`[data-plan-btn="${planActivoActual}"]`);
                if (anteriorBtn) {
                    const originalClass = anteriorBtn.dataset.originalClass || '';
                    const originalHtml = anteriorBtn.dataset.originalHtml || '';
                    // Quitar clases de activo, agregar las originales
                    ACTIVE_BTN_CLASSES.forEach(c => anteriorBtn.classList.remove(c));
                    originalClass.split(' ').filter(Boolean).forEach(c => anteriorBtn.classList.add(c));
                    anteriorBtn.innerHTML = originalHtml;
                    anteriorBtn.disabled = false;
                    // Restaurar comportamiento de abrir modal
                    anteriorBtn.setAttribute('data-bs-toggle', 'modal');
                    anteriorBtn.setAttribute('data-bs-target', '#modalCambioPlan');
                    anteriorBtn.setAttribute(
                        'onclick',
                        `setPlanDestino('${anteriorBtn.dataset.planLabel}')`
                    );
                }
            }

            // ── 2. Activar la nueva tarjeta ────────────────────────────────────
            const nuevaCard = document.querySelector(`[data-plan-card="${nuevoPlan}"]`);
            if (nuevaCard) {
                nuevaCard.classList.remove(INACTIVE_CARD_CLASS);
                ACTIVE_CARD_CLASSES.forEach(c => nuevaCard.classList.add(c));

                // Mostrar badge CONTRATADO con fade-in
                const nuevaBadge = nuevaCard.querySelector('.badge-contratado');
                if (nuevaBadge) {
                    nuevaBadge.style.opacity = '0';
                    nuevaBadge.classList.remove('d-none');
                    requestAnimationFrame(() => {
                        nuevaBadge.style.opacity = '1';
                    });
                }

                // Convertir el botón al estado "Tu Plan Actual"
                const nuevoBtn = nuevaCard.querySelector(`[data-plan-btn="${nuevoPlan}"]`);
                if (nuevoBtn) {
                    // Guardar clases originales antes de sobreescribir (por si cambia de nuevo)
                    const currentOriginalClass = nuevoBtn.dataset.originalClass || '';
                    currentOriginalClass.split(' ').filter(Boolean)
                        .forEach(c => nuevoBtn.classList.remove(c));
                    ACTIVE_BTN_CLASSES.forEach(c => nuevoBtn.classList.add(c));
                    nuevoBtn.innerHTML = ACTIVE_BTN_HTML;
                    nuevoBtn.disabled = true;
                    // Quitar comportamiento modal (ya es el plan activo)
                    nuevoBtn.removeAttribute('data-bs-toggle');
                    nuevoBtn.removeAttribute('data-bs-target');
                    nuevoBtn.removeAttribute('onclick');
                }
            }

            // Actualizar variable de sesión cliente y badge del modal
            planActivoActual = nuevoPlan;
            const badgeActual = document.getElementById('badge_plan_actual');
            if (badgeActual) {
                const nombreCapitalizado = nuevoPlan.charAt(0).toUpperCase() + nuevoPlan.slice(1);
                badgeActual.textContent = 'Plan ' + nombreCapitalizado;
                if (nuevoPlan === 'bronce') {
                    badgeActual.style.backgroundColor = '#CD7F32';
                } else if (nuevoPlan === 'plata') {
                    badgeActual.style.backgroundColor = '#475569';
                } else if (nuevoPlan === 'oro') {
                    badgeActual.style.backgroundColor = '#B45309';
                }
                badgeActual.style.color = '#ffffff';
            }
        }

        // ────────────────────────────────────────────────────────────────────────
        // Fetch + integración con actualizarInterfazPlan + SweetAlert2 Toast
        // ────────────────────────────────────────────────────────────────────────
        const form = document.getElementById('formActivarLicencia');
        if (!form) return;

        const modalEl = document.getElementById('modalCambioPlan');
        const bsModal = modalEl ? bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl) : null;
        const alertBox = document.getElementById('licencia-alert');
        const btnSubmit = document.getElementById('btnSubmitLicencia');

        form.addEventListener('submit', function() {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Validando...';
        });

    });
</script>