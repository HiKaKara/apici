<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ====================================================================
// GANTI SELURUH BLOK 'api' ANDA DENGAN YANG DI BAWAH INI
// ====================================================================
$routes->group('api', function ($routes) {

    $routes->post('auth/login', 'Api\Auth::login');
    $routes->post('users/upload/(:num)', 'Api\Users::uploadProfilePicture/$1');
    $routes->resource('users', ['controller' => 'Api\Users']);

    $routes->get('attendance/history/(:num)', 'Api\Attendance::history/$1');
    $routes->post('attendance/checkin', 'Api\Attendance::checkin');
    $routes->post('attendance/checkout', 'Api\Attendance::checkout');

    // Route baru untuk lembur
    $routes->post('overtime/submit', 'Api\OvertimeController::submit');
    $routes->get('overtime/history/(:num)', 'Api\OvertimeController::history/$1');
    $routes->post('attendance/validate-wfo-ip', 'Api\Attendance::validateWfoIp');
});

// app/Config/Routes.php
$routes->group('api/admin', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Rute untuk data pegawai
    $routes->get('employees', 'AdminController::getAllEmployees');
    $routes->put('employees/update_role/(:num)', 'AdminController::updateUserRole/$1');
    
    // Rute untuk riwayat presensi
    $routes->get('history/attendance', 'AdminController::getAttendanceHistory');
});
