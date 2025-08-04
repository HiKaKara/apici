<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Grup untuk semua rute API agar lebih terorganisir
$routes->group('api', ['namespace' => 'App\Controllers\api'], function ($routes) {
    
    // --- Rute Publik & Pegawai ---
    $routes->post('auth/login', 'Auth::login');

    $routes->get('users', 'Users::index');
    $routes->get('users/(:num)', 'Users::show/$1');
    $routes->post('users/upload/(:num)', 'Users::uploadProfilePicture/$1');

    $routes->post('attendance/checkin', 'AttendanceController::checkin');
    $routes->post('attendance/checkout', 'AttendanceController::checkout');
    $routes->get('attendance/history/(:num)', 'AttendanceController::history/$1');
    $routes->post('attendance/validate-wfo-ip', 'AttendanceController::validateWfoIp');

    $routes->post('overtime/submit', 'OvertimeController::submit');
    $routes->get('overtime/history/(:num)', 'OvertimeController::history/$1');

    // --- Grup Khusus untuk Rute Admin ---
    $routes->group('admin', function ($routes) {
        // Rute untuk mengelola data pegawai (Employees)
        $routes->get('employees', 'AdminController::getAllEmployees');
        $routes->post('employees', 'AdminController::createEmployee');
        $routes->put('employees/(:num)', 'AdminController::updateEmployee/$1');
        // Catatan: Rute 'update_role' dihapus karena sudah ditangani oleh 'updateEmployee'.

        // Rute untuk dasbor dan riwayat
        $routes->get('dashboard-summary', 'AdminController::dashboardSummary');
        $routes->get('attendance-history', 'AdminController::getAllAttendanceHistory');
        $routes->get('overtime-history', 'AdminController::getAllOvertimeHistory');
        
        // Rute untuk mengelola pengajuan lembur
        $routes->put('overtime/status/(:num)', 'AdminController::updateOvertimeStatus/$1');
    });

});
