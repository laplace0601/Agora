<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login/process', 'Auth::processLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index'); //controlador protegido