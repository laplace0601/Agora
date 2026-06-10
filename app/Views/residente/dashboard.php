<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Residente - Ágora CRM</title>
    <!-- Bootstrap 4.6.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
    </style>
</head>
<body>

    <!-- 1. Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="#">Ágora CRM</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResidente" aria-controls="navbarResidente" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarResidente">
                <span class="navbar-text text-light mr-auto">
                    Bienvenido, Residente
                </span>
                <button class="btn btn-outline-danger bg-white font-weight-bold mt-2 mt-lg-0" id="btnLogout">
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <!-- 2. Contenido Principal -->
    <div class="container">
        <!-- Alerta Global para Feedback -->
        <div id="globalAlert"></div>

        <div class="row">
            <!-- Card 1: Estado de Cuenta -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="text-primary font-weight-bold mb-0">Mi Estado de Cuenta</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tablaRecibos">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-top-0 pl-4">Mes/Año</th>
                                        <th class="border-top-0">Monto Base</th>
                                        <th class="border-top-0">Monto Total</th>
                                        <th class="border-top-0">Estado</th>
                                        <th class="border-top-0 pr-4 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyRecibos">
                                    <!-- Datos inyectados por Fetch -->
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                            Cargando sus recibos...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Solvencia -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100 text-center">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="text-primary font-weight-bold">Solvencia</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center pb-5">
                        <div class="mb-4 text-muted">
                            <svg width="4em" height="4em" viewBox="0 0 16 16" class="bi bi-shield-check text-success mb-3" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M5.443 1.991a30.173 30.173 0 0 1 2.557-.506.08.08 0 0 1 .044.013v11.956a12.025 12.025 0 0 0 5.304-4.814c.783-1.42 1.258-3.087 1.258-4.993V3.125a.08.08 0 0 0-.08-.08c-1.63-.035-3.238-.346-4.78-1.011A.08.08 0 0 0 8 2.001V2zM7.956 1.98A.08.08 0 0 1 8 2v11.956c-2.484-.46-4.63-1.92-5.748-4.227A11.97 11.97 0 0 1 1 4.733V3.125a.08.08 0 0 1 .08-.08c1.63-.035 3.238-.346 4.78-1.011a.08.08 0 0 1 .096.012z"/>
                              <path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                            </svg>
                            <p class="mb-0">Verifique su estado actual y descargue su certificado digital de solvencia.</p>
                        </div>
                        <button class="btn btn-success btn-lg btn-block font-weight-bold shadow-sm" id="btnSolvencia">
                            Descargar Solvencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Modal de Pago -->
    <div class="modal fade" id="modalPagar" tabindex="-1" aria-labelledby="modalPagarLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalPagarLabel">Registrar Pago de Recibo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form id="formPago">
                    <div class="modal-body pb-0">
                        <!-- Alerta interna del Modal -->
                        <div id="modalAlert"></div>
                        
                        <!-- ID oculto del recibo -->
                        <input type="hidden" name="recibo_mensual_id" id="recibo_mensual_id">
                        
                        <div class="form-group">
                            <label for="metodo_pago" class="font-weight-bold text-secondary">Método de Pago</label>
                            <select class="form-control" name="metodo_pago" id="metodo_pago" required>
                                <option value="" disabled selected>Seleccione una opción...</option>
                                <option value="Pago Movil">Pago Móvil</option>
                                <option value="Transferencia">Transferencia Bancaria</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="monto_pagado" class="font-weight-bold text-secondary">Monto Pagado</label>
                                <input type="number" class="form-control" name="monto_pagado" id="monto_pagado" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="referencia_transaccion" class="font-weight-bold text-secondary">Referencia (Últimos 4)</label>
                                <input type="text" class="form-control" name="referencia_transaccion" id="referencia_transaccion" maxlength="4" placeholder="Ej: 1234" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="comprobante_url" class="font-weight-bold text-secondary">URL del Comprobante</label>
                            <input type="text" class="form-control" name="comprobante_url" id="comprobante_url" placeholder="Ej: https://img.domain.com/foto1.jpg" required>
                            <small class="form-text text-muted">Pegue el enlace a la imagen del comprobante.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary font-weight-bold" id="btnProcesarPago">Registrar Pago</button>
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
            
            // 1. CARGAR RECIBOS AL INICIAR LA PÁGINA
            cargarRecibos();

            const globalAlert = document.getElementById('globalAlert');

            function cargarRecibos() {
                const tbody = document.getElementById('tbodyRecibos');
                
                fetch('/crm/finanzas/mis-recibos', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error de red al obtener recibos');
                    return response.json();
                })
                .then(data => {
                    tbody.innerHTML = ''; // Limpiamos tabla
                    
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay recibos registrados en su estado de cuenta.</td></tr>';
                        return;
                    }

                    // Iteramos JSON e inyectamos filas
                    data.forEach(recibo => {
                        const tr = document.createElement('tr');
                        
                        let badgeHtml = '';
                        let accionHtml = '';
                        const estado = (recibo.estado || '').toLowerCase();

                        if (estado === 'pagado') {
                            badgeHtml = '<span class="badge badge-success px-2 py-1">Pagado</span>';
                            accionHtml = '<span class="text-muted"><small>No requiere acción</small></span>';
                        } else if (estado === 'pendiente') {
                            badgeHtml = '<span class="badge badge-danger px-2 py-1">Pendiente</span>';
                            accionHtml = `<button type="button" class="btn btn-sm btn-outline-primary btn-pagar px-3" data-id="${recibo.id}">Pagar</button>`;
                        } else {
                            // En proceso, en revisión, etc.
                            badgeHtml = `<span class="badge badge-warning px-2 py-1 text-white">${recibo.estado}</span>`;
                            accionHtml = '<span class="text-muted"><small>Validando...</small></span>';
                        }

                        tr.innerHTML = `
                            <td class="align-middle pl-4">${recibo.mes || '-'} / ${recibo.anio || '-'}</td>
                            <td class="align-middle">${recibo.monto_base || '0.00'}</td>
                            <td class="align-middle font-weight-bold text-dark">${recibo.monto_total || '0.00'}</td>
                            <td class="align-middle">${badgeHtml}</td>
                            <td class="align-middle pr-4 text-center">${accionHtml}</td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // Añadir evento a los botones 'Pagar' dinámicos
                    document.querySelectorAll('.btn-pagar').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const reciboId = this.getAttribute('data-id');
                            abrirModalPago(reciboId);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error cargando recibos:', error);
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Ocurrió un error al cargar su estado de cuenta. Intente recargar.</td></tr>';
                });
            }

            // ABRIR MODAL DE PAGO
            function abrirModalPago(reciboId) {
                // Seteamos el input hidden
                document.getElementById('recibo_mensual_id').value = reciboId;
                
                // Reseteamos el formulario y las alertas
                document.getElementById('formPago').reset();
                document.getElementById('modalAlert').innerHTML = '';
                
                // Abrimos el modal con jQuery (Bootstrap 4 req)
                $('#modalPagar').modal('show');
            }

            // 2. REGISTRAR PAGO
            const formPago = document.getElementById('formPago');
            const btnProcesarPago = document.getElementById('btnProcesarPago');
            const modalAlert = document.getElementById('modalAlert');

            formPago.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevenir recarga
                modalAlert.innerHTML = '';

                // Botón en estado de carga
                const originalBtnText = btnProcesarPago.innerHTML;
                btnProcesarPago.disabled = true;
                btnProcesarPago.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...';

                // Armar FormData
                const formData = new FormData(formPago);
                const data = Object.fromEntries(formData.entries());

                fetch('/crm/finanzas/pagar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    // Restaurar botón
                    btnProcesarPago.disabled = false;
                    btnProcesarPago.innerHTML = originalBtnText;

                    if (response.status === 200) {
                        // Éxito: Cerrar modal y mostrar alerta global
                        $('#modalPagar').modal('hide');
                        globalAlert.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <strong>¡Pago Registrado!</strong> Su pago ha sido enviado y se encuentra en revisión.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        // Recargar la tabla automáticamente
                        cargarRecibos();
                    } else if (response.status === 400 || response.status === 422) {
                        modalAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Campos obligatorios faltantes o inválidos.</div>`;
                    } else if (response.status === 409) {
                        modalAlert.innerHTML = `<div class="alert alert-warning py-2" role="alert">No solvente o límite de intentos alcanzado.</div>`;
                    } else {
                        modalAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Ocurrió un error (HTTP ${response.status}). Intente de nuevo.</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error al registrar pago:', error);
                    btnProcesarPago.disabled = false;
                    btnProcesarPago.innerHTML = originalBtnText;
                    modalAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Error de conexión con el servidor.</div>`;
                });
            });

            // 3. LOGOUT
            const btnLogout = document.getElementById('btnLogout');
            btnLogout.addEventListener('click', function() {
                // Feedback visual
                btnLogout.disabled = true;
                btnLogout.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                fetch('/auth/logout', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(() => {
                    // Independientemente de la respuesta, redirigir al login
                    window.location.href = '/auth/login';
                })
                .catch(error => {
                    console.error('Error en logout:', error);
                    window.location.href = '/auth/login';
                });
            });

        });
    </script>
</body>
</html>
