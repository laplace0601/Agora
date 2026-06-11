<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ágora - Tu Comunidad, Tu Espacio de Conexión 🏢</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F9FA; /* Gris claro de fondo */
            color: #343A40; /* Gris oscuro para textos legibles */
        }

        /* Color azul corporativo de Ágora */
        .text-bg-agora {
            background-color: #2E6F7A !important;
            color: white !important;
        }

        /* Corrección y centrado del contenedor del icono */
        .feature-icon {
            width: 4rem;
            height: 4rem;
            border-radius: .75rem;
            display: inline-flex; 
            align-items: center;    
            justify-content: center;
        }

        /* Enlaces dorados con efecto de transición suave */
        .link-agora {
            color: #C59B4E;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .link-agora:hover {
            color: #1F4E56; /* Cambio a azul oscuro al pasar el mouse */
        }

        /* Botón premium de inicio de sesión y sus efectos */
        .btn-agora {
            background-color: #C59B4E !important;
            color: white !important;
            font-weight: 600;
            border: none;
            transition: background-color 0.2s ease;
        }
        .btn-agora:hover {
            background-color: #A37F3D !important;
            color: white !important;
        }

        /* Estilos para la sección de bienvenida (Hero) */
        .hero-title {
            font-size: 3.5rem;
            line-height: 1.2;
            color: #1F4E56; /* Nuestro azul oscuro identificativo */
        }
        .btn-outline-agora {
            border: 2px solid #2E6F7A !important;
            color: #2E6F7A !important;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-outline-agora:hover {
            background-color: #2E6F7A !important;
            color: white !important;
        }

        /* Estilos y efectos de elevación (Hover) para las Tarjetas de Planes */
        .card-plan {
            border: 1px solid rgba(0,0,0,.125);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-plan:hover {
            transform: translateY(-5px); /* Efecto de levitación */
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        }
        .plan-destacado {
            border: 2px solid #2E6F7A !important;
        }
        .text-bronze {
            color: #CD7F32;
        }
        .text-gold {
            color: #C59B4E;
        }

        /* Estilos de enfoque para los campos del formulario de contacto */
        .form-control:focus {
            border-color: #2E6F7A;
            box-shadow: 0 0 0 0.25rem rgba(46, 111, 122, 0.25);
        }

        /* Iluminación dinámica para el menú de navegación */
.nav-link-custom {
    color: rgba(255, 255, 255, 0.6) !important; /* Gris claro inicial */
    transition: color 0.3s ease, text-shadow 0.3s ease; /* Transición suave */
}

.nav-link-custom:hover, .nav-link-custom.active {
    color: #ffffff !important; /* Blanco puro al pasar el ratón */
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5); /* Efecto de iluminación sutil */
}
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="#">
                <span style="color: #2E6F7A;">Á</span>gora <span class="ms-2 fs-6 text-muted">🏢</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
    <li class="nav-item">
        <a class="nav-link nav-link-custom active" aria-current="page" href="#">Inicio</a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-link-custom" href="#featured-3">Beneficios</a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-link-custom" href="#planes">Planes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link nav-link-custom" href="#contacto">Contacto</a>
    </li>
</ul>
                
                <div class="d-flex">
                    <a href="<?= base_url('login') ?>" class="btn btn-agora px-4 rounded-pill shadow-sm">Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="collection" viewBox="0 0 16 16">
            <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6v7zm1.5.5A.5.5 0 0 1 1 13V6a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-13z"></path>
        </symbol>
        <symbol id="people-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"></path>
        </symbol>
        <symbol id="toggles2" viewBox="0 0 16 16">
            <path d="M9.465 10H12a2 2 0 1 1 0 4H9.465c.34-.588.535-1.271.535-2 0-.729-.195-1.412-.535-2z"></path>
            <path d="M6 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 1a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm.535-10a3.975 3.975 0 0 1-.409-1H4a1 1 0 0 1 0-2h2.126c.091-.355.23-.69.41-1H4a2 2 0 1 0 0 4h2.535z"></path>
            <path d="M14 4a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"></path>
        </symbol>
        <symbol id="chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"></path>
        </symbol>
    </svg>

    <header class="bg-white py-5 border-bottom">
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                
                <div class="col-10 col-sm-8 col-lg-6 mx-auto">
                    <div class="p-5 text-center rounded-4 shadow" style="background: linear-gradient(135deg, #2E6F7A 0%, #1F4E56 100%); color: white;">
                        <img src="<?= base_url('images/agora.jpg') ?>" alt="Logo Ágora" class="img-fluid mb-3 rounded shadow-sm" style="max-height: 80px;">
                        <h4 class="mt-3 fw-bold">Ágora App</h4>
                        <p class="small text-white-50">Tu edificio en un solo clic.</p>
                        <div class="bg-white bg-opacity-10 p-3 rounded text-start small border border-white border-opacity-10">
                            <code>// Vista previa del Dashboard<br>• Estado de cuenta listo<br>• 0 incidencias pendientes</code>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <span class="badge text-bg-agora mb-2 px-3 py-2 rounded-pill uppercase fw-semibold tracking-wider">¡Hola, Comunidad!</span>
                    <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3 hero-title">La forma inteligente de gestionar tu condominio</h1>
                    <p class="lead text-muted">Olvídate de las carteleras de papel y los recibos perdidos. Ágora conecta a los administradores y copropietarios en una plataforma transparente, rápida y segura.</p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-md-start mt-4">
                        <a href="#featured-3" class="btn btn-agora btn-lg px-4 me-md-2 rounded-pill shadow-sm">Ver características</a>
                        <a href="#planes" class="btn btn-outline-agora btn-lg px-4 rounded-pill">Ver planes</a>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <main class="container px-4 py-5" id="featured-3">
        
        <h2 class="pb-2 border-bottom fw-bold text-dark mb-5">¿Qué ofrece Ágora para tu Condominio?</h2>

        <div class="row g-5 row-cols-1 row-cols-lg-3 mb-5">
            
            <div class="col d-flex flex-column align-items-start">
                <div class="feature-icon text-bg-agora bg-gradient fs-2 mb-3">
                    <svg class="bi" width="1em" height="1em"><use xlink:href="#collection"></use></svg>
                </div>
                <h3 class="fs-4 fw-bold text-dark">Control Financiero</h3>
                <p class="text-muted small">Emisión masiva de recibos de cobro automáticos basados en alícuotas y pasarela para el reporte y validación de pagos.</p>
                <a href="#" class="link-agora d-inline-flex align-items-center mt-auto" data-bs-toggle="modal" data-bs-target="#modalFinanciero">
                    Saber más <svg class="bi ms-1" width="1em" height="1em"><use xlink:href="#chevron-right"></use></svg>
                </a>
            </div>

            <div class="col d-flex flex-column align-items-start">
                <div class="feature-icon text-bg-agora bg-gradient fs-2 mb-3">
                    <svg class="bi" width="1em" height="1em"><use xlink:href="#people-circle"></use></svg>
                </div>
                <h3 class="fs-4 fw-bold text-dark">Cartelera Digital</h3>
                <p class="text-muted small">Mantén informada a toda la comunidad publicando de forma directa comunicados, reglamentos y noticias oficiales del edificio.</p>
                <a href="#" class="link-agora d-inline-flex align-items-center mt-auto" data-bs-toggle="modal" data-bs-target="#modalCartelera">
                    Saber más <svg class="bi ms-1" width="1em" height="1em"><use xlink:href="#chevron-right"></use></svg>
                </a>
            </div>

            <div class="col d-flex flex-column align-items-start">
                <div class="feature-icon text-bg-agora bg-gradient fs-2 mb-3">
                    <svg class="bi" width="1em" height="1em"><use xlink:href="#toggles2"></use></svg>
                </div>
                <h3 class="fs-4 fw-bold text-dark">Buzón de Incidencias</h3>
                <p class="text-muted small">Reporte y seguimiento de averías vecinales o quejas, con un sistema interactivo de estatus en tiempo real.</p>
                <a href="#" class="link-agora d-inline-flex align-items-center mt-auto" data-bs-toggle="modal" data-bs-target="#modalIncidencias">
                    Saber más <svg class="bi ms-1" width="1em" height="1em"><use xlink:href="#chevron-right"></use></svg>
                </a>
            </div>

        </div>

        <div class="modal fade" id="modalFinanciero" tabindex="-1" aria-labelledby="modalFinancieroLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-bg-agora">
                        <h5 class="modal-title fw-bold" id="modalFinancieroLabel">📊 Control Financiero Detallado</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        <p>Nuestra plataforma automatiza la contabilidad del condominio de manera transparente:</p>
                        <ul>
                            <li>Cálculo automático de alícuotas por apartamento.</li>
                            <li>Generación de recibos digitales en formato PDF.</li>
                            <li>Historial de ingresos, egresos y fondos de reserva visibles para toda la comunidad.</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-agora rounded-pill" onclick="irAPlanes('modalFinanciero')">Ver Planes Disponibles</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCartelera" tabindex="-1" aria-labelledby="modalCarteleraLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-bg-agora">
                        <h5 class="modal-title fw-bold" id="modalCarteleraLabel">📢 Cartelera Digital Oficial</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        <p>Centraliza la comunicación del edificio y dile adiós a los chats informales colapsados:</p>
                        <ul>
                            <li>Publicación de comunicados importantes por parte de la junta administradora.</li>
                            <li>Acceso inmediato al reglamento interno del condominio desde cualquier dispositivo.</li>
                            <li>Notificaciones instantáneas para asambleas de vecinos y convocatorias urgentes.</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-agora rounded-pill" onclick="irAPlanes('modalCartelera')">Ver Planes Disponibles</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalIncidencias" tabindex="-1" aria-labelledby="modalIncidenciasLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-bg-agora">
                        <h5 class="modal-title fw-bold" id="modalIncidenciasLabel">🛠️ Gestión de Incidencias</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        <p>Un canal estructurado para resolver problemas y averías comunitarias de forma eficiente:</p>
                        <ul>
                            <li>Reporte directo de fallas (filtraciones, luces quemadas, ascensores dañados) con descripción detallada.</li>
                            <li>Trazabilidad total a través de un sistema de estatus: <em>Pendiente, En Proceso</em> y <em>Resuelto</em>.</li>
                            <li>Historial de mantenimientos ejecutados para una auditoría comunitaria clara.</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-agora rounded-pill" onclick="irAPlanes('modalIncidencias')">Ver Planes Disponibles</button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <section class="bg-light py-5 border-top border-bottom" id="planes">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge text-bg-agora mb-2 px-3 py-2 rounded-pill fw-semibold">Suscripciones</span>
                <h2 class="display-6 fw-bold text-dark">Planes a la medida de tu comunidad</h2>
                <p class="lead text-muted">Selecciona el plan ideal según la cantidad de apartamentos de tu condominio.</p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
                
                <div class="col">
                    <div class="card h-100 text-center rounded-4 shadow-sm card-plan bg-white">
                        <div class="card-header py-4 bg-transparent border-0">
                            <h4 class="my-0 fw-bold text-bronze">🥉 Plan Bronce</h4>
                            <p class="text-muted small mt-1">Para edificios pequeños</p>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title fw-bold mb-4">$15<small class="text-muted fw-light">/mes</small></h1>
                            <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small">
                                <li class="mb-2">🏢 Hasta <strong>40</strong> Apartamentos</li>
                                <li class="mb-2">📊 Control Financiero Básico</li>
                                <li class="mb-2">📢 Cartelera Digital Oficial</li>
                                <li class="mb-2">🛠️ Buzón de Incidencias Básico</li> 
                            </ul>
                            <button type="button" class="w-100 btn btn-outline-agora btn-lg mt-auto rounded-pill" data-bs-toggle="modal" data-bs-target="#modalRegistroBronce">Contratar Bronce</button>
                        </div>
                    </div>
                </div>
                    
                <div class="col">
                    <div class="card h-100 text-center rounded-4 shadow card-plan plan-destacado bg-white position-relative">
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill text-bg-agora px-3 py-2">MÁS POPULAR</span>
                        <div class="card-header py-4 bg-transparent border-0">
                            <h4 class="my-0 fw-bold" style="color: #2E6F7A;">🥈 Plan Plata</h4>
                            <p class="text-muted small mt-1">Para condominios medianos</p>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title fw-bold mb-4">$30<small class="text-muted fw-light">/mes</small></h1>
                            <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small">
                                <li class="mb-2">🏢 Hasta <strong>120</strong> Apartamentos</li>
                                <li class="mb-2">📊 Control Financiero Completo</li>
                                <li class="mb-2">📢 Cartelera Digital Oficial</li>
                                <li class="mb-2">🛠️ Buzón de Incidencias Completo</li>
                            </ul>
                            <button type="button" class="w-100 btn btn-agora btn-lg mt-auto rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistroPlata">Contratar Plata</button>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 text-center rounded-4 shadow-sm card-plan bg-white">
                        <div class="card-header py-4 bg-transparent border-0">
                            <h4 class="my-0 fw-bold text-gold">🥇 Plan Oro</h4>
                            <p class="text-muted small mt-1">Para grandes residencias</p>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h1 class="card-title pricing-card-title fw-bold mb-4">$50<small class="text-muted fw-light">/mes</small></h1>
                            <ul class="list-unstyled mt-2 mb-4 text-start mx-auto w-75 small">
                                <li class="mb-2">🏢 Apartamentos <strong>Ilimitados</strong></li>
                                <li class="mb-2">📊 Todo lo del Plan Plata</li>
                                <li class="mb-2">🚀 Servidor de alta prioridad</li>
                                <li class="mb-2">📞 Soporte Técnico 24/7</li>
                            </ul>
                            <button type="button" class="w-100 btn btn-outline-agora btn-lg mt-auto rounded-pill" data-bs-toggle="modal" data-bs-target="#modalRegistroOro">Contratar Oro</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="bg-white py-5" id="contacto">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge text-bg-agora mb-2 px-3 py-2 rounded-pill fw-semibold">Contacto</span>
                <h2 class="display-6 fw-bold text-dark">¿Tienes dudas o sugerencias?</h2>
                <p class="lead text-muted">Envíanos un mensaje directo y el equipo de desarrollo te responderá lo antes posible.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="p-4 rounded-4 shadow-sm border bg-light">
                        <form action="#" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nombre" class="form-label small fw-semibold text-muted">Nombre Completo</label>
                                <input type="text" class="form-control rounded-pill px-3" id="nombre" placeholder="Ej. Juan Gómez" required>
                                <div class="invalid-feedback small ps-3">Por favor, ingresa tu nombre completo.</div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold text-muted">Correo Electrónico</label>
                                <input type="email" class="form-control rounded-pill px-3" id="email" placeholder="ejemplo@correo.com" required>
                                <div class="invalid-feedback small ps-3">Ingresa un correo electrónico válido (ejemplo@correo.com).</div>
                            </div>
                            <div class="mb-3">
                                <label for="mensaje" class="form-label small fw-semibold text-muted">Mensaje o Consulta</label>
                                <textarea class="form-control rounded-3 p-3" id="mensaje" rows="4" placeholder="Escribe aquí tu duda sobre el sistema para el equipo..." required></textarea>
                                <div class="invalid-feedback small ps-3">El campo de mensaje no puede estar vacío.</div>
                            </div>
                            <button type="submit" class="btn btn-agora w-100 rounded-pill py-2 shadow-sm">Enviar Mensaje al Equipo 🚀</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5 border-top border-secondary border-opacity-25">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <h5 class="fw-bold"><span style="color: #2E6F7A;">Á</span>gora</h5>
                    <p class="small text-white-50 mb-0">© 2026 Sistema de Gestión de Condominios. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="text-white-50 small d-block mb-1">Desarrollado con ❤️ por:</span>
                    <span class="badge bg-secondary bg-opacity-25 text-white p-2 small me-1">Jesús Cedeño</span>
                    <span class="badge bg-secondary bg-opacity-25 text-white p-2 small me-1">Jeremy Castillo</span>
                    <span class="badge bg-secondary bg-opacity-25 text-white p-2 small me-1">Roiner Casanova</span>
                    <span class="badge bg-secondary bg-opacity-25 text-white p-2 small me-1">María Lugo</span>
                    <span class="badge bg-secondary bg-opacity-25 text-white p-2 small">Ángel Morales</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /**
         * Función encargada de cerrar el modal activo de forma limpia,
         * eliminar el fondo oscuro (backdrop) y desplazar la pantalla a la sección de planes.
         * @param {string} modalId - El ID del modal que se desea cerrar.
         */
        function irAPlanes(modalId) {
            // 1. Buscamos la instancia del modal de Bootstrap que está abierto actualmente
            var miModalElemento = document.getElementById(modalId);
            var modalInstancia = bootstrap.Modal.getInstance(miModalElemento);
            
            // 2. Si la instancia existe, le ordenamos que se cierre de inmediato
            if (modalInstancia) {
                modalInstancia.hide();
            }
            
            // 3. Una vez cerrado, hacemos un scroll suave directo hacia el contenedor de planes
            // Usamos un pequeño retraso (setTimeout) para dar tiempo a que Bootstrap limpie el DOM
            setTimeout(function() {
                document.getElementById('planes').scrollIntoView({ 
                    behavior: 'smooth' // Efecto de deslizamiento elegante
                });
            }, 300); // 300 milisegundos es el tiempo ideal de respuesta
        }

        /**
         * Lógica de Validación JavaScript para el Formulario de Contacto
         * Intercepta el envío si existen campos vacíos o incorrectos.
         */
        (() => {
            'use strict'
            // Capturamos el formulario de contacto con la clase de validación
            const formulario = document.querySelector('.needs-validation')

            formulario.addEventListener('submit', event => {
                // Si el formulario no es válido según los atributos HTML5 (required, type="email")
                if (!formulario.checkValidity()) {
                    event.preventDefault() // Detiene el envío real
                    event.stopPropagation() // Frena la propagación del evento
                }
                // Añadimos la clase de Bootstrap para renderizar los colores de error/éxito
                formulario.classList.add('was-validated')
            }, false)
        })()
    </script>
</body>
</html>