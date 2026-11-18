<?php
$pagina_actual = 'residente_habitantes';
echo view('template/residente_header', ['pagina_actual' => $pagina_actual, 'apartamentos' => $apartamentos ?? []]);
?>

<main class="container my-5" role="main">

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

    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4 border-bottom pb-4">
        <div>
            <h1 class="fw-bold h3 text-dark mb-1"><i class="bi bi-people-fill me-2"></i>Censo de Mi Departamento</h1>
            <p class="text-secondary small mb-0">
                Consulta los habitantes registrados en tu apartamento y solicita agregar nuevos.
                <span class="text-muted fst-italic">Las solicitudes deben ser aprobadas por la administración.</span>
            </p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSolicitarHabitante">
            <i class="bi bi-person-plus me-1"></i> Solicitar Nuevo Habitante
        </button>
    </header>

    <!-- Información del apartamento -->
    <?php if (!empty($apartamentos)): ?>
        <div class="bg-light rounded-3 p-3 mb-4 d-inline-flex align-items-center gap-3">
            <i class="bi bi-door-open-fill fs-4 text-primary"></i>
            <div>
                <small class="text-muted d-block">Tu Apartamento</small>
                <strong class="text-dark"><?= htmlspecialchars($apartamentos[0]['nro_apartamento'] ?? 'N/A') ?></strong>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabla de habitantes (Renderizado PHP en Servidor) -->
    <div id="contenedorHabitantes">
        <?php
        $habitantes_lista = $habitantes ?? [];
        if (empty($habitantes_lista)):
        ?>
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                <i class="bi bi-people text-muted fs-1 d-block mb-2"></i>
                <p class="text-secondary mb-0">No hay habitantes registrados aún.</p>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nombre Completo</th>
                                <th>Cédula</th>
                                <th>Teléfono</th>
                                <th>Tipo</th>
                                <th class="text-center">Principal</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($habitantes_lista as $h): ?>
                                <?php
                                if ($h['estado'] === 'aprobado') {
                                    $estadoBadge = '<span class="badge bg-success-subtle text-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Aprobado</span>';
                                } elseif ($h['estado'] === 'pendiente') {
                                    $estadoBadge = '<span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Pendiente</span>';
                                } else {
                                    $estadoBadge = '<span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Rechazado</span>';
                                }
                                ?>
                                <tr class="<?= $h['estado'] === 'rechazado' ? 'table-light text-muted' : '' ?>">
                                    <td class="ps-4 fw-medium"><?= esc($h['nombre']) ?> <?= esc($h['apellido']) ?></td>
                                    <td class="small"><?= esc($h['cedula_identidad'] ?? '—') ?></td>
                                    <td class="small"><?= esc($h['telefono'] ?? '—') ?></td>
                                    <td><span class="badge bg-info-subtle text-info rounded-pill"><?= esc($h['tipo_habitante']) ?></span></td>
                                    <td class="text-center"><?= ($h['es_principal'] == 1) ? '<i class="bi bi-star-fill text-warning"></i>' : '' ?></td>
                                    <td class="text-center"><?= $estadoBadge ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            $aprobados = count(array_filter($habitantes_lista, fn($h) => $h['estado'] === 'aprobado'));
            $pendientes = count(array_filter($habitantes_lista, fn($h) => $h['estado'] === 'pendiente'));
            ?>
            <div class="d-flex gap-3 mt-3">
                <span class="badge bg-light text-dark border px-3 py-2">
                    <i class="bi bi-people-fill me-1 text-success"></i> <?= $aprobados ?> aprobado<?= $aprobados !== 1 ? 's' : '' ?>
                </span>
                <?php if ($pendientes > 0): ?>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                        <i class="bi bi-hourglass-split me-1"></i> <?= $pendientes ?> pendiente<?= $pendientes !== 1 ? 's' : '' ?> de aprobación
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<!-- Modal: Solicitar Nuevo Habitante -->
<div class="modal fade" id="modalSolicitarHabitante" tabindex="-1" aria-labelledby="modalSolicitarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalSolicitarLabel">
                    <i class="bi bi-person-plus me-2"></i>Solicitar Nuevo Habitante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formSolicitarHabitante" action="<?= base_url('residente/habitantes/solicitar') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="alert alert-info border-0 small py-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Tu solicitud será revisada por la administración antes de ser aprobada.
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="sol_nombre" name="nombre" required placeholder="Nombre">
                                <label for="sol_nombre">Nombre *</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="sol_apellido" name="apellido" required placeholder="Apellido">
                                <label for="sol_apellido">Apellido *</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="sol_cedula" name="cedula_identidad" placeholder="Cédula">
                                <label for="sol_cedula">Cédula de Identidad</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="sol_telefono" name="telefono" placeholder="Teléfono">
                                <label for="sol_telefono">Teléfono</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select" id="sol_tipo" name="tipo_habitante" required>
                                    <option value="Familiar">Familiar</option>
                                    <option value="Inquilino">Inquilino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <label for="sol_tipo">Tipo de Habitante *</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnEnviarSolicitud">
                        <i class="bi bi-send me-1"></i> Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formSolicitarHabitante');
    const btnEnviar = document.getElementById('btnEnviarSolicitud');

    if (form && btnEnviar) {
        form.addEventListener('submit', function() {
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
        });
    }
});
</script>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>
