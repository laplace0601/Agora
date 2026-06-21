<?php 
    $pagina_actual = 'admin_residentes'; // Mapeo automático
    echo view('template/admin_header', ['pagina_actual' => $pagina_actual]);
?>

<main class="container my-5" role="main">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold h3 text-dark mb-0">Directorio de Residentes</h1>
                <p class="text-secondary mt-1">Consulta la información de contacto y ubicación de los residentes registrados.</p>
            </div>
        </div>
    </div>

    <!-- Buscador -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="buscadorResidentes" class="form-control border-start-0" placeholder="Buscar por nombre, cédula, apto...">
            </div>
        </div>
    </div>

    <!-- Tabla de residentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaResidentes">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4 py-3">Residente</th>
                                    <th scope="col" class="py-3">Cédula</th>
                                    <th scope="col" class="py-3">Contacto</th>
                                    <th scope="col" class="py-3">Apartamento(s)</th>
                                    <th scope="col" class="text-center py-3 pe-4">Estado Cuenta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($residentes)): ?>
                                    <?php foreach ($residentes as $res): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="bi bi-person text-secondary fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold text-dark"><?= esc($res['nombre_completo']) ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= esc($res['cedula_identidad']) ?></td>
                                            <td>
                                                <?php if (!empty($res['telefono'])): ?>
                                                    <div class="small"><i class="bi bi-telephone text-muted me-1"></i> <?= esc($res['telefono']) ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($res['correo'])): ?>
                                                    <div class="small"><i class="bi bi-envelope text-muted me-1"></i> <a href="mailto:<?= esc($res['correo']) ?>" class="text-decoration-none"><?= esc($res['correo']) ?></a></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($res['apartamentos'])): ?>
                                                    <?php foreach ($res['apartamentos'] as $apto): ?>
                                                        <span class="badge bg-light text-dark border me-1 mb-1"><?= esc($apto) ?></span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">Sin asignar</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center pe-4">
                                                <?php if (($res['estado'] ?? '') === 'activa'): ?>
                                                    <span class="badge bg-success rounded-pill px-3">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3"><?= esc($res['estado'] ?? 'Inactivo') ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            No hay residentes registrados en el sistema.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscador = document.getElementById('buscadorResidentes');
        const filas = document.querySelectorAll('#tablaResidentes tbody tr');

        buscador.addEventListener('keyup', function(e) {
            const termino = e.target.value.toLowerCase();
            
            filas.forEach(fila => {
                // Obtenemos todo el texto de la fila (nombre, cedula, correo, aptos)
                const texto = fila.textContent.toLowerCase();
                if (texto.includes(termino)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    });
</script>

<?php echo view('template/admin_footer', ['pagina_actual' => $pagina_actual]); ?>
