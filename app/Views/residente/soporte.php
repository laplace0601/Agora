<?php 
    $pagina_actual = 'residente_soporte'; // Mapeo automático a soporte.css y soporte.js
    echo view('template/residente_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container my-5" role="main">
    <div class="row g-4">
        <section class="col-12 col-lg-4" aria-labelledby="crear-ticket">
            <div class="card-agora-form p-4">
                <h2 id="crear-ticket" class="fw-bold h4 text-dark mb-3">Reportar Incidencia</h2>
                <form action="<?= site_url('residente/soporte/abrir') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="categoriaIncidencia" name="categoria" required>
                            <option value="" selected disabled>Selecciona una opción</option>
                            <option value="1">Ascensores</option>
                            <option value="2">Infraestructura</option>
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
                    <button type="submit" class="btn btn-agora-primary w-100 py-2.5 rounded-3">Abrir Ticket</button>
                </form>
            </div>
        </section>

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
                            <tr>
                                <td class="fw-bold ps-3">#0421</td>
                                <td>Falla en la botonera del Ascensor B</td>
                                <td><span class="badge rounded-pill bg-warning text-dark px-2.5 py-1.5">Abierto</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</main>

<?php echo view('template/residente_footer', ['pagina_actual' => $pagina_actual]); ?>