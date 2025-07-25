<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Grup untuk semua rute API agar lebih terorganisir
$routes->group('api', function ($routes) {
    // Rute Auth
    $routes->post('auth/login', 'api\Auth::login');

    // Rute Users
    $routes->get('users', 'api\Users::index');
    $routes->get('users/(:num)', 'api\Users::show/$1');
    $routes->post('users/upload/(:num)', 'api\Users::uploadProfilePicture/$1');

    // Rute Attendance
    $routes->post('attendance/checkin', 'api\Attendance::checkin');
    $routes->post('attendance/checkout', 'api\Attendance::checkout');
    $routes->get('attendance/history/(:num)', 'api\Attendance::history/$1');
    
    // PERBAIKAN UTAMA: Pastikan rute ini menggunakan metode POST
    $routes->post('attendance/validate-wfo-ip', 'api\Attendance::validateWfoIp');

    // Rute Overtime
    $routes->post('overtime/submit', 'api\OvertimeController::submit');
    $routes->get('overtime/history/(:num)', 'api\OvertimeController::history/$1');

    // Rute Admin
    $routes->get('admin/employees', 'api\AdminController::getAllEmployees');
    $routes->put('admin/employees/update_role/(:num)', 'api\AdminController::updateUserRole/$1');
    $routes->get('admin/dashboard-summary', 'api\AdminController::dashboardSummary');
});
