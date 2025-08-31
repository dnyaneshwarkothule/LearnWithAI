<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
//$routes->post('api/login', 'Auth::login');
//$routes->get('api/profile', 'Auth::profile');

$routes->post('api/login', 'AuthController::login');               // 🚀 User Login
$routes->post('api/register', 'AuthController::register');         // 🚀 User Registration

// Protected routes
$routes->group('', ['filter' => 'jwtAuth'], function($routes) {
    $routes->get('api/exams', 'Exam::index');
    $routes->get('api/exams/(:num)', 'Exam::show/$1');

    $routes->get('api/exam-questions', 'ExamQuestion::index');
    $routes->post('api/exam-result/submit', 'ExamResult::submit');

    $routes->put('api/profile/(:num)', 'AuthController::updateProfile/$1'); // 🚀 Update Profile by userId
    $routes->get('api/profile/(:num)', 'AuthController::getProfile/$1');    // 🚀 Get Profile by userId
});
