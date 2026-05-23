<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Route untuk Landing Page (Hanya bisa diakses jika belum login)
$routes->get('/', 'Home::index', ['filter' => 'guest']);

// Route Group untuk Admin (Hanya role admin yang bisa akses semua URL yang berawalan /admin)
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    // dashboard
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('dashboard', 'AdminController::dashboard');

    // Layanan
    $routes->get('layanan', 'AdminController::layananIndex');
    $routes->get('layanan/create', 'AdminController::layananCreate');
    $routes->post('layanan/store', 'AdminController::layananStore');
    $routes->get('layanan/edit/(:num)', 'AdminController::layananEdit/$1');
    $routes->post('layanan/update/(:num)', 'AdminController::layananUpdate/$1');
    $routes->get('layanan/delete/(:num)', 'AdminController::layananDelete/$1');
    $routes->post('layanan/toggle/(:num)', 'AdminController::layananToggle/$1');

    // Jadwal
    $routes->get('jadwal', 'AdminController::jadwalIndex');
    $routes->get('jadwal/create', 'AdminController::jadwalCreate');
    $routes->post('jadwal/store', 'AdminController::jadwalStore');
    $routes->get('jadwal/edit/(:num)', 'AdminController::jadwalEdit/$1');
    $routes->post('jadwal/update/(:num)', 'AdminController::jadwalUpdate/$1');
    $routes->get('jadwal/delete/(:num)', 'AdminController::jadwalDelete/$1');

    // Booking
    $routes->get('booking', 'AdminController::bookingIndex');
    $routes->get('booking/(:num)', 'AdminController::bookingShow/$1');
    $routes->post('booking/konfirmasi/(:num)', 'AdminController::bookingKonfirmasi/$1');
    $routes->post('booking/tolak/(:num)', 'AdminController::bookingTolak/$1');
    $routes->post('booking/assign-staff/(:num)', 'AdminController::bookingAssignStaff/$1');

    // Staff / Teknisi
    $routes->get('staff', 'AdminController::staffIndex');
    $routes->get('staff/create', 'AdminController::staffCreate');
    $routes->post('staff/store', 'AdminController::staffStore');
    $routes->get('staff/edit/(:num)', 'AdminController::staffEdit/$1');
    $routes->post('staff/update/(:num)', 'AdminController::staffUpdate/$1');
    $routes->get('staff/delete/(:num)', 'AdminController::staffDelete/$1');

    // User
    $routes->get('users', 'AdminController::usersIndex');
    $routes->post('users/toggle/(:num)', 'AdminController::usersToggle/$1');
    $routes->get('users/delete/(:num)', 'AdminController::usersDelete/$1');
});

// Route Group untuk Staff
$routes->group('staff', ['filter' => 'auth:staff'], static function ($routes) {
    $routes->get('/', 'StaffController::index');
    $routes->get('jadwal-tugas-harian', 'StaffJadwalController::index');
    $routes->get('riwayat-pekerjaan', 'StaffRiwayatController::index');
});

// Route Group untuk Pelanggan (User)
$routes->group('user', ['filter' => 'auth:pelanggan'], static function ($routes) {
    $routes->get('/', 'UserController::index');
});

// Route untuk autentikasi (Login & Register) - Dilindungi oleh GuestFilter
$routes->get('/login', 'AuthController::login', ['filter' => 'guest']);
$routes->post('/login/process', 'AuthController::processLogin', ['filter' => 'guest']);
$routes->get('/register', 'AuthController::register', ['filter' => 'guest']);
$routes->post('/register/process', 'AuthController::processRegister', ['filter' => 'guest']);
$routes->get('/logout', 'AuthController::logout');
