<?php

/**
 * @file marca_blanca.php
 * @description Panel de personalización de identidad visual (Marca Blanca) para el condominio.
 * @framework Renderizado modular acoplado al helper nativo view().
 */
echo view('template/super_header', ['pagina_actual' => 'admin_marca_blanca']);
?>

<main class="p-5" role="main">
    <div class="mb-5 border-bottom pb-4">
        <h1 class="fw-bold h3 text-dark mb-1">Personalización de Marca</h1>
        <p class="text-secondary small mb-0">Adapta la interfaz del sistema con la identidad visual, logotipos y paleta de colores de tu condominio.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border border-light">
                <div class="card-body">
                    <h2 class="h5 fw-bold text-dark mb-4"><i class="bi bi-sliders me-2 text-agora-teal"></i>Ajustes de Identidad</h2>

                    <form action="<?= site_url('super/marca-blanca/guardar') ?>" method="POST" enctype="multipart/form-data">

                        <div class="form-floating mb-4">
                            <input type="text" class="form-control" id="custom-title" name="nombre_personalizado" placeholder="Ágora Residencias" required value="Residencias El Parque">
                            <label for="custom-title">Nombre Comercial del Condominio *</label>
                            <div class="form-text small text-muted">Este nombre reemplazará al texto genérico en las facturas y correos.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-bold text-uppercase">Logotipo Oficial (Fondo Transparente)</label>
                            <div class="upload-zone-agora rounded-3 p-4 text-center border border-dashed d-flex flex-column align-items-center justify-content-center position-relative">
                                <i class="bi bi-cloud-arrow-up text-secondary fs-2 mb-2"></i>
                                <span class="small fw-medium text-dark">Arrastra tu logo aquí o <span class="text-agora-teal cursor-pointer">examina tus archivos</span></span>
                                <span class="fs-7 text-secondary mt-1">Soporta PNG, SVG o JPEG (Máx. 2MB)</span>
                                <input type="file" id="logo-input" name="logo_condominio" accept="image/*" class="position-absolute opacity-0 w-100 h-100 start-0 top-0 cursor-pointer">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label for="color-primary" class="form-label text-secondary small fw-bold text-uppercase">Color Primario (Sidebar)</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color border-end-0 picker-custom" id="color-primary" name="color_primario" value="#1F4E56" title="Elige el color primario">
                                    <input type="text" class="form-control border-start-0 fs-7 uppercase" id="color-primary-text" value="#1F4E56" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="color-accent" class="form-label text-secondary small fw-bold text-uppercase">Color de Acento (Botones)</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color border-end-0 picker-custom" id="color-accent" name="color_acento" value="#C59B4E" title="Elige el color de acento">
                                    <input type="text" class="form-control border-start-0 fs-7 uppercase" id="color-accent-text" value="#C59B4E" readonly>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-agora-admin text-white w-100 py-2.5 rounded-pill fw-semibold shadow-sm mt-2">
                            <i class="bi bi-check-circle me-1"></i> Aplicar Identidad de Marca
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border border-light h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 fw-bold text-dark mb-2"><i class="bi bi-eye me-2 text-agora-teal"></i>Vista Previa en Vivo</h2>
                    <p class="text-secondary small mb-4">Observa cómo interactúan los colores elegidos simulando el menú lateral del sistema.</p>

                    <div class="mockup-sidebar-container rounded-4 shadow-sm overflow-hidden flex-grow-1 p-4 d-flex align-items-center justify-content-center bg-light">
                        <div id="preview-sidebar" class="mockup-sidebar p-3 text-white rounded-3 shadow d-flex flex-column" style="width: 260px; height: 320px; background: #1F4E56; transition: background 0.3s ease;">
                            <div class="text-center fw-bold py-2 border-bottom border-white border-opacity-10 fs-6">
                                <span id="preview-logo-text">RESIDENCIAS EL PARQUE</span>
                            </div>
                            <div class="d-flex flex-column gap-2 mt-4 flex-grow-1">
                                <div id="preview-active-link" class="d-flex align-items-center py-2 px-3 rounded-2 small fw-semibold text-white shadow-sm" style="background: #C59B4E; transition: background 0.3s ease;">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard Activo
                                </div>
                                <div class="d-flex align-items-center py-2 px-3 rounded-2 small text-white text-opacity-50">
                                    <i class="bi bi-building me-2"></i> Apartamentos
                                </div>
                                <div class="d-flex align-items-center py-2 px-3 rounded-2 small text-white text-opacity-50">
                                    <i class="bi bi-cash-coin me-2"></i> Finanzas
                                </div>
                            </div>
                            <div class="fs-8 text-center text-white text-opacity-25 mt-auto">Vista previa a escala</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<?php
echo view('template/super_footer', ['pagina_actual' => 'admin_marca_blanca']);
?>