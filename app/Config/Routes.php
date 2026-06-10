<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');

// ---------------------------------------------------------------
// Autenticación
// ---------------------------------------------------------------
$routes->post('auth/login', 'AuthController::processLogin');
$routes->get('auth/logout', 'AuthController::logout');

// ---------------------------------------------------------------
// CRM — Gestión de Apartamentos (requiere rol admin en sesión)
// ---------------------------------------------------------------
$routes->post('crm/apartamentos/registrar', 'LicenciaController::registrarApartamento');

// ---------------------------------------------------------------
// CRM — Finanzas (facturación, pagos, solvencia)
// ---------------------------------------------------------------
$routes->post('crm/finanzas/facturar', 'FinanzasController::emitirRecibos');
$routes->post('crm/finanzas/pagar', 'FinanzasController::registrarPago');
$routes->post('crm/finanzas/validar-pago', 'FinanzasController::validarPago');
$routes->get('crm/finanzas/solvencia/(:num)', 'FinanzasController::procesarSolvencia/$1');

// ---------------------------------------------------------------
// CRM — Comunidad (bitácora, tickets de soporte)
// ---------------------------------------------------------------
$routes->post('crm/comunidad/comunicado', 'ComunidadController::crearComunicado');
$routes->get('crm/comunidad/bitacora', 'ComunidadController::listarComunicados');
$routes->post('crm/comunidad/ticket', 'ComunidadController::abrirTicket');
$routes->post('crm/comunidad/ticket/gestionar', 'ComunidadController::responderTicket');
