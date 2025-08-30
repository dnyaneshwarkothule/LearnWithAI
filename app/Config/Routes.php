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
    $routes->get('api/exams', 'Exam::index');
    $routes->get('api/exams/(:num)', 'Exam::show/$1');

    $routes->get('api/exam-questions', 'ExamQuestion::index');
    $routes->post('api/exam-result/submit', 'ExamResult::submit');
});
