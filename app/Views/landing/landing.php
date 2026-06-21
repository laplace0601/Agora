<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

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
                <svg class="bi" width="1em" height="1em">
                    <use xlink:href="#collection"></use>
                </svg>
            </div>
            <h3 class="fs-4 fw-bold text-dark">Control Financiero</h3>
            <p class="text-muted small">Emisión masiva de recibos de cobro automáticos basados en alícuotas y pasarela para el reporte y validación de pagos.</p>
            <a href="#" class="link-agora d-inline-flex align-items-center mt-auto" data-bs-toggle="modal" data-bs-target="#modalFinanciero">
                Saber más <svg class="bi ms-1" width="1em" height="1em">
                    <use xlink:href="#chevron-right"></use>
                </svg>
            </a>
        </div>

        <div class="col d-flex flex-column align-items-start">
            <div class="feature-icon text-bg-agora bg-gradient fs-2 mb-3">
                <svg class="bi" width="1em" height="1em">
                    <use xlink:href="#people-circle"></use>
                </svg>
            </div>
            <h3 class="fs-4 fw-bold text-dark">Cartelera Digital</h3>
            <p class="text-muted small">Mantén informada a toda la comunidad publicando de forma directa comunicados, reglamentos y noticias oficiales del edificio.</p>
            <a href="#" class="link-agora d-inline-flex align-items-center mt-auto" data-bs-toggle="modal" data-bs-target="#modalCartelera">
                Saber más <svg class="bi ms-1" width="1em" height="1em">
                    <use xlink:href="#chevron-right"></use>
                </svg>
            </a>
        </div>

        <div class="col d-flex flex-column align-items-start">
            <div class="feature-icon text-bg-agora bg-gradient fs-2 mb-3">
                <svg class="bi" width="1em" height="1em">
                    <use xlink:href="#toggles2"></use>
                </svg>
            </div>
            <h3 class="fs-4 fw-bold text-dark">Buzón de Incidencias</h3>
            <p class="text-muted small">Reporte y seguimiento de averías vecinales o quejas, con un sistema interactivo de estatus en tiempo real.</p>
            <a href="#" class="link-agora d-inline-flex align-items-center mt-auto" data-bs-toggle="modal" data-bs-target="#modalIncidencias">
                Saber más <svg class="bi ms-1" width="1em" height="1em">
                    <use xlink:href="#chevron-right"></use>
                </svg>
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
                        <h1 class="card-title pricing-card-title fw-bold mb-4">$15<small class="text-muted fw-light">pago unico</small></h1>
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
                        <h1 class="card-title pricing-card-title fw-bold mb-4">$30<small class="text-muted fw-light">pago unico</small></h1>
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
                        <h1 class="card-title pricing-card-title fw-bold mb-4">$50<small class="text-muted fw-light">pago unico</small></h1>
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

<?= $this->endSection() ?>

<?= $this->include('template/footer') ?>