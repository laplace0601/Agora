<?php 
    $pagina_actual = 'admin_directorio';
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="p-5" role="main">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1"><i class="bi bi-building me-2"></i>Directorio de Departamentos</h1>
            <p class="text-secondary small mb-0">Haz clic en un departamento para ver su censo de habitantes y estado de cuenta.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-warning rounded-pill px-4 py-2 shadow-sm position-relative" id="btnPendientes" data-bs-toggle="modal" data-bs-target="#modalPendientes">
                <i class="bi bi-hourglass-split me-1"></i> Solicitudes Pendientes
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="badgePendientes">0</span>
            </button>
            <div class="input-group input-group-sm" style="max-width: 280px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="buscadorAptos" class="form-control border-start-0" placeholder="Buscar apto, residente...">
            </div>
        </div>
    </header>

    <!-- Cuadrícula de departamentos -->
    <div class="row g-3" id="gridApartamentos">
        <?php 
        $apartamentos = $apartamentos ?? [];
        if (!empty($apartamentos)):
            foreach ($apartamentos as $apto):
                $tieneDeuda = ($apto['deuda_total'] ?? 0) > 0;
                $borderColor = $tieneDeuda ? 'border-danger' : 'border-success';
                $ocupado = !empty($apto['nombre_completo']);
        ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2 fila-apto" data-search="<?= strtolower(htmlspecialchars($apto['nro_apartamento'] . ' ' . ($apto['nombre_completo'] ?? '') . ' ' . ($apto['nombre_condominio'] ?? ''))) ?>">
                <div class="card h-100 border-0 border-start border-3 <?= $borderColor ?> shadow-sm rounded-3 cursor-pointer card-departamento"
                     data-apto-id="<?= $apto['id'] ?>"
                     style="cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;"
                     onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)';"
                     onmouseleave="this.style.transform=''; this.style.boxShadow='';">
                    <div class="card-body p-3 text-center">
                        <!-- Número del apartamento -->
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px; background: <?= $tieneDeuda ? 'rgba(220,53,69,0.1)' : 'rgba(25,135,84,0.1)' ?>;">
                            <i class="bi bi-door-open-fill fs-5 <?= $tieneDeuda ? 'text-danger' : 'text-success' ?>"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($apto['nro_apartamento']) ?></h6>
                        <small class="text-muted d-block" style="font-size: 0.7rem;"><?= htmlspecialchars($apto['nombre_condominio'] ?? '') ?></small>
                        
                        <!-- Residente -->
                        <div class="mt-2">
                            <?php if ($ocupado): ?>
                                <span class="text-dark small fw-medium text-truncate d-block" style="max-width: 100%;"><?= htmlspecialchars($apto['nombre_completo']) ?></span>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Vacío</span>
                            <?php endif; ?>
                        </div>

                        <!-- Indicador de deuda -->
                        <?php if ($tieneDeuda): ?>
                            <span class="badge bg-danger-subtle text-danger rounded-pill mt-1 px-2 py-1" style="font-size: 0.65rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>$<?= number_format($apto['deuda_total'], 2) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success rounded-pill mt-1 px-2 py-1" style="font-size: 0.65rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>Al día
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
            endforeach;
        else:
        ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-building text-muted fs-1 d-block mb-2"></i>
                <p class="text-secondary fs-5">No hay departamentos registrados.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- ============================================================= -->
<!-- Modal: Detalle del Apartamento con Censo y Estado de Cuenta   -->
<!-- ============================================================= -->
<div class="modal fade" id="modalDetalleApto" tabindex="-1" aria-labelledby="modalDetalleAptoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalDetalleAptoLabel">
                    <i class="bi bi-door-open me-2"></i>Detalle del Departamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4" id="modalDetalleBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Cargando información...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- Modal: Solicitudes Pendientes de Aprobación                   -->
<!-- ============================================================= -->
<div class="modal fade" id="modalPendientes" tabindex="-1" aria-labelledby="modalPendientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalPendientesLabel">
                    <i class="bi bi-hourglass-split me-2 text-warning"></i>Solicitudes de Habitantes Pendientes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4" id="modalPendientesBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="text-muted mt-2">Cargando solicitudes...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================= -->
<!-- Modal: Agregar Habitante (Admin)                              -->
<!-- ============================================================= -->
<div class="modal fade" id="modalAgregarHabitante" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalAgregarLabel">
                    <i class="bi bi-person-plus me-2"></i>Agregar Habitante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formAgregarHabitante">
                <input type="hidden" id="agregar_apto_id" name="apartamento_id">
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="agregar_nombre" name="nombre" required placeholder="Nombre">
                                <label for="agregar_nombre">Nombre *</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="agregar_apellido" name="apellido" required placeholder="Apellido">
                                <label for="agregar_apellido">Apellido *</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="agregar_cedula" name="cedula_identidad" placeholder="Cédula">
                                <label for="agregar_cedula">Cédula de Identidad</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="agregar_telefono" name="telefono" placeholder="Teléfono">
                                <label for="agregar_telefono">Teléfono</label>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="form-floating">
                                <select class="form-select" id="agregar_tipo" name="tipo_habitante" required>
                                    <option value="Propietario">Propietario</option>
                                    <option value="Familiar" selected>Familiar</option>
                                    <option value="Inquilino">Inquilino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <label for="agregar_tipo">Tipo de Habitante *</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check mt-3 ms-2">
                                <input class="form-check-input" type="checkbox" id="agregar_principal" name="es_principal" value="1">
                                <label class="form-check-label small" for="agregar_principal">
                                    <i class="bi bi-star-fill text-warning me-1"></i>Principal
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnGuardarHabitante">
                        <i class="bi bi-check-lg me-1"></i> Guardar Habitante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleApto'));
    const modalBody = document.getElementById('modalDetalleBody');
    const modalTitle = document.getElementById('modalDetalleAptoLabel');
    const buscador = document.getElementById('buscadorAptos');
    const badgePendientes = document.getElementById('badgePendientes');

    // Variable para recordar el apartamento actual
    let currentAptoId = null;

    // ---------------------------------------------------------------
    // Buscador
    // ---------------------------------------------------------------
    if (buscador) {
        buscador.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.fila-apto').forEach(card => {
                const text = card.getAttribute('data-search');
                card.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // ---------------------------------------------------------------
    // Cargar conteo de pendientes al inicio
    // ---------------------------------------------------------------
    async function cargarConteoPendientes() {
        try {
            const resp = await fetch('<?= site_url('admin/habitantes/pendientes') ?>');
            const result = await resp.json();
            if (result.status === 'success' && result.total > 0) {
                badgePendientes.textContent = result.total;
                badgePendientes.classList.remove('d-none');
            } else {
                badgePendientes.classList.add('d-none');
            }
        } catch(err) { console.error(err); }
    }
    cargarConteoPendientes();

    // ---------------------------------------------------------------
    // Click en tarjeta -> abrir modal con detalle
    // ---------------------------------------------------------------
    document.querySelectorAll('.card-departamento').forEach(card => {
        card.addEventListener('click', async function() {
            const aptoId = this.getAttribute('data-apto-id');
            currentAptoId = aptoId;
            modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2">Cargando información...</p></div>';
            modal.show();
            await cargarDetalleApto(aptoId);
        });
    });

    async function cargarDetalleApto(aptoId) {
        try {
            const resp = await fetch(`<?= site_url('admin/apartamento/') ?>${aptoId}/detalle`);
            const result = await resp.json();

            if (result.status === 'success') {
                const d = result.data;
                const a = d.apartamento;
                
                modalTitle.innerHTML = `<i class="bi bi-door-open me-2"></i>Apto. ${a.nro_apartamento} — ${a.nombre_condominio || ''}`;

                let html = '';

                // Info del apartamento
                html += `<div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Nro</small>
                            <strong class="text-dark">${a.nro_apartamento}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Torre</small>
                            <strong class="text-dark">${a.nombre_edificio_torre || '—'}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Propietario</small>
                            <strong class="text-dark">${a.nombre_completo || 'Sin asignar'}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Deuda</small>
                            <strong class="${d.deuda_total > 0 ? 'text-danger' : 'text-success'}">$${parseFloat(d.deuda_total).toFixed(2)}</strong>
                        </div>
                    </div>
                </div>`;

                // Censo de habitantes con botón de agregar
                html += `<div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Censo de Habitantes (${d.habitantes.length})</h6>
                    <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="abrirModalAgregar(${aptoId})">
                        <i class="bi bi-person-plus me-1"></i> Agregar
                    </button>
                </div>`;

                if (d.habitantes.length > 0) {
                    html += `<div class="table-responsive mb-4"><table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr>
                            <th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Tipo</th><th class="text-center">Principal</th><th class="text-center">Estado</th><th class="text-center">Acciones</th>
                        </tr></thead><tbody>`;
                    d.habitantes.forEach(h => {
                        let estadoBadge = '';
                        let acciones = '';
                        if (h.estado === 'aprobado') {
                            estadoBadge = '<span class="badge bg-success-subtle text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Aprobado</span>';
                            acciones = `<button class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" onclick="eliminarHabitante(${h.id})" title="Eliminar"><i class="bi bi-trash"></i></button>`;
                        } else if (h.estado === 'pendiente') {
                            estadoBadge = '<span class="badge bg-warning-subtle text-warning rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Pendiente</span>';
                            acciones = `<div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-success rounded-start-pill px-2 py-0" onclick="aprobarHabitante(${h.id})" title="Aprobar"><i class="bi bi-check-lg"></i></button>
                                <button class="btn btn-outline-danger rounded-end-pill px-2 py-0" onclick="rechazarHabitante(${h.id})" title="Rechazar"><i class="bi bi-x-lg"></i></button>
                            </div>`;
                        } else {
                            estadoBadge = '<span class="badge bg-danger-subtle text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Rechazado</span>';
                            acciones = `<button class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" onclick="eliminarHabitante(${h.id})" title="Eliminar"><i class="bi bi-trash"></i></button>`;
                        }
                        html += `<tr class="${h.estado === 'rechazado' ? 'table-light text-muted' : ''}">
                            <td class="fw-medium">${h.nombre} ${h.apellido}</td>
                            <td class="small">${h.cedula_identidad || '—'}</td>
                            <td class="small">${h.telefono || '—'}</td>
                            <td><span class="badge bg-info-subtle text-info rounded-pill">${h.tipo_habitante}</span></td>
                            <td class="text-center">${h.es_principal == 1 ? '<i class="bi bi-star-fill text-warning"></i>' : ''}</td>
                            <td class="text-center">${estadoBadge}</td>
                            <td class="text-center">${acciones}</td>
                        </tr>`;
                    });
                    html += `</tbody></table></div>`;
                } else {
                    html += `<div class="alert alert-light border text-muted small mb-4"><i class="bi bi-info-circle me-1"></i>No hay habitantes registrados en el censo.</div>`;
                }

                // Estado de cuenta
                html += `<h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="bi bi-receipt me-2 text-danger"></i>Recibos Pendientes (${d.recibos_pendientes.length})</h6>`;
                if (d.recibos_pendientes.length > 0) {
                    const meses = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                    html += `<div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Período</th><th class="text-end">Monto</th><th class="text-center">Estado</th></tr></thead><tbody>`;
                    d.recibos_pendientes.forEach(r => {
                        html += `<tr>
                            <td>${meses[r.mes] || r.mes} ${r.anio}</td>
                            <td class="text-end fw-bold text-danger">$${parseFloat(r.monto_total).toFixed(2)}</td>
                            <td class="text-center"><span class="badge bg-danger-subtle text-danger rounded-pill">Pendiente</span></td>
                        </tr>`;
                    });
                    html += `</tbody></table></div>`;
                } else {
                    html += `<div class="alert alert-success border-0 small"><i class="bi bi-check-circle-fill me-1"></i>Sin deudas pendientes. ¡Al día!</div>`;
                }

                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Error al cargar la información.</div>';
            }
        } catch (err) {
            modalBody.innerHTML = '<div class="alert alert-danger">Error de conexión al cargar el detalle.</div>';
            console.error(err);
        }
    }

    // ---------------------------------------------------------------
    // Cargar solicitudes pendientes en el modal
    // ---------------------------------------------------------------
    const modalPendientes = document.getElementById('modalPendientes');
    modalPendientes.addEventListener('show.bs.modal', async function() {
        const body = document.getElementById('modalPendientesBody');
        body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-warning" role="status"></div><p class="text-muted mt-2">Cargando solicitudes...</p></div>';

        try {
            const resp = await fetch('<?= site_url('admin/habitantes/pendientes') ?>');
            const result = await resp.json();

            if (result.status === 'success') {
                if (result.data.length === 0) {
                    body.innerHTML = `<div class="text-center py-5">
                        <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                        <p class="text-secondary">No hay solicitudes pendientes de aprobación.</p>
                    </div>`;
                    return;
                }

                let html = `<div class="table-responsive"><table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr>
                        <th>Apartamento</th><th>Nombre</th><th>Cédula</th><th>Tipo</th><th>Fecha</th><th class="text-center">Acciones</th>
                    </tr></thead><tbody>`;

                result.data.forEach(h => {
                    const fecha = h.created_at ? new Date(h.created_at).toLocaleDateString('es-VE') : '—';
                    html += `<tr>
                        <td><span class="badge bg-primary-subtle text-primary rounded-pill px-2">${h.nro_apartamento}</span> <small class="text-muted">${h.nombre_condominio || ''}</small></td>
                        <td class="fw-medium">${h.nombre} ${h.apellido}</td>
                        <td class="small">${h.cedula_identidad || '—'}</td>
                        <td><span class="badge bg-info-subtle text-info rounded-pill">${h.tipo_habitante}</span></td>
                        <td class="small text-muted">${fecha}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success rounded-start-pill px-3" onclick="aprobarHabitante(${h.id})">
                                    <i class="bi bi-check-lg me-1"></i>Aprobar
                                </button>
                                <button class="btn btn-danger rounded-end-pill px-3" onclick="rechazarHabitante(${h.id})">
                                    <i class="bi bi-x-lg me-1"></i>Rechazar
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });

                html += '</tbody></table></div>';
                body.innerHTML = html;
            } else {
                body.innerHTML = '<div class="alert alert-danger">Error al cargar las solicitudes.</div>';
            }
        } catch(err) {
            body.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>';
            console.error(err);
        }
    });

    // ---------------------------------------------------------------
    // Agregar habitante (admin) — form submit
    // ---------------------------------------------------------------
    const formAgregar = document.getElementById('formAgregarHabitante');
    formAgregar.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarHabitante');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

        try {
            const formData = new FormData(formAgregar);
            const resp = await fetch('<?= site_url('admin/habitantes/guardar') ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await resp.json();

            if (result.status === 'success') {
                bootstrap.Modal.getInstance(document.getElementById('modalAgregarHabitante')).hide();
                formAgregar.reset();
                // Recargar detalle del apartamento actual
                if (currentAptoId) {
                    await cargarDetalleApto(currentAptoId);
                }
            } else {
                alert(result.message || 'Error al guardar el habitante.');
            }
        } catch(err) {
            alert('Error de conexión.');
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Guardar Habitante';
        }
    });

    // ---------------------------------------------------------------
    // Funciones globales: aprobar, rechazar, eliminar, agregar
    // ---------------------------------------------------------------
    window.abrirModalAgregar = function(aptoId) {
        document.getElementById('agregar_apto_id').value = aptoId;
        const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregarHabitante'));
        modalAgregar.show();
    };

    window.aprobarHabitante = async function(id) {
        if (!confirm('¿Aprobar esta solicitud de habitante?')) return;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const resp = await fetch('<?= site_url('admin/habitantes/aprobar') ?>', { method: 'POST', body: formData });
            const result = await resp.json();
            if (result.status === 'success') {
                // Recargar la vista actual
                if (currentAptoId) await cargarDetalleApto(currentAptoId);
                cargarConteoPendientes();
                // Si el modal de pendientes está abierto, re-trigger su carga
                const pendModal = document.getElementById('modalPendientes');
                if (pendModal.classList.contains('show')) {
                    pendModal.dispatchEvent(new Event('show.bs.modal'));
                }
            } else {
                alert(result.message || 'Error al aprobar.');
            }
        } catch(err) { alert('Error de conexión.'); console.error(err); }
    };

    window.rechazarHabitante = async function(id) {
        if (!confirm('¿Rechazar esta solicitud de habitante?')) return;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const resp = await fetch('<?= site_url('admin/habitantes/rechazar') ?>', { method: 'POST', body: formData });
            const result = await resp.json();
            if (result.status === 'success') {
                if (currentAptoId) await cargarDetalleApto(currentAptoId);
                cargarConteoPendientes();
                const pendModal = document.getElementById('modalPendientes');
                if (pendModal.classList.contains('show')) {
                    pendModal.dispatchEvent(new Event('show.bs.modal'));
                }
            } else {
                alert(result.message || 'Error al rechazar.');
            }
        } catch(err) { alert('Error de conexión.'); console.error(err); }
    };

    window.eliminarHabitante = async function(id) {
        if (!confirm('¿Eliminar este habitante del censo?')) return;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const resp = await fetch('<?= site_url('admin/habitantes/eliminar') ?>', { method: 'POST', body: formData });
            const result = await resp.json();
            if (result.status === 'success') {
                if (currentAptoId) await cargarDetalleApto(currentAptoId);
            } else {
                alert(result.message || 'Error al eliminar.');
            }
        } catch(err) { alert('Error de conexión.'); console.error(err); }
    };
});
</script>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>
