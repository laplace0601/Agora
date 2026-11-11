<?php

/**
 * @file marca_blanca.php
 * @description Panel de personalización de identidad visual (Marca Blanca) para el condominio.
 * @framework Renderizado modular acoplado al helper nativo view().
 */
echo view('template/super_header', ['pagina_actual' => 'admin_marca_blanca']);
?>

<main class="p-5" role="main">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-5 border-bottom pb-4">
        <h1 class="fw-bold h3 text-dark mb-1">Personalización de Marca</h1>
        <p class="text-secondary small mb-0">Adapta la interfaz del sistema con la identidad visual, logotipos y datos de pago de tu condominio.</p>
    </div>

    <!-- Contenedor general ampliado al 100% (col-12) sin la vista previa lateral -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border border-light">
                <div class="card-body">
                    <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-sliders me-2 text-agora-teal"></i>Ajustes de Identidad</h2>

                    <form action="<?= site_url('super/marca-blanca/guardar') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Nombre Comercial -->
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control" id="custom-title" name="nombre_empresa" placeholder="Ágora Residencias" required value="<?= esc($marca['nombre_empresa'] ?? '') ?>">
                            <label for="custom-title">Nombre Comercial del Condominio *</label>
                            <div class="form-text small text-muted">Este nombre reemplazará al texto genérico en las cabeceras, recibos y correos.</div>
                        </div>

                        <!-- Logo Actual (preview) -->
                        <?php if (!empty($marca['logo_url'])): ?>
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Logo Actual</label>
                                <div class="text-center"><img src="<?= base_url($marca['logo_url']) ?>" alt="Logo actual" style="max-height: 80px;" class="rounded"></div>
                            </div>
                        <?php endif; ?>

                        <!-- Subida de Logo -->
                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-bold text-uppercase">Logotipo Oficial (Fondo Transparente)</label>
                            <div class="upload-zone-agora rounded-3 p-4 text-center border border-dashed d-flex flex-column align-items-center justify-content-center position-relative">
                                <i class="bi bi-cloud-arrow-up text-secondary fs-2 mb-2"></i>
                                <span class="small fw-medium text-dark">Arrastra tu logo aquí o <span class="text-agora-teal cursor-pointer">examina tus archivos</span></span>
                                <span class="fs-7 text-secondary mt-1">Soporta PNG, SVG o JPEG (Máx. 2MB)</span>
                                <input type="file" id="logo-input" name="logo_condominio" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="position-absolute opacity-0 w-100 h-100 start-0 top-0 cursor-pointer">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- ============================================= -->
                        <!-- IMAGEN DE FONDO DEL LOGIN                     -->
                        <!-- ============================================= -->
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-image me-2 text-agora-teal"></i>Imagen de Fondo del Login</h6>

                        <?php if (!empty($marca['login_bg_url'])): ?>
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Fondo Actual</label>
                                <div class="text-center">
                                    <img src="<?= base_url($marca['login_bg_url']) ?>" alt="Fondo login actual" style="max-height: 120px; max-width: 100%; border-radius: 8px;" class="shadow-sm border">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <div class="upload-zone-agora rounded-3 p-4 text-center border border-dashed d-flex flex-column align-items-center justify-content-center position-relative">
                                <i class="bi bi-card-image text-secondary fs-2 mb-2"></i>
                                <span class="small fw-medium text-dark">Sube la imagen de fondo para la pantalla de inicio de sesión</span>
                                <span class="fs-7 text-secondary mt-1">JPG, PNG o WebP — Máx. 5MB — Resolución recomendada: 1920×1080</span>
                                <input type="file" name="login_bg" accept="image/jpeg,image/png,image/webp" class="position-absolute opacity-0 w-100 h-100 start-0 top-0 cursor-pointer">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Correo de Contacto -->
                        <div class="mb-4">
                            <label for="correo-contacto" class="form-label text-secondary small fw-bold text-uppercase">Correo de Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="correo-contacto" name="correo_contacto" placeholder="contacto@micondominio.com" value="<?= esc($marca['correo_contacto'] ?? '') ?>">
                            </div>
                            <div class="form-text small text-muted">Este correo aparecerá como remitente en las notificaciones del sistema.</div>
                        </div>

                        <!-- Teléfono de Soporte / Contacto -->
                        <div class="mb-4">
                            <label for="telefono-contacto" class="form-label text-secondary small fw-bold text-uppercase">Teléfono de Soporte (Pantalla de Login)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control" id="telefono-contacto" name="telefono_contacto" placeholder="+58 412-1234567" value="<?= esc($marca['telefono_contacto'] ?? '') ?>">
                            </div>
                            <div class="form-text small text-muted">Este número se mostrará en el mensaje "¿Olvidaste tus datos? Llama a..." en la pantalla de inicio de sesión.</div>
                        </div>

                        <hr class="my-4">

                        <!-- ============================================= -->
                        <!-- DATOS DE PAGO DESTINO                         -->
                        <!-- ============================================= -->
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-credit-card-2-front me-2 text-agora-teal"></i>Datos de Pago Destino</h6>
                        <p class="text-secondary small mb-3">Estos datos se mostrarán a los residentes al momento de reportar un pago.</p>

                        <div class="mb-3">
                            <label for="cedula-pago" class="form-label text-secondary small fw-bold text-uppercase">Cédula / RIF del Titular</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" class="form-control" id="cedula-pago" name="cedula_pago" placeholder="V-12345678 o J-12345678-9" value="<?= esc($marca['cedula_pago'] ?? '') ?>" maxlength="20">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telefono-pago" class="form-label text-secondary small fw-bold text-uppercase">Teléfono de Pago Móvil</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="text" class="form-control" id="telefono-pago" name="telefono_pago" placeholder="0412-1234567" value="<?= esc($marca['telefono_pago'] ?? '') ?>" maxlength="30">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="banco-pago" class="form-label text-secondary small fw-bold text-uppercase">Banco Destino</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                <input type="text" class="form-control" id="banco-pago" name="banco_pago" placeholder="Banesco, Mercantil, Provincial..." value="<?= esc($marca['banco_pago'] ?? '') ?>" maxlength="100">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success text-white w-100 py-2 rounded-pill fw-semibold shadow-sm mt-2" style="background-color: #198754; border-color: #198754;">
                            <i class="bi bi-check-circle me-1"></i> Aplicar Identidad de Marca
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
echo view('template/super_footer', ['pagina_actual' => 'admin_marca_blanca']);
?>