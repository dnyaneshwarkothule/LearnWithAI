<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/login', 'Auth::login');
$routes->get('api/profile', 'Auth::profile');

// Protected routes
$routes->group('', ['filter' => 'jwtAuth'], function($routes) {
    $routes->get('exams', 'Exam::index');
    $routes->get('exams/(:num)', 'Exam::show/$1');
});


