<?php
$pagina_actual = 'residente_reservas';
echo view('template/residente_header', ['pagina_actual' => $pagina_actual, 'apartamentos' => $apartamentos ?? []]);
?>

<main class="container my-5" role="main">

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

    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= site_url('residente/dashboard') ?>" class="text-decoration-none text-secondary small">
                <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
            </a>
            <h1 class="fw-bold h3 text-dark mt-3 mb-1"><i class="bi bi-calendar-check me-2 text-success"></i>Reservar Área Común</h1>
            <p class="text-secondary small mb-0">Selecciona el área, la fecha y el horario disponible para tu reserva.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="<?= site_url('residente/reservas/crear') ?>" method="POST" id="formReserva">
                        <?= csrf_field() ?>

                        <div class="row g-3 mb-3">
                            <!-- Área común -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select" id="area_id" name="area_id" required>
                                        <option value="">Seleccione un área...</option>
                                        <?php foreach (($areas ?? []) as $area): ?>
                                            <option value="<?= $area['id'] ?>">
                                                <?= htmlspecialchars($area['nombre']) ?>
                                                <?php if (!empty($area['capacidad_maxima'])): ?>
                                                    (Cap. <?= $area['capacidad_maxima'] ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="area_id">Área Común *</label>
                                </div>
                            </div>

                            <!-- Apartamento (auto-selección del residente) -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select" id="apartamento_id" name="apartamento_id" required>
                                        <?php if (!empty($apartamentos)): ?>
                                            <?php foreach ($apartamentos as $apto): ?>
                                                <option value="<?= $apto['id'] ?>">
                                                    Apto. <?= htmlspecialchars($apto['nro_apartamento']) ?>
                                                    <?php if (!empty($apto['nombre_condominio'])): ?>
                                                        — <?= htmlspecialchars($apto['nombre_condominio']) ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No tienes apartamento asignado</option>
                                        <?php endif; ?>
                                    </select>
                                    <label for="apartamento_id">Tu Apartamento *</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Fecha -->
                            <div class="col-12 col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva"
                                           required min="<?= date('Y-m-d') ?>">
                                    <label for="fecha_reserva">Fecha *</label>
                                </div>
                            </div>

                            <!-- Hora inicio -->
                            <div class="col-6 col-md-4">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                    <label for="hora_inicio">Hora Inicio *</label>
                                </div>
                            </div>

                            <!-- Hora fin -->
                            <div class="col-6 col-md-4">
                                <div class="form-floating">
                                    <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                                    <label for="hora_fin">Hora Fin *</label>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de disponibilidad AJAX -->
                        <div id="panelDisponibilidad" class="d-none mb-3">
                            <div class="alert alert-info border-0 rounded-3 py-2 px-3 small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Reservas existentes para la fecha seleccionada:</strong>
                                <ul id="listaReservas" class="mb-0 mt-1"></ul>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success text-white w-100 py-3 rounded-3 fw-medium shadow-sm"
                                <?= empty($apartamentos) ? 'disabled' : '' ?>>
                            <i class="bi bi-calendar-check me-1"></i> Confirmar Reserva
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Script: Consulta de disponibilidad en tiempo real -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const areaSelect  = document.getElementById('area_id');
    const fechaInput  = document.getElementById('fecha_reserva');
    const panel       = document.getElementById('panelDisponibilidad');
    const lista       = document.getElementById('listaReservas');

    async function consultarDisponibilidad() {
        const areaId = areaSelect.value;
        const fecha  = fechaInput.value;

        if (!areaId || !fecha) {
            panel.classList.add('d-none');
            return;
        }

        try {
            const resp = await fetch(`<?= site_url('api/reservas/disponibilidad') ?>?area_id=${areaId}&fecha=${fecha}`);
            const data = await resp.json();

            if (data.status === 'success' && data.data.length > 0) {
                lista.innerHTML = '';
                data.data.forEach(r => {
                    const li = document.createElement('li');
                    li.textContent = `${r.hora_inicio.substring(0,5)} — ${r.hora_fin.substring(0,5)} (${r.estado})`;
                    lista.appendChild(li);
                });
                panel.classList.remove('d-none');
            } else {
                lista.innerHTML = '<li class="text-success">No hay reservas — horario completamente libre.</li>';
                panel.classList.remove('d-none');
            }
        } catch (e) {
            console.error('Error consultando disponibilidad:', e);
        }
    }

    if (areaSelect) areaSelect.addEventListener('change', consultarDisponibilidad);
    if (fechaInput) fechaInput.addEventListener('change', consultarDisponibilidad);
});
</script>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>

