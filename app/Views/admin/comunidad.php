<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad y Soporte - Ágora CRM</title>
    <!-- Bootstrap 4.6.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .comunicado-item {
            border-left: 4px solid #007bff;
            background-color: white;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
    </style>
</head>
<body>

    <!-- 1. Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container-fluid px-md-5">
            <a class="navbar-brand font-weight-bold text-white" href="#">Panel de Administración - Ágora</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav mr-auto">
                    <!-- Nav items de admin si los hay -->
                </ul>
                <span class="navbar-text text-light mr-3">
                    Hola, Administrador
                </span>
                <button class="btn btn-outline-danger mt-2 mt-lg-0 font-weight-bold" id="btnLogout">
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <!-- 2. Layout Principal -->
    <div class="container-fluid px-md-5">
        <!-- Alerta Global -->
        <div id="globalAlert"></div>

        <div class="row">
            
            <!-- COLUMNA IZQUIERDA: Cartelera / Bitácora (col-md-6) -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="text-primary font-weight-bold mb-0">Publicar Nuevo Comunicado</h5>
                        <small class="text-muted">Anuncie novedades y avisos a todos los residentes</small>
                    </div>
                    <div class="card-body">
                        <div id="comunicadoAlert"></div>

                        <form id="formComunicado">
                            <div class="form-group">
                                <label for="titulo" class="font-weight-bold text-secondary">Título del Anuncio</label>
                                <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Ej: Mantenimiento del Ascensor" required>
                            </div>

                            <div class="form-group mb-4">
                                <label for="contenido" class="font-weight-bold text-secondary">Contenido</label>
                                <textarea class="form-control" name="contenido" id="contenido" rows="5" placeholder="Detalles del comunicado..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" id="btnPublicar">
                                Publicar en Bitácora
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Historial de Comunicados -->
                <h6 class="font-weight-bold text-secondary mb-3">Últimos Comunicados Publicados</h6>
                <div id="listaComunicadosAdmin">
                    <div class="text-center text-muted my-4">
                        <span class="spinner-border spinner-border-sm" role="status"></span> Cargando historial...
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Gestión de Tickets (col-md-6) -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-primary font-weight-bold mb-0">Tickets de Soporte de Residentes</h5>
                            <small class="text-muted">Atienda y gestione los reportes de la comunidad</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cargarTickets()" title="Actualizar">
                            ↻
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="tablaTickets">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="border-top-0 pl-4">ID</th>
                                        <th class="border-top-0">Residente</th>
                                        <th class="border-top-0">Categoría</th>
                                        <th class="border-top-0 text-center">Estatus</th>
                                        <th class="border-top-0 text-center pr-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyTickets">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <span class="spinner-border spinner-border-sm mr-2" role="status"></span>
                                            Cargando tickets...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 3. Modal de Gestión de Ticket -->
    <div class="modal fade" id="modalTicket" tabindex="-1" aria-labelledby="modalTicketLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalTicketLabel">Gestionar Ticket</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form id="formGestionTicket">
                    <div class="modal-body pb-0">
                        <div id="modalAlert"></div>
                        
                        <!-- ID oculto del ticket -->
                        <input type="hidden" name="ticket_id" id="ticket_id">
                        
                        <div class="form-group">
                            <label for="estado_ticket" class="font-weight-bold text-secondary">Actualizar Estatus</label>
                            <select class="form-control" name="estado" id="estado_ticket" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="En Proceso">En Proceso</option>
                                <option value="Resuelto">Resuelto</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label for="respuesta_admin" class="font-weight-bold text-secondary">Respuesta Oficial (Opcional)</label>
                            <textarea class="form-control" name="respuesta_admin" id="respuesta_admin" rows="4" placeholder="Escriba la respuesta o resolución al residente..."></textarea>
                            <small class="form-text text-muted">Esta respuesta será visible para el residente que abrió el ticket.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary font-weight-bold" id="btnActualizarTicket">Actualizar Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap 4.6.2 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Lógica JavaScript (Fetch API) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const globalAlert = document.getElementById('globalAlert');

            // Inicializar las cargas de datos
            cargarBitacora();
            cargarTickets();
            // Hacer globales las funciones para los botones onclick
            window.cargarTickets = cargarTickets;
            window.abrirModalGestion = abrirModalGestion;

            // ==========================================
            // 1. PUBLICAR COMUNICADO
            // ==========================================
            const formComunicado = document.getElementById('formComunicado');
            const btnPublicar = document.getElementById('btnPublicar');
            const comunicadoAlert = document.getElementById('comunicadoAlert');

            formComunicado.addEventListener('submit', function(e) {
                e.preventDefault();
                comunicadoAlert.innerHTML = '';
                
                const originalText = btnPublicar.innerHTML;
                btnPublicar.disabled = true;
                btnPublicar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Publicando...';

                const formData = new FormData(formComunicado);
                const data = Object.fromEntries(formData.entries());

                fetch('/crm/comunidad/comunicado', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    btnPublicar.disabled = false;
                    btnPublicar.innerHTML = originalText;

                    if (response.status === 200) {
                        comunicadoAlert.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>¡Publicado!</strong> El comunicado ya está visible en la bitácora.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        formComunicado.reset();
                        cargarBitacora(); // Recargar historial automáticamente
                    } else if (response.status === 400 || response.status === 422) {
                        comunicadoAlert.innerHTML = `<div class="alert alert-danger py-2">Por favor complete todos los campos requeridos.</div>`;
                    } else {
                        comunicadoAlert.innerHTML = `<div class="alert alert-danger py-2">Error inesperado (HTTP ${response.status}).</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btnPublicar.disabled = false;
                    btnPublicar.innerHTML = originalText;
                    comunicadoAlert.innerHTML = `<div class="alert alert-danger py-2">Error de conexión con el servidor.</div>`;
                });
            });

            // ==========================================
            // 2. CARGAR HISTORIAL (BITÁCORA)
            // ==========================================
            function cargarBitacora() {
                const listaContainer = document.getElementById('listaComunicadosAdmin');
                
                fetch('/crm/comunidad/bitacora', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error de red');
                    return response.json();
                })
                .then(data => {
                    listaContainer.innerHTML = '';
                    
                    if (!data || data.length === 0) {
                        listaContainer.innerHTML = '<div class="text-muted text-center py-3">Aún no hay comunicados publicados.</div>';
                        return;
                    }

                    data.forEach(comunicado => {
                        // Construimos cada item de la lista
                        const div = document.createElement('div');
                        div.className = 'comunicado-item';
                        // Asumiendo que el JSON trae 'titulo', 'contenido' y una 'fecha'
                        const fecha = comunicado.created_at ? new Date(comunicado.created_at).toLocaleDateString() : 'Reciente';
                        
                        div.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold text-dark mb-0">${comunicado.titulo}</h6>
                                <small class="text-muted">${fecha}</small>
                            </div>
                            <p class="text-muted mb-0 small text-truncate" style="max-height: 2.5em;">${comunicado.contenido}</p>
                        `;
                        listaContainer.appendChild(div);
                    });
                })
                .catch(error => {
                    console.error('Error cargando bitácora:', error);
                    listaContainer.innerHTML = '<div class="text-danger text-center py-3">Error al cargar el historial de comunicados.</div>';
                });
            }

            // ==========================================
            // 3. CARGAR TICKETS
            // ==========================================
            function cargarTickets() {
                const tbody = document.getElementById('tbodyTickets');
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm mr-2"></span>Cargando...</td></tr>';
                
                // Endpoint hipotético según la instrucción (Asumido /crm/comunidad/tickets GET)
                fetch('/crm/comunidad/tickets', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error al obtener tickets');
                    return response.json();
                })
                .then(data => {
                    tbody.innerHTML = '';
                    
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay tickets reportados.</td></tr>';
                        return;
                    }

                    data.forEach(ticket => {
                        const tr = document.createElement('tr');
                        
                        let badgeClass = '';
                        const estado = (ticket.estado || '').toLowerCase();

                        if (estado === 'abierto') {
                            badgeClass = 'badge-warning';
                        } else if (estado === 'en proceso') {
                            badgeClass = 'badge-primary';
                        } else if (estado === 'resuelto') {
                            badgeClass = 'badge-success';
                        } else {
                            badgeClass = 'badge-secondary';
                        }

                        const badgeHtml = `<span class="badge ${badgeClass} px-2 py-1">${ticket.estado || 'Desconocido'}</span>`;
                        // Info del residente, asumiendo que el JOIN trae un 'apto' o 'nombre_residente'
                        const residenteInfo = ticket.apartamento ? `Apto ${ticket.apartamento}` : (ticket.residente || `Usuario #${ticket.usuario_id}`);

                        tr.innerHTML = `
                            <td class="align-middle pl-4 font-weight-bold">#${ticket.id}</td>
                            <td class="align-middle text-dark">${residenteInfo}</td>
                            <td class="align-middle">${ticket.categoria || '-'}</td>
                            <td class="align-middle text-center">${badgeHtml}</td>
                            <td class="align-middle text-center pr-4">
                                <button class="btn btn-sm btn-outline-info font-weight-bold shadow-sm" onclick="abrirModalGestion(${ticket.id})">Gestionar</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    console.error('Error cargando tickets:', error);
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar tickets. Intente actualizar.</td></tr>';
                });
            }

            // ==========================================
            // 4. ACTUALIZAR TICKET
            // ==========================================
            function abrirModalGestion(ticketId) {
                document.getElementById('ticket_id').value = ticketId;
                document.getElementById('formGestionTicket').reset();
                document.getElementById('modalAlert').innerHTML = '';
                $('#modalTicket').modal('show');
            }

            const formGestionTicket = document.getElementById('formGestionTicket');
            const btnActualizarTicket = document.getElementById('btnActualizarTicket');
            const modalAlert = document.getElementById('modalAlert');

            formGestionTicket.addEventListener('submit', function(e) {
                e.preventDefault();
                modalAlert.innerHTML = '';

                const originalText = btnActualizarTicket.innerHTML;
                btnActualizarTicket.disabled = true;
                btnActualizarTicket.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Actualizando...';

                const formData = new FormData(formGestionTicket);
                const data = Object.fromEntries(formData.entries());

                fetch('/crm/comunidad/ticket/gestionar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    btnActualizarTicket.disabled = false;
                    btnActualizarTicket.innerHTML = originalText;

                    if (response.status === 200) {
                        $('#modalTicket').modal('hide');
                        globalAlert.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <strong>Ticket Actualizado:</strong> El estatus y la respuesta fueron registrados.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        cargarTickets(); // Recargar tabla automáticamente
                    } else if (response.status === 400 || response.status === 422) {
                        modalAlert.innerHTML = `<div class="alert alert-danger py-2">Faltan datos requeridos.</div>`;
                    } else {
                        modalAlert.innerHTML = `<div class="alert alert-danger py-2">Ocurrió un error en el servidor (HTTP ${response.status}).</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error actualizando ticket:', error);
                    btnActualizarTicket.disabled = false;
                    btnActualizarTicket.innerHTML = originalText;
                    modalAlert.innerHTML = `<div class="alert alert-danger py-2">Error de conexión.</div>`;
                });
            });

            // ==========================================
            // 5. LOGOUT
            // ==========================================
            const btnLogout = document.getElementById('btnLogout');
            btnLogout.addEventListener('click', function() {
                btnLogout.disabled = true;
                btnLogout.innerHTML = 'Saliendo...';

                fetch('/auth/logout', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(() => {
                    window.location.href = '/auth/login';
                })
                .catch(error => {
                    window.location.href = '/auth/login';
                });
            });

        });
    </script>
</body>
</html>
