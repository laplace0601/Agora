<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Ágora CRM</title>
    <!-- Bootstrap 4.6.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Fondo minimalista para resaltar la Card */
        body {
            background-color: #f4f6f9;
        }
    </style>
</head>

<body class="vh-100 d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <!-- Logo / Título de Marca Blanca -->
                            <h4 class="text-primary font-weight-bold mb-1">Ágora CRM</h4>
                            <p class="text-muted">Inicie sesión en su cuenta</p>
                        </div>

                        <!-- Contenedor de Alertas dinámicas -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de Login -->
                        <form id="loginForm" action="<?= site_url('auth/procesar-login') ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="form-group mb-3">
                                <label for="correo" class="font-weight-bold text-secondary">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-lg" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="clave" class="font-weight-bold text-secondary">Contraseña</label>
                                <input type="password" class="form-control form-control-lg" id="clave" name="clave" placeholder="••••••••" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold" id="btnLogin">
                                Ingresar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts requeridos por Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>

</html>