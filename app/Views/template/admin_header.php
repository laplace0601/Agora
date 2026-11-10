<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= nombreEmpresa() ?> — Panel Administrativo</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Global del Panel Admin -->
    <link rel="stylesheet" href="<?= base_url('css/admin_global.css?v=' . time()) ?>">

    <!-- CSS específico de la página (ej: admin_residentes.css) -->
    <?php if (isset($pagina_actual)): ?>
        <link rel="stylesheet" href="<?= base_url('css/' . $pagina_actual . '.css?v=' . time()) ?>">
    <?php endif; ?>

    <!-- Variables CSS dinámicas desde Marca Blanca -->
    <style>
        :root {
            --agora-primary: <?= colorPrimario() ?>;
        }
    </style>
</head>

<body>

    <!-- Barra de navegación del panel admin -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3" role="navigation" aria-label="Panel administrativo">
        <div class="container-fluid px-4">

            <!-- Logo / Marca -->
            <a class="navbar-brand fw-bold fs-4 text-dark d-flex align-items-center" href="<?= site_url(session()->get('rol') === 'root' ? 'super/apartamentos' : 'admin/residentes') ?>">
                <?php if (logoEmpresa()): ?>
                    <img src="<?= logoEmpresa() ?>" alt="<?= esc(nombreEmpresa()) ?>" style="height: 32px;" class="me-2 rounded">
                    <span><?= esc(nombreEmpresa()) ?></span>
                <?php else: ?>
                    <span style="color: <?= colorPrimario() ?>;"><?= mb_substr(nombreEmpresa(), 0, 1) ?></span><?= mb_substr(nombreEmpresa(), 1) ?>
                <?php endif; ?>
                <span class="badge bg-warning text-dark ms-2 small fw-semibold" style="font-size: 0.6rem;">Admin</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarAdmin" aria-controls="navbarAdmin"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-1">
                    <?php if (session()->get('rol') === 'root'): ?>
                        <li class="nav-item">
                            <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'super/apartamentos' ? 'active fw-semibold' : '') ?>"
                                href="<?= site_url('super/apartamentos') ?>">
                                <i class="bi bi-buildings me-1"></i> Inmuebles
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'admin/residentes' ? 'active fw-semibold' : '') ?>"
                            href="<?= site_url('admin/residentes') ?>">
                            <i class="bi bi-building me-1"></i> Departamentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'admin/soporte' ? 'active fw-semibold' : '') ?>"
                            href="<?= site_url('admin/soporte') ?>">
                            <i class="bi bi-headset me-1"></i> Soporte
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
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 <?= (uri_string() === 'admin/finanzas/morosidad' ? 'active' : '') ?>"
                                    href="<?= site_url('admin/finanzas/morosidad') ?>">
                                    <i class="bi bi-exclamation-diamond me-2 text-danger"></i> Morosidad
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
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (str_starts_with(uri_string(), 'admin/areas') ? 'active fw-semibold' : '') ?>"
                            href="<?= site_url('admin/areas-comunes') ?>">
                            <i class="bi bi-tree me-1"></i> Áreas Comunes
                        </a>
                    </li>
                </ul>

                <!-- Acciones de usuario -->
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="text-secondary small fw-bold">
                        <i class="bi bi-person-circle me-1"></i>
                        @<?= session()->get('nombre_usuario') ?? 'admin' ?>
                    </span>
                    <a href="<?= site_url('auth/logout') ?>"
                        class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </a>
                </div>
            </div>

        </div>
    </nav>
