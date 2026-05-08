<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Route untuk Landing Page (Hanya bisa diakses jika belum login)
$routes->get('/', 'Home::index', ['filter' => 'guest']);

// Route Group untuk Admin (Hanya role admin yang bisa akses semua URL yang berawalan /admin)
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->get('/', 'AdminController::index');
    // Tambahkan route admin lainnya di sini nanti, otomatis terlindungi!
});

// Route Group untuk Staff
$routes->group('staff', ['filter' => 'auth:staff'], static function ($routes) {
    $routes->get('/', 'StaffController::index');
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
