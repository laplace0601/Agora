<?php 
    /**
     * @file crear_usuario.php
     * @description Panel maestro del Súper Usuario para dar de alta los 3 roles del ecosistema Ágora.
     */
    echo view('template/super_header', ['pagina_actual' => 'super_crear_usuario']); 
?>

<main class="container my-5" role="main">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold h3 text-dark"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Registro Central de Usuarios</h1>
            <p class="text-secondary small">Alta global de cuentas en la plataforma Ágora. Selecciona el tipo de rol correspondiente para desplegar sus parámetros específicos.</p>
        </div>
    </div>

    <ul class="nav nav-pills nav-justified mb-4 bg-white p-2 rounded-4 shadow-sm" id="roleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 py-2.5 fw-semibold" id="residente-tab" data-bs-toggle="tab" data-bs-target="#panel-residente" type="button" role="tab">
                <i class="bi bi-house-door-fill me-2"></i>Nuevo Residente
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2.5 fw-semibold" id="admin-tab" data-bs-toggle="tab" data-bs-target="#panel-admin" type="button" role="tab">
                <i class="bi bi-building-fill-gear me-2"></i>Nuevo Administrador
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2.5 fw-semibold" id="super-tab" data-bs-toggle="tab" data-bs-target="#panel-super" type="button" role="tab">
                <i class="bi bi-shield-lock-fill me-2"></i>Nuevo Súper Usuario
            </button>
        </li>
    </ul>

    <div class="tab-content" id="roleTabsContent">
        
        <div class="tab-pane fade show active" id="panel-residente" role="tabpanel">
            <div class="card card-agora-form p-4 p-md-5 shadow-sm border-0">
                <h2 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">Datos Personales y de Vivienda</h2>
                <form action="<?= site_url('super/guardar-residente') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="nombre" placeholder="Nombre" required><label class="ms-2">Nombres y Apellidos *</label></div>
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="cedula" placeholder="Cédula" required><label class="ms-2">Cédula de Identidad *</label></div>
                        <div class="col-md-6 form-floating"><input type="tel" class="form-control" name="telefono" placeholder="Teléfono" required><label class="ms-2">Número Telefónico *</label></div>
                        <div class="col-md-6 form-floating"><input type="email" class="form-control" name="correo" placeholder="Correo" required><label class="ms-2">Correo Electrónico *</label></div>
                        <div class="col-md-12 form-floating"><input type="password" class="form-control" name="clave" placeholder="Contraseña" required minlength="6"><label class="ms-2">Contraseña Temporal *</label></div>
                        
                        <div class="col-md-6 form-floating">
                            <select class="form-select" name="condominio_id" id="residente-condominio" required>
                                <option value="" selected disabled>Selecciona el condominio asignado</option>
                                <?php if (!empty($condominios)): ?>
                                    <?php foreach ($condominios as $condo): ?>
                                        <option value="<?= $condo['id'] ?>"><?= esc($condo['nombre_condominio']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label class="ms-2">Condominio *</label>
                        </div>
                        <div class="col-md-6 form-floating">
                            <select class="form-select" name="apartamento_id" id="residente-apartamento" required disabled>
                                <option value="" selected disabled>Selecciona primero un condominio</option>
                                <?php if (!empty($apartamentos)): ?>
                                    <?php foreach ($apartamentos as $apto): ?>
                                        <option value="<?= $apto['id'] ?>" data-condominio="<?= $apto['condominio_id'] ?>" style="display: none;">
                                            Apto. <?= esc($apto['nro_apartamento'] ?? $apto['numero'] ?? 'N/A') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label class="ms-2">Nro de Departamento / Ubicación *</label>
                        </div>
                        
                        <div class="col-12 mt-4"><button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold shadow-sm">Crear Cuenta de Residente</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="panel-admin" role="tabpanel">
            <div class="card card-agora-form p-4 p-md-5 shadow-sm border-0">
                <h2 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">Datos Generales y Suscripción del Condominio</h2>
                <form action="<?= site_url('super/guardar-admin') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="nombre" placeholder="Nombre" required><label class="ms-2">Nombres y Apellidos *</label></div>
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="cedula" placeholder="Cédula" required><label class="ms-2">Cédula de Identidad *</label></div>
                        <div class="col-md-6 form-floating"><input type="tel" class="form-control" name="telefono" placeholder="Teléfono" required><label class="ms-2">Número Telefónico *</label></div>
                        <div class="col-md-6 form-floating"><input type="email" class="form-control" name="correo" placeholder="Correo" required><label class="ms-2">Correo Electrónico *</label></div>
                        <div class="col-md-12 form-floating"><input type="password" class="form-control" name="clave" placeholder="Contraseña" required minlength="6"><label class="ms-2">Contraseña Temporal *</label></div>
                        
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="nombre_condominio" placeholder="Condominio" required><label class="ms-2">Nombre Comercial del Condominio *</label></div>
                        <div class="col-md-6 form-floating">
                    <select class="form-select" name="plan_id" id="planSelector" required>
                        <option value="" selected disabled>Selecciona el Plan de Negocio</option>
                        <option value="bronce">🥉 Plan Bronce ($15/mes - Hasta 40 Aptos)</option>
                        <option value="plata">🥈 Plan Plata ($30/mes - Hasta 120 Aptos)</option>
                        <option value="oro">🥇 Plan Oro ($50/mes - Aptos Ilimitados)</option>
                    </select>
                    <label class="ms-2">Plan Comercial Asignado *</label>
                </div>
                        
                        <div class="col-12 mt-4"><button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold shadow-sm">Dar de Alta Condominio y Administrador</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="panel-super" role="tabpanel">
            <div class="card card-agora-form p-4 p-md-5 shadow-sm border-0">
                <h2 class="h5 fw-bold text-dark mb-4 border-bottom pb-2">Credenciales del Equipo Ágora Core</h2>
                <form action="<?= site_url('super/guardar-super') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="nombre" placeholder="Nombre" required><label class="ms-2">Nombres y Apellidos *</label></div>
                        <div class="col-md-6 form-floating"><input type="text" class="form-control" name="cedula" placeholder="Cédula" required><label class="ms-2">Cédula de Identidad *</label></div>
                        <div class="col-md-6 form-floating"><input type="tel" class="form-control" name="telefono" placeholder="Teléfono" required><label class="ms-2">Número Telefónico *</label></div>
                        <div class="col-md-6 form-floating"><input type="email" class="form-control" name="correo" placeholder="Correo" required><label class="ms-2">Correo Electrónico *</label></div>
                        <div class="col-md-12 form-floating"><input type="password" class="form-control" name="clave" placeholder="Contraseña" required minlength="6"><label class="ms-2">Contraseña Temporal *</label></div>
                        
                        <div class="col-12 form-floating">
                            <input type="text" class="form-control" name="area_desempeno" placeholder="Ej: Infraestructura / Soporte / Finanzas" required>
                            <label class="ms-2">Área Operativa de Desempeño *</label>
                        </div>
                        
                        <div class="col-12 mt-4"><button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-semibold shadow-sm"><i class="bi bi-shield-check me-2"></i>Registrar Súper Usuario Maestro</button></div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectCondominio = document.getElementById('residente-condominio');
    const selectApartamento = document.getElementById('residente-apartamento');

    if (selectCondominio && selectApartamento) {
        selectCondominio.addEventListener('change', function() {
            const condoId = this.value;
            let hasOptions = false;

            // Mostrar solo apartamentos del condominio seleccionado
            Array.from(selectApartamento.options).forEach(option => {
                if (option.value === "") return; // Ignorar el placeholder
                
                if (option.getAttribute('data-condominio') === condoId) {
                    option.style.display = '';
                    hasOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            // Habilitar o deshabilitar
            selectApartamento.disabled = !hasOptions;
            
            // Resetear valor si no hay o si se cambia
            selectApartamento.value = "";
            
            if (!hasOptions) {
                selectApartamento.options[0].text = "No hay apartamentos registrados en este condominio";
            } else {
                selectApartamento.options[0].text = "Selecciona un apartamento...";
            }
        });
    }
});
</script>

<?php echo view('template/super_footer', ['pagina_actual' => 'super_crear_usuario']); ?>