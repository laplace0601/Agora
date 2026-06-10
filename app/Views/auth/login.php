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
                        <div id="loginAlert"></div>

                        <!-- Formulario de Login -->
                        <form id="loginForm">
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

    <!-- Lógica de Login con Fetch API -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const btnLogin = document.getElementById('btnLogin');
            const loginAlert = document.getElementById('loginAlert');

            loginForm.addEventListener('submit', function(e) {
                // 1. Prevenir el recargo de la página
                e.preventDefault();

                // Limpiar alertas previas
                loginAlert.innerHTML = '';
                
                // 2. Deshabilitar botón y cambiar texto
                const originalBtnText = btnLogin.innerHTML;
                btnLogin.disabled = true;
                btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verificando...';

                // 3. Armar objeto JSON (o FormData) con los valores
                const formData = new FormData(loginForm);
                const data = Object.fromEntries(formData.entries());

                // 4. Hacer fetch POST dinámicamente usando site_url de CodeIgniter
                fetch('<?= site_url('auth/login') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    // 5. Manejo de respuestas según HTTP Status
                    if (response.status === 200) {
                        // Éxito
                        loginAlert.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>¡Acceso concedido!</strong> Redirigiendo...
                            </div>
                        `;
                        // Redirigir al dashboard después de 1 segundo
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 1000);
                    } 
                    else if (response.status === 400 || response.status === 401 || response.status === 422) {
                        // Credenciales incorrectas o campos vacíos
                        loginAlert.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Credenciales incorrectas o campos vacíos.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        // Volver a habilitar el botón
                        btnLogin.disabled = false;
                        btnLogin.innerHTML = originalBtnText;
                    } 
                    else {
                        // Otros errores de servidor no especificados
                        loginAlert.innerHTML = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                Ocurrió un error inesperado (Error ${response.status}).
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        btnLogin.disabled = false;
                        btnLogin.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Error de red:', error);
                    loginAlert.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error de conexión con el servidor. Intente nuevamente.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    btnLogin.disabled = false;
                    btnLogin.innerHTML = originalBtnText;
                });
            });
        });
    </script>
</body>
</html>
