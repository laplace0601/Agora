<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ágora - Portal Residencial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- CSS Global del Portal Residente -->
    <link rel="stylesheet" href="<?= base_url('css/residente_global.css?v=' . time()) ?>">

    <!-- CSS específico de la página (ej: residente_dashboard.css) -->
    <?php if (isset($pagina_actual)): ?>
        <link rel="stylesheet" href="<?= base_url('css/' . $pagina_actual . '.css?v=' . time()) ?>">
    <?php endif; ?>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-agora py-3" role="navigation">
        <div class="container">
            <a class="navbar-brand brand-agora" href="<?= site_url('residente/dashboard') ?>">
                <span style="color: #D97706;">Á</span>GORA
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary small fw-medium">
                        <?php if (!empty($apartamentos)): ?>
                            <i class="bi bi-door-open me-1"></i> Apto. <?= htmlspecialchars($apartamentos[0]['nro_apartamento']) ?>
                        <?php else: ?>
                            Sin Apartamento Asignado
                        <?php endif; ?>
                        <!-- ↓↓↓ BOTÓN DE CERRAR SESIÓN ↓↓↓ -->
                        <a href="<?= site_url('auth/logout') ?>"
                            class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                            <i class="bi bi-box-arrow-right me-1"></i> Salir
                        </a>
                        <!-- ↑↑↑ BOTÓN DE CERRAR SESIÓN ↑↑↑ -->
                    </span>
                </div>
            </div>
        </div>
    </nav>