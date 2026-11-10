<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= nombreEmpresa() ?> — Iniciar Sesión</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">

    <!-- Variables CSS dinámicas desde Marca Blanca -->
    <style>
        :root {
            --agora-primary: <?= colorPrimario() ?>;
        }
        .btn-agora { background-color: var(--agora-primary); }
        .btn-agora:hover { background-color: var(--agora-primary); filter: brightness(0.85); }
    </style>
</head>
<body class="bg-light">

    <?= $this->renderSection('content') ?>
