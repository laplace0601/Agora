<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
// Ruta para mostrar el formulario (GET)
$routes->get('auth/login', 'AuthController::login');

// Ruta para procesar el formulario (POST)
$routes->post('auth/procesar-login', 'AuthController::processLogin');

// ---------------------------------------------------------------
// Autenticación y Paneles por Rol
// ---------------------------------------------------------------
$routes->get('auth/logout', 'AuthController::logout');

// Rutas de Paneles
$routes->get('super/panel', 'SuperController::panel');
$routes->get('admin/comunidad', 'AdminController::comunidad');
$routes->get('admin/finanzas', 'AdminController::finanzas');
$routes->get('residente/dashboard', 'ResidenteController::dashboard');

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
