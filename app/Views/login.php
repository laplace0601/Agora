<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Sistema de Condominio 🏢</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  
  <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #F8F9FA; /* El gris claro de la paleta */
        color: #343A40; /* El gris oscuro para los textos */
    }

    /* Asegura la simetría perfecta de la imagen */
    .object-fit-cover {
        object-fit: cover;
    }

    /* EFECTO PREMIUM DEL BOTÓN */
    .btn-agora {
        background-color: #2E6F7A;
        color: white;
        border: none;
        transition: all 0.3s ease; /* Hace que el movimiento sea suave y elegante */
    }

    .btn-agora:hover {
        background-color: #1F4E56; /* Se oscurece un poco al pasar el mouse */
        transform: translateY(-3px); /* Eleva el botón 3 píxeles */
        box-shadow: 0 6px 12px rgba(46, 111, 122, 0.3); /* Sombra suave azulada */
        color: white;
    }

    .btn-agora:active {
        transform: translateY(-1px); /* Al hacer clic, baja un poquito simulando presión real */
    }
  </style>
</head>
<body class="bg-light">

<section class="p-3 p-md-4 p-xl-5 vh-100 d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xxl-11">
        <div class="card border-light-subtle shadow-sm">
          <div class="row g-0">
            
            <div class="col-12 col-md-6">
              <img class="img-fluid rounded-start w-100 h-100 object-fit-cover" 
                   loading="lazy" 
                   src="<?= base_url('images/edificio.jpg') ?>" 
                   alt="Condominio Residencial">
            </div>
            
            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
              <div class="col-12 col-lg-11 col-xl-10">
                <div class="card-body p-3 p-md-4 p-xl-5">
                  
                  <div class="row">
                    <div class="col-12">
                      <div class="mb-4 text-center">
                        <a href="#!">
                          <img src="<?= base_url('images/agora.jpg') ?>" alt="Agora Logo" style="max-width: 200px; width: 100%; height: auto; border-radius: 8px;">
                        </a>
                        <h2 class="h5 text-center fw-bold text-dark mt-3">Sistema de Gestión de Condominios</h2>
                        <p class="text-center text-muted small">Ingresa tus credenciales para acceder</p>
                      </div>
                    </div>
                  </div>

                  <form action="<?= base_url('login/autenticar') ?>" method="POST" class="needs-validation" novalidate>
                    <div class="row gy-3 overflow-hidden">
                      
                      <div class="col-12">
                        <div class="form-floating mb-1">
                          <input type="email" class="form-control" name="correo" id="correo" placeholder="nombre@correo.com" required>
                          <label for="correo" class="form-label text-secondary">Correo Electrónico</label>
                          <div class="invalid-feedback small ps-2">Por favor, ingresa tu correo electrónico corporativo o residencial.</div>
                        </div>
                      </div>
                      
                      <div class="col-12">
                        <div class="form-floating mb-1">
                          <input type="password" class="form-control" name="clave" id="clave" placeholder="Contraseña" required>
                          <label for="clave" class="form-label text-secondary">Contraseña</label>
                          <div class="invalid-feedback small ps-2">Por favor, escribe la contraseña asociada a tu cuenta.</div>
                        </div>
                      </div>
                      
                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="" name="recuerdame" id="recuerdame">
                          <label class="form-check-label text-secondary small" for="recuerdame">
                            Mantener sesión iniciada
                          </label>
                        </div>
                      </div>
                      
                      <div class="col-12">
                        <div class="d-grid">
                          <button class="btn btn-agora btn-lg fw-bold" type="submit">Ingresar</button>
                        </div>
                      </div>
                    </div>
                  </form>
                  
                  <div class="row">
                    <div class="col-12">
                      <p class="mb-0 mt-5 text-secondary text-center small">
                        ¿Olvidaste tus datos? <a href="#!" class="link-primary text-decoration-none">Contacta al administrador</a>
                      </p>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    /**
     * Lógica de Validación JavaScript para el Formulario de Login
     * Intercepta el envío si existen campos vacíos o incorrectos.
     */
    (() => {
        'use strict'
        // Capturamos el formulario de login con la clase de validación
        const formulario = document.querySelector('.needs-validation')

        formulario.addEventListener('submit', event => {
            // Si el formulario no pasa la prueba de validación nativa de HTML5
            if (!formulario.checkValidity()) {
                event.preventDefault() // Detiene el envío hacia el controlador de CodeIgniter
                event.stopPropagation() // Frena la propagación del evento en el DOM
            }
            // Añadimos la clase para activar los estilos CSS dinámicos de Bootstrap (bordes rojos/verdes)
            formulario.classList.add('was-validated')
        }, false)
    })()
  </script>
</body>
</html>