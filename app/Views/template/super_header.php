<?php

/**
 * @file super_header.php
 * @description Cabecera estructural para el panel de control del Súper Usuario (Ágora Core).
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SúperAdmin - Ágora Core Platform</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= base_url('css/super_global.css?v=' . time()) ?>">

    <?php if (isset($pagina_actual) && !empty($pagina_actual)): ?>
        <link rel="stylesheet" href="<?= base_url('css/' . $pagina_actual . '.css?v=' . time()) ?>">
    <?php endif; ?>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark py-3 shadow" style="background-color: #0F172A;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= site_url('super/apartamentos') ?>">
                <span class="bg-primary text-white px-2 py-1 rounded fs-6"><i class="bi bi-shield-fill-check"></i></span>
                <span>AGORA <span class="text-primary small fw-normal">CORE</span></span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSuper" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSuper">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-1">
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'super/apartamentos' ? 'active fw-semibold bg-white bg-opacity-10' : '') ?>"
                            href="<?= site_url('super/apartamentos') ?>">
                            <i class="bi bi-buildings me-1"></i> Inmuebles
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'super/gestion_usuarios' ? 'active fw-semibold bg-white bg-opacity-10' : '') ?>"
                            href="<?= site_url('super/gestion_usuarios') ?>">
                            <i class="bi bi-person-plus me-1"></i> Gestionar Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-2 px-3 py-2 <?= (uri_string() === 'super/planes' ? 'active fw-semibold bg-white bg-opacity-10' : '') ?>"
                            href="<?= site_url('super/planes') ?>">
                            <i class="bi bi-patch-check me-1"></i> Planes
                        </a>
                    </li>


                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <span class="text-light small fw-bold">
                            <i class="bi bi-person-badge-fill me-1"></i> @<?= session()->get('nombre_usuario') ?? 'root' ?>
                        </span>
                        <a href="<?= site_url('auth/logout') ?>" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                            Salir <i class="bi bi-box-arrow-right ms-1"></i>
                        </a>
                    </div>
            </div>
        </div>
    </nav>


    <div class="min-vh-100">