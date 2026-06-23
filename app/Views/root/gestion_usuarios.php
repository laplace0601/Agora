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
            <a href="<?= site_url('super/crear-usuario') ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold">
                <i class="bi bi-person-plus-fill me-1"></i> Crear Usuario
            </a>
        </div>
    </div>

    <!-- Alertas -->
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
                        <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Cargando usuarios...</td></tr>
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
            <form id="form-editar-usuario">
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
                    <div class="form-floating mb-2">
                        <input type="password" class="form-control" id="edit-clave" name="nueva_clave" placeholder="Nueva Contraseña">
                        <label for="edit-clave">Nueva Contraseña (Opcional)</label>
                        <div class="form-text text-muted small mt-1">Déjalo en blanco si no deseas cambiar la contraseña actual.</div>
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
    const tbody = document.getElementById('tbody-usuarios');
    const formEditar = document.getElementById('form-editar-usuario');
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    const alertContainer = document.getElementById('alert-container');
    
    // Variables globales
    let currentUserId = <?= session()->get('usuario_id') ?? 0 ?>;
    let csrfToken = '<?= csrf_hash() ?>';

    // Cargar usuarios
    async function loadUsuarios() {
        try {
            const response = await fetch('<?= site_url("super/api/usuarios") ?>');
            const result = await response.json();
            
            if (result.status === 'success') {
                renderTable(result.data);
            } else {
                showAlert('danger', 'Error al cargar usuarios.');
            }
        } catch (error) {
            showAlert('danger', 'Error de conexión.');
        }
    }

    // Renderizar tabla
    function renderTable(usuarios) {
        tbody.innerHTML = '';
        if (usuarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>';
            return;
        }

        usuarios.forEach(u => {
            const tr = document.createElement('tr');
            
            // Badge color para el rol
            let rolBadge = 'bg-secondary';
            if (u.rol === 'root') rolBadge = 'bg-danger';
            else if (u.rol === 'admin') rolBadge = 'bg-primary';
            else if (u.rol === 'residente') rolBadge = 'bg-success';

            // Badge color para el estado
            let estadoBadge = u.estado === 'activo' ? 'bg-success' : 'bg-warning text-dark';

            // Botones de acción
            let botones = `
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2 btn-editar" data-usuario='${JSON.stringify(u)}'>
                    <i class="bi bi-pencil-square"></i> Editar
                </button>
            `;

            // No permitir auto-eliminarse en la UI (aunque el backend ya lo protege)
            if (parseInt(u.id) !== currentUserId) {
                botones += `
                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-eliminar" data-id="${u.id}" data-nombre="${u.nombre_usuario || u.correo}">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                `;
            } else {
                botones += `<span class="badge bg-light text-muted border px-2 py-1">Tú</span>`;
            }

            tr.innerHTML = `
                <td class="fw-bold text-muted">#${u.id}</td>
                <td class="fw-medium">@${u.nombre_usuario || 'N/A'}</td>
                <td>${u.correo}</td>
                <td><span class="badge ${rolBadge} rounded-pill text-uppercase" style="font-size:0.75rem;">${u.rol}</span></td>
                <td><span class="badge ${estadoBadge} rounded-pill text-uppercase" style="font-size:0.75rem;">${u.estado}</span></td>
                <td class="text-end">${botones}</td>
            `;
            tbody.appendChild(tr);
        });

        // Asignar eventos de edición y eliminación
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
        document.getElementById('edit-id').value = usuario.id;
        document.getElementById('edit-correo').value = usuario.correo;
        document.getElementById('edit-rol').value = usuario.rol;
        document.getElementById('edit-estado').value = usuario.estado;
        document.getElementById('edit-clave').value = ''; // Limpiar campo clave
        
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

    // Guardar Edición
    formEditar.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btnGuardar = document.getElementById('btn-guardar-edicion');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

        const id = document.getElementById('edit-id').value;
        const formData = new FormData();
        formData.append('correo', document.getElementById('edit-correo').value);
        
        // Si no está deshabilitado, capturar rol y estado
        if (!document.getElementById('edit-rol').disabled) {
            formData.append('rol', document.getElementById('edit-rol').value);
            formData.append('estado', document.getElementById('edit-estado').value);
        } else {
            // Valores por defecto para él mismo para pasar validación backend
            formData.append('rol', document.getElementById('edit-rol').value);
            formData.append('estado', document.getElementById('edit-estado').value);
        }

        const nuevaClave = document.getElementById('edit-clave').value;
        if (nuevaClave) {
            formData.append('nueva_clave', nuevaClave);
        }

        // Agregar Token CSRF
        formData.append('<?= csrf_token() ?>', csrfToken);

        try {
            const response = await fetch(`<?= site_url("super/api/usuarios/update/") ?>${id}`, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            if (result.csrf) csrfToken = result.csrf; // Actualizar token
            
            if (response.ok && result.status === 'success') {
                modalEditar.hide();
                showAlert('success', 'Usuario actualizado correctamente.');
                loadUsuarios();
            } else {
                showAlert('danger', result.error || 'Error al actualizar el usuario.');
            }
        } catch (error) {
            showAlert('danger', 'Error de red al actualizar.');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar Cambios';
        }
    });

    // Eliminar Usuario
    async function eliminarUsuario(id) {
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', csrfToken);

        try {
            const response = await fetch(`<?= site_url("super/api/usuarios/delete/") ?>${id}`, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            if (result.csrf) csrfToken = result.csrf; // Actualizar token
            
            if (response.ok && result.status === 'success') {
                showAlert('success', 'Usuario eliminado lógicamente.');
                loadUsuarios();
            } else {
                showAlert('danger', result.error || 'Error al eliminar usuario.');
            }
        } catch (error) {
            showAlert('danger', 'Error de red al eliminar.');
        }
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

    // Inicializar
    loadUsuarios();
});
</script>

<?php echo view('template/admin_footer'); ?>
