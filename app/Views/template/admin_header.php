<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ágora — Panel Administrativo</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS específico de la página (ej: admin_apartamentos.css) -->
    <?php if (isset($pagina_actual)): ?>
        <link rel="stylesheet" href="<?= base_url('css/' . $pagina_actual . '.css') ?>">
    <?php endif; ?>
</head>

<body>

    <!-- Barra de navegación del panel admin -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3" role="navigation" aria-label="Panel administrativo">
        <div class="container-fluid px-4">

            <!-- Logo / Marca -->
            <a class="navbar-brand fw-bold fs-4 text-dark" href="<?= site_url('admin/apartamentos') ?>">
                <span style="color: #D97706;">Á</span>GORA
                <span class="badge bg-warning text-dark ms-2 small fw-semibold" style="font-size: 0.6rem;">Admin</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarAdmin" aria-controls="navbarAdmin"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarAdmin">
                <!-- Menú principal -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-1">
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'admin/apartamentos' ? 'active fw-semibold' : '') ?>"
                            href="<?= site_url('admin/apartamentos') ?>">
                            <i class="bi bi-buildings me-1"></i> Inmuebles
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle rounded-2 px-3 py-2 <?= (str_starts_with(uri_string(), 'admin/finanzas') ? 'active fw-semibold' : '') ?>"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-wallet2 me-1"></i> Finanzas
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm rounded-3">
                            <li>
                                <a class="dropdown-item rounded-2 py-2 <?= (uri_string() === 'admin/finanzas/cobro' ? 'active' : '') ?>"
                                   href="<?= site_url('admin/finanzas/cobro') ?>">
                                    <i class="bi bi-receipt me-2 text-secondary"></i> Generar Cobro
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 <?= (uri_string() === 'admin/finanzas/pagos' ? 'active' : '') ?>"
                                   href="<?= site_url('admin/finanzas/pagos') ?>">
                                    <i class="bi bi-check2-circle me-2 text-secondary"></i> Validar Pagos
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'admin/cartelera' ? 'active fw-semibold' : '') ?>"
                            href="<?= site_url('admin/cartelera') ?>">
                            <i class="bi bi-megaphone me-1"></i> Cartelera
                        </a>
                    </li>
                </ul>

                <!-- Acciones de usuario -->
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary small">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= session()->get('correo') ?? 'Administrador' ?>
                    </span>
                    <a href="<?= site_url('auth/logout') ?>"
                        class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </a>
                </div>
            </div>

        </div>
    </nav>