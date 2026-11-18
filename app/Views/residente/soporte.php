<?php
$pagina_actual = 'residente_soporte'; // Mapeo automático a soporte.css y soporte.js
echo view('template/residente_header', ['pagina_actual' => $pagina_actual]);
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

    <!-- Botón Volver al Dashboard fuera de las columnas para que no rompa el diseño -->
    <div class="mb-4">
        <a href="<?= base_url('residente/dashboard') ?>" class="text-decoration-none text-secondary small d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Contenedor principal de rejilla (Fila global) -->
    <div class="row g-4">
       
        <!-- Formulario: Ocupa 4 columnas de 12 en pantallas grandes -->
        <section class="col-12 col-lg-4" aria-labelledby="crear-ticket">
            <div class="card-agora-form p-4">
                <h2 id="crear-ticket" class="fw-bold h4 text-dark mb-3">Reportar Incidencia</h2>
                <form id="formReportarIncidencia" action="<?= base_url('residente/soporte/abrir') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="categoriaIncidencia" name="categoria" required>
                            <option value="" selected disabled>Selecciona una opción</option>
                            <option value="Ascensores">Ascensores</option>
                            <option value="Infraestructura">Infraestructura</option>
                            <option value="Limpieza">Limpieza</option>
                            <option value="Seguridad">Seguridad</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <label for="categoriaIncidencia">Área del Problema</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="asuntoTicket" name="asunto" placeholder="Asunto" required>
                        <label for="asuntoTicket">Asunto Breve</label>
                    </div>
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="detallesTicket" name="descripcion" placeholder="Detalles" style="height: 120px" required></textarea>
                        <label for="detallesTicket">Descripción Detallada</label>
                    </div>
                    <button type="submit" id="btnAbrirTicket" class="btn btn-success w-100 py-2.5 rounded-3">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="spinnerTicket" role="status" aria-hidden="true"></span>
                        <span id="textoTicket">Abrir Ticket</span>
                    </button>
                </form>
            </div>
        </section>

        <!-- Historial: Ocupa 8 columnas de 12 en pantallas grandes -->
        <section class="col-12 col-lg-8" aria-labelledby="historial-tickets">
            <div class="card-agora-form p-4">
                <h2 id="historial-tickets" class="fw-bold h4 text-dark mb-4">Tus Reportes de Soporte</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-dark">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th scope="col" class="ps-3">ID</th>
                                <th scope="col">Asunto</th>
                                <th scope="col">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (!empty($mis_tickets)): ?>
                                <?php foreach ($mis_tickets as $ticket): ?>
                                    <tr>
                                        <td class="fw-bold ps-3">#<?= str_pad($ticket['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= esc($ticket['asunto']) ?></td>
                                        <td>
                                            <?php if ($ticket['estado'] === 'Abierto'): ?>
                                                <span class="badge rounded-pill bg-warning text-dark px-2.5 py-1.5">Abierto</span>
                                            <?php elseif ($ticket['estado'] === 'En Progreso'): ?>
                                                <span class="badge rounded-pill bg-info text-dark px-2.5 py-1.5">En Progreso</span>
                                            <?php elseif ($ticket['estado'] === 'Cerrado'): ?>
                                                <span class="badge rounded-pill bg-success px-2.5 py-1.5">Cerrado</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-secondary px-2.5 py-1.5"><?= esc($ticket['estado']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No has abierto ningún ticket de soporte.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>