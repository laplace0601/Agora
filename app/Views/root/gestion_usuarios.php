<?php 
    /**
     * @file gestion_usuarios.php
     * @description Panel maestro del Súper Usuario para ver, editar y eliminar usuarios.
     */
    echo view('template/super_header', ['pagina_actual' => 'super_gestion_usuarios']); 
?>

<main class="container my-5" role="main">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold h3 text-dark"><i class="bi bi-people-fill me-2 text-primary"></i>Gestión de Usuarios</h1>
            <p class="text-secondary small">Administración integral de cuentas de la plataforma Ágora.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?= base_url('super/crear-usuario') ?>" class="btn btn-success text-white rounded-pill px-4 py-2 shadow-sm fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Crear Usuario
            </a>
        </div>
    </div>

    <!-- Alertas Flashdata y Dinámicas -->
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
    <div id="alert-container"></div>

    <div class="card card-agora-form border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabla-usuarios">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-secondary">ID</th>
                            <th scope="col" class="text-secondary">Nombre de Usuario</th>
                            <th scope="col" class="text-secondary">Correo</th>
                            <th scope="col" class="text-secondary">Rol</th>
                            <th scope="col" class="text-secondary">Estado</th>
                            <th scope="col" class="text-end text-secondary">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-usuarios">
                        <?php if (empty($usuarios)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                        <?php else: ?>
                            <?php 
                            $currentUserId = session()->get('usuario_id') ?? 0;
                            foreach ($usuarios as $u): 
                                $rolBadge = 'bg-secondary';
                                if ($u['rol'] === 'root') $rolBadge = 'bg-danger';
                                elseif ($u['rol'] === 'admin') $rolBadge = 'bg-primary';
                                elseif ($u['rol'] === 'residente') $rolBadge = 'bg-success';

                                $estadoBadge = ($u['estado'] === 'activo') ? 'bg-success' : 'bg-warning text-dark';
                                $isSelf = ((int)$u['id'] === (int)$currentUserId);
                            ?>
                                <tr>
                                    <td class="fw-bold text-muted">#<?= esc($u['id']) ?></td>
                                    <td class="fw-medium">@<?= esc($u['nombre_usuario'] ?? 'N/A') ?></td>
                                    <td><?= esc($u['correo']) ?></td>
                                    <td><span class="badge <?= $rolBadge ?> rounded-pill text-uppercase" style="font-size:0.75rem;"><?= esc($u['rol']) ?></span></td>
                                    <td><span class="badge <?= $estadoBadge ?> rounded-pill text-uppercase" style="font-size:0.75rem;"><?= esc($u['estado']) ?></span></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2 btn-editar" data-usuario='<?= json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                        <?php if (!$isSelf): ?>
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-eliminar" data-id="<?= esc($u['id']) ?>" data-nombre="<?= esc($u['nombre_usuario'] ?? $u['correo']) ?>">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-2 py-1">Tú</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal: Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-editar-usuario" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <input type="hidden" id="edit-id" name="id">
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="edit-correo" name="correo" required>
                        <label for="edit-correo">Correo Electrónico *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="edit-rol" name="rol" required>
                            <option value="residente">Residente</option>
                            <option value="admin">Administrador</option>
                            <option value="root">Súper Root</option>
                        </select>
                        <label for="edit-rol">Rol *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="edit-estado" name="estado" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                        <label for="edit-estado">Estado *</label>
                    </div>

                    <hr class="my-4 text-muted">

                    <h6 class="fw-bold mb-3">Seguridad</h6>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="edit-clave" name="nueva_clave" placeholder="Nueva Contraseña" minlength="8">
                        <label for="edit-clave">Nueva Contraseña (Opcional - mín. 8 caracteres)</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="password" class="form-control" id="edit-clave-confirm" name="nueva_clave_confirm" placeholder="Confirmar Nueva Contraseña" minlength="8">
                        <label for="edit-clave-confirm">Confirmar Nueva Contraseña</label>
                        <div class="form-text text-muted small mt-1">Déjalo en blanco si no deseas cambiar la contraseña actual. Si se ingresa, debe tener mínimo 8 caracteres y coincidir en ambas casillas.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary text-white rounded-3 fw-bold px-4" id="btn-guardar-edicion">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formEditar = document.getElementById('form-editar-usuario');
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    const alertContainer = document.getElementById('alert-container');

    // Variable global para identificar al usuario actual
    let currentUserId = <?= session()->get('usuario_id') ?? 0 ?>;

    function enlazarEventos() {
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                const u = JSON.parse(this.getAttribute('data-usuario'));
                abrirModalEdicion(u);
            });
        });

        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                if (confirm(`¿Estás seguro de que deseas eliminar lógicamente al usuario ${nombre}? No podrá acceder al sistema.`)) {
                    eliminarUsuario(id);
                }
            });
        });
    }

    // Modal de edición
    function abrirModalEdicion(usuario) {
        formEditar.action = '<?= base_url("super/api/usuarios/update/") ?>' + usuario.id;
        document.getElementById('edit-id').value = usuario.id;
        document.getElementById('edit-correo').value = usuario.correo;
        document.getElementById('edit-rol').value = usuario.rol;
        document.getElementById('edit-estado').value = usuario.estado;
        document.getElementById('edit-clave').value = ''; // Limpiar campo clave
        document.getElementById('edit-clave-confirm').value = ''; // Limpiar campo confirmación
        
        // Bloquear rol si es él mismo
        if (parseInt(usuario.id) === currentUserId) {
            document.getElementById('edit-rol').disabled = true;
            document.getElementById('edit-estado').disabled = true;
        } else {
            document.getElementById('edit-rol').disabled = false;
            document.getElementById('edit-estado').disabled = false;
        }

        modalEditar.show();
    }

    // Validación de coincidencia de contraseñas en edición
    const editClave = document.getElementById('edit-clave');
    const editClaveConfirm = document.getElementById('edit-clave-confirm');
    function validarClavesEdicion() {
        if (editClave.value.length > 0 && editClave.value.length < 8) {
            editClave.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
        } else {
            editClave.setCustomValidity('');
        }
        if (editClaveConfirm.value.length > 0 && editClave.value !== editClaveConfirm.value) {
            editClaveConfirm.setCustomValidity('Las contraseñas no coinciden.');
        } else {
            editClaveConfirm.setCustomValidity('');
        }
    }
    editClave.addEventListener('input', validarClavesEdicion);
    editClaveConfirm.addEventListener('input', validarClavesEdicion);

    // Guardar Edición (Envío Tradicional POST Nativo)
    formEditar.addEventListener('submit', function(e) {
        const btnGuardar = document.getElementById('btn-guardar-edicion');
        const nuevaClave = document.getElementById('edit-clave').value;
        const nuevaClaveConfirm = document.getElementById('edit-clave-confirm').value;

        // Validar contraseña si se escribió en cualquiera de las casillas
        if (nuevaClave || nuevaClaveConfirm) {
            if (nuevaClave.length < 8) {
                e.preventDefault();
                alert('La nueva contraseña debe tener al menos 8 caracteres.');
                return;
            }
            if (nuevaClave !== nuevaClaveConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor verifíquelas en ambas casillas.');
                return;
            }
        }

        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    });

    // Eliminar Usuario (Envío Tradicional POST Nativo)
    function eliminarUsuario(id) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = '<?= base_url("super/api/usuarios/delete/") ?>' + id;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '<?= csrf_token() ?>';
        csrf.value = '<?= csrf_hash() ?>';
        f.appendChild(csrf);
        
        document.body.appendChild(f);
        f.submit();
    }

    // Alertas dinámicas Bootstrap
    function showAlert(type, message) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        // Auto-cerrar después de 4 segundos
        setTimeout(() => {
            const alertNode = document.querySelector('.alert');
            if (alertNode) {
                const bsAlert = new bootstrap.Alert(alertNode);
                bsAlert.close();
            }
        }, 4000);
    }

    // Inicializar eventos en la tabla renderizada por el servidor
    enlazarEventos();
});
</script>

<?php echo view('template/super_footer', ['pagina_actual' => 'super_gestion_usuarios']); ?>
