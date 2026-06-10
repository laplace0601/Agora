<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro Financiero - Ágora CRM</title>
    <!-- Bootstrap 4.6.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        /* Pequeño ajuste para botones de acción en tabla */
        .btn-accion { padding: 0.2rem 0.5rem; font-size: 0.875rem; }
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
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-outline-info text-white mr-2 mt-1" href="<?= site_url('admin/comunidad') ?>">Volver a Comunidad</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link font-weight-bold" href="<?= site_url('admin/finanzas') ?>">Finanzas <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
                <span class="navbar-text text-light mr-3">
                    Hola, Administrador
                </span>
                <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-danger mt-2 mt-lg-0 font-weight-bold" id="btnLogout">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- 2. Layout Principal -->
    <div class="container-fluid px-md-5">
        <!-- Alerta Global -->
        <div id="globalAlert"></div>

        <div class="row">
            
            <!-- COLUMNA IZQUIERDA: Facturación (col-md-5) -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                        <h5 class="text-primary font-weight-bold mb-0">Emisión de Recibos Mensuales</h5>
                        <small class="text-muted">Genera los cobros para todos los apartamentos</small>
                    </div>
                    <div class="card-body">
                        <!-- Alerta Local del Formulario -->
                        <div id="facturacionAlert"></div>

                        <form id="formFacturar">
                            <div class="form-group">
                                <label for="condominio_id" class="font-weight-bold text-secondary">ID Condominio</label>
                                <input type="number" class="form-control" name="condominio_id" id="condominio_id" min="1" value="1" required>
                                <small class="form-text text-muted">Temporalmente un número. Luego será un selector dinámico.</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="mes" class="font-weight-bold text-secondary">Mes</label>
                                    <select class="form-control" name="mes" id="mes" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="Enero">Enero</option>
                                        <option value="Febrero">Febrero</option>
                                        <option value="Marzo">Marzo</option>
                                        <option value="Abril">Abril</option>
                                        <option value="Mayo">Mayo</option>
                                        <option value="Junio">Junio</option>
                                        <option value="Julio">Julio</option>
                                        <option value="Agosto">Agosto</option>
                                        <option value="Septiembre">Septiembre</option>
                                        <option value="Octubre">Octubre</option>
                                        <option value="Noviembre">Noviembre</option>
                                        <option value="Diciembre">Diciembre</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="anio" class="font-weight-bold text-secondary">Año</label>
                                    <input type="number" class="form-control" name="anio" id="anio" min="2020" max="2100" value="2026" required>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="monto_base" class="font-weight-bold text-secondary">Monto Base a Dividir (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control form-control-lg" name="monto_base" id="monto_base" step="0.01" min="0" placeholder="Ej: 1500.00" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm" id="btnFacturar">
                                Generar Facturación Masiva
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Conciliación (col-md-7) -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-primary font-weight-bold mb-0">Pagos por Validar</h5>
                            <small class="text-muted">Revise las transferencias y apruebe para otorgar solvencia</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cargarPagosPendientes()" title="Actualizar">
                            ↻
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <!-- Alerta Local de Validación -->
                        <div id="validacionAlert" class="px-3 pt-2"></div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="tablaPagosPendientes">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="border-top-0 pl-4">Apto / Recibo</th>
                                        <th class="border-top-0 text-center">Ref. Trans.</th>
                                        <th class="border-top-0 text-right">Monto</th>
                                        <th class="border-top-0 text-center">Comprobante</th>
                                        <th class="border-top-0 text-center pr-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPagosPendientes">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                            Cargando pagos pendientes...
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

    <!-- Scripts Bootstrap 4.6.2 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Lógica JavaScript (Fetch API) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const globalAlert = document.getElementById('globalAlert');
            const facturacionAlert = document.getElementById('facturacionAlert');
            const validacionAlert = document.getElementById('validacionAlert');

            // Inicializar carga de tabla
            cargarPagosPendientes();
            // Hacer global la función para que funcione el botón de recarga (↻)
            window.cargarPagosPendientes = cargarPagosPendientes;

            // ==========================================
            // 1. LÓGICA DE FACTURACIÓN
            // ==========================================
            const formFacturar = document.getElementById('formFacturar');
            const btnFacturar = document.getElementById('btnFacturar');

            formFacturar.addEventListener('submit', function(e) {
                e.preventDefault();
                facturacionAlert.innerHTML = '';
                
                // Deshabilitar botón
                const originalText = btnFacturar.innerHTML;
                btnFacturar.disabled = true;
                btnFacturar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

                const formData = new FormData(formFacturar);
                const data = Object.fromEntries(formData.entries());

                fetch('/crm/finanzas/facturar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    btnFacturar.disabled = false;
                    btnFacturar.innerHTML = originalText;

                    if (response.status === 200) {
                        facturacionAlert.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <strong>¡Éxito!</strong> Recibos generados para todos los residentes.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        formFacturar.reset();
                        // Opcional: restaurar valores por defecto como año
                        document.getElementById('anio').value = new Date().getFullYear();
                        document.getElementById('condominio_id').value = 1;
                    } else if (response.status === 400 || response.status === 422) {
                        facturacionAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Faltan campos obligatorios.</div>`;
                    } else {
                        facturacionAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Error del servidor al facturar.</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btnFacturar.disabled = false;
                    btnFacturar.innerHTML = originalText;
                    facturacionAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Error de conexión.</div>`;
                });
            });

            // ==========================================
            // 2. CARGAR PAGOS PENDIENTES
            // ==========================================
            function cargarPagosPendientes() {
                const tbody = document.getElementById('tbodyPagosPendientes');
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm mr-2" role="status"></span>Cargando...</td></tr>';
                
                fetch('/crm/finanzas/pagos-pendientes', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la petición');
                    return response.json();
                })
                .then(data => {
                    tbody.innerHTML = '';
                    
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No hay pagos pendientes de validación en este momento.</td></tr>';
                        return;
                    }

                    data.forEach(pago => {
                        const tr = document.createElement('tr');
                        
                        // Formatear datos usando lo que trae el JOIN del backend (pago.apartamento, pago.mes, etc.)
                        const aptoInfo = pago.apartamento ? `Apto ${pago.apartamento}` : `Recibo #${pago.recibo_mensual_id}`;
                        const mesInfo = (pago.mes && pago.anio) ? `(${pago.mes} ${pago.anio})` : '';
                        
                        // Comprobante como enlace
                        const urlComprobante = pago.comprobante_url ? pago.comprobante_url : '#';
                        const enlaceComprobante = `<a href="${urlComprobante}" target="_blank" class="btn btn-sm btn-outline-info btn-accion">Ver Foto</a>`;

                        tr.innerHTML = `
                            <td class="align-middle pl-4">
                                <div class="font-weight-bold text-dark">${aptoInfo}</div>
                                <small class="text-muted">${mesInfo}</small>
                            </td>
                            <td class="align-middle text-center font-weight-bold text-secondary">${pago.referencia_transaccion || '-'}</td>
                            <td class="align-middle text-right font-weight-bold">$${pago.monto_pagado || '0.00'}</td>
                            <td class="align-middle text-center">${enlaceComprobante}</td>
                            <td class="align-middle text-center pr-4">
                                <button class="btn btn-success btn-accion shadow-sm mr-1" onclick="validarPago(${pago.id}, 'aprobar')">✓ Aprobar</button>
                                <button class="btn btn-danger btn-accion shadow-sm" onclick="validarPago(${pago.id}, 'rechazar')">✗ Rechazar</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    console.error('Error cargando pagos:', error);
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error al obtener los pagos. Intente actualizar.</td></tr>';
                });
            }

            // ==========================================
            // 3. VALIDAR PAGO (Aprobar o Rechazar)
            // ==========================================
            window.validarPago = function(pagoId, accion) {
                // Confirmación simple para evitar clics accidentales
                if (!confirm(`¿Está seguro que desea ${accion.toUpperCase()} este pago?`)) return;

                validacionAlert.innerHTML = '';
                
                fetch('/crm/finanzas/validar-pago', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pago_id: pagoId,
                        accion: accion
                    })
                })
                .then(response => {
                    if (response.status === 200) {
                        const alertClass = accion === 'aprobar' ? 'alert-success' : 'alert-warning';
                        const actionText = accion === 'aprobar' ? 'aprobado y la solvencia otorgada' : 'rechazado';
                        
                        validacionAlert.innerHTML = `
                            <div class="alert ${alertClass} alert-dismissible fade show shadow-sm" role="alert">
                                <strong>Éxito:</strong> El pago ha sido ${actionText}.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        // Recargar tabla después de accionar
                        cargarPagosPendientes();
                    } else {
                        validacionAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Error al procesar la validación.</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    validacionAlert.innerHTML = `<div class="alert alert-danger py-2" role="alert">Error de red. Intente nuevamente.</div>`;
                });
            };



        });
    </script>
</body>
</html>
