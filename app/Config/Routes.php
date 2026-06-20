<?php

use CodeIgniter\Router\RouteCollection;
// porfa no me toquen esto U-U

/**
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------
// Rutas Públicas (sin autenticación)
// ---------------------------------------------------------------

$routes->get('/', function () {
    return view('landing/landing'); // carga la landing page principal
});

// Login — GET muestra el formulario, POST lo procesa
$routes->get('login',              'AuthController::login');
$routes->get('auth/login',         'AuthController::login');
$routes->post('auth/procesar-login', 'AuthController::processLogin');
$routes->get('auth/logout',        'AuthController::logout');

// ---------------------------------------------------------------
// Panel Super-Admin (rol: root)
// ---------------------------------------------------------------
$routes->group('super', ['filter' => 'auth:root'], function ($routes) {
    $routes->get('panel', 'SuperController::panel');
});

// ---------------------------------------------------------------
// Panel Admin (rol: admin) — Inmuebles y Apartamentos
// ---------------------------------------------------------------
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {

    // Vistas principales
    $routes->get('apartamentos',          'AdminController::apartamentos');
    $routes->get('finanzas',              'AdminController::finanzas');
    $routes->get('finanzas/cobro',        'AdminController::finanzasCobro');
    $routes->get('finanzas/pagos',        'AdminController::finanzasPagos');
    $routes->get('cartelera',             'AdminController::cartelera');
    $routes->get('comunidad',             'AdminController::comunidad');

    // Handlers POST — formularios de las vistas admin
    $routes->post('apartamentos/registrar-condominio',  'AdminController::registrarCondominio');
    $routes->post('apartamentos/registrar-apartamento', 'AdminController::registrarApartamento');
    $routes->post('finanzas/facturar',                  'AdminController::emitirRecibos');
    $routes->post('finanzas/validar-pago',              'AdminController::validarPago');
    $routes->post('cartelera/publicar',                 'AdminController::publicarAnuncio');
    $routes->get('cartelera/eliminar/(:num)',            'AdminController::eliminarAnuncio/$1');
});

// ---------------------------------------------------------------
// Panel Residente (rol: residente)
// ---------------------------------------------------------------
$routes->group('residente', ['filter' => 'auth:residente'], function ($routes) {
    $routes->get('dashboard',     'ResidenteController::dashboard');
    $routes->get('pago',          'ResidenteController::reportarPago');
    $routes->post('pago/enviar',  'ResidenteController::enviarPago');
    $routes->get('soporte',       'ResidenteController::soporte');
    $routes->post('soporte/abrir', 'ResidenteController::abrirTicket');
});

// ---------------------------------------------------------------
// CRM — API JSON (endpoints consumidos por el frontend vía fetch/AJAX)
// Protegidos por el filtro auth global definido en Config/Filters.php
// ---------------------------------------------------------------

// Gestión de Apartamentos
$routes->post('crm/apartamentos/registrar', 'LicenciaController::registrarApartamento');

// Finanzas
$routes->post('crm/finanzas/facturar',         'FinanzasController::emitirRecibos');
$routes->post('crm/finanzas/pagar',            'FinanzasController::registrarPago');
$routes->post('crm/finanzas/validar-pago',     'FinanzasController::validarPago');
$routes->get('crm/finanzas/solvencia/(:num)',  'FinanzasController::procesarSolvencia/$1');
$routes->get('crm/finanzas/pagos-pendientes',  'FinanzasController::listarPagosPendientes');

// Comunidad
$routes->post('crm/comunidad/comunicado',         'ComunidadController::crearComunicado');
$routes->get('crm/comunidad/bitacora',            'ComunidadController::listarComunicados');
$routes->post('crm/comunidad/ticket',             'ComunidadController::abrirTicket');
$routes->post('crm/comunidad/ticket/gestionar',   'ComunidadController::responderTicket');
