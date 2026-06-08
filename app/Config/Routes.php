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
    $routes->get('/', 'AdminDashboardController::dashboard');
    $routes->get('dashboard', 'AdminDashboardController::dashboard');

    // Layanan
    $routes->get('layanan', 'AdminLayananController::layananIndex');
    $routes->get('layanan/create', 'AdminLayananController::layananCreate');
    $routes->post('layanan/store', 'AdminLayananController::layananStore');
    $routes->get('layanan/edit/(:num)', 'AdminLayananController::layananEdit/$1');
    $routes->post('layanan/update/(:num)', 'AdminLayananController::layananUpdate/$1');
    $routes->get('layanan/delete/(:num)', 'AdminLayananController::layananDelete/$1');
    $routes->post('layanan/toggle/(:num)', 'AdminLayananController::layananToggle/$1');

    // Jadwal
    $routes->get('jadwal', 'AdminJadwalController::jadwalIndex');
    $routes->get('jadwal/create', 'AdminJadwalController::jadwalCreate');
    $routes->post('jadwal/store', 'AdminJadwalController::jadwalStore');
    $routes->get('jadwal/edit/(:num)', 'AdminJadwalController::jadwalEdit/$1');
    $routes->post('jadwal/update/(:num)', 'AdminJadwalController::jadwalUpdate/$1');
    $routes->get('jadwal/delete/(:num)', 'AdminJadwalController::jadwalDelete/$1');

    // Booking
    $routes->get('booking', 'AdminBookingController::bookingIndex');
    $routes->get('booking/(:num)', 'AdminBookingController::bookingShow/$1');
    $routes->post('booking/konfirmasi/(:num)', 'AdminBookingController::bookingKonfirmasi/$1');
    $routes->post('booking/tolak/(:num)', 'AdminBookingController::bookingTolak/$1');
    $routes->post('booking/assign-staff/(:num)', 'AdminBookingController::bookingAssignStaff/$1');

    // Staff / Teknisi
    $routes->get('staff', 'AdminStaffController::staffIndex');
    $routes->get('staff/create', 'AdminStaffController::staffCreate');
    $routes->post('staff/store', 'AdminStaffController::staffStore');
    $routes->get('staff/edit/(:num)', 'AdminStaffController::staffEdit/$1');
    $routes->post('staff/update/(:num)', 'AdminStaffController::staffUpdate/$1');
    $routes->get('staff/delete/(:num)', 'AdminStaffController::staffDelete/$1');

    // User
    $routes->get('users', 'AdminUserController::usersIndex');
    $routes->post('users/store', 'AdminUserController::usersStore');
    $routes->post('users/update/(:num)', 'AdminUserController::usersUpdate/$1');
    $routes->post('users/toggle/(:num)', 'AdminUserController::usersToggle/$1');
    $routes->get('users/delete/(:num)', 'AdminUserController::usersDelete/$1');
});

// Route Group untuk Staff
$routes->group('staff', ['filter' => 'auth:staff'], static function ($routes) {
    $routes->get('/', 'StaffController::index');
    // Route untuk menampilkan halaman jadwal tugas staff
    $routes->get('jadwal-tugas', 'StaffJadwalController::index');
    $routes->get('riwayat-pekerjaan', 'StaffRiwayatController::index');
    // Route untuk staff menyelesaikan orderan berdasarkan order_id
    $routes->post('tugas/selesai/(:any)', 'StaffJadwalController::selesai/$1');
    // Route untuk staff mengambil orderan berdasarkan order_id
    $routes->post('tugas/ambil/(:any)', 'StaffJadwalController::ambil/$1');
});

// Route Group untuk Pelanggan (User)
$routes->group('user', ['filter' => 'auth:pelanggan'], static function ($routes) {
    $routes->get('/', 'UserDashboardController::index');
    // Halaman buat booking laundry baru
    $routes->get('pesanan/baru', 'UserBookingController::pesananBaru');
    // Proses menaruh booking laundry baru
    $routes->post('pesanan/store', 'UserBookingController::pesananStore');
    // Halaman pembayaran pesanan
    $routes->get('pesanan/bayar/(:any)', 'UserPembayaranController::pesananBayar/$1');
    // Proses penyelesaian pembayaran
    $routes->post('pesanan/proses-bayar/(:any)', 'UserPembayaranController::pesananProsesBayar/$1');
    // Batalkan booking pesanan yang belum dibayar
    $routes->post('pesanan/batal/(:any)', 'UserBookingController::pesananBatal/$1');
    // Halaman riwayat pesanan (history) pelanggan
    $routes->get('history', 'UserHistoryController::history');
    // Proses pengiriman ulasan & rating bintang
    $routes->post('pesanan/ulasan/(:any)', 'UserHistoryController::pesananUlasan/$1');
});

// Route untuk autentikasi (Login & Register) - Dilindungi oleh GuestFilter
$routes->get('/login', 'AuthController::login', ['filter' => 'guest']);
$routes->post('/login/process', 'AuthController::processLogin', ['filter' => 'guest']);
$routes->get('/register', 'AuthController::register', ['filter' => 'guest']);
$routes->post('/register/process', 'AuthController::processRegister', ['filter' => 'guest']);
$routes->get('/logout', 'AuthController::logout');

// Route untuk manajemen edit profil user, staff, dan admin
$routes->get('/profile', 'ProfileController::index', ['filter' => 'auth']);
$routes->post('/profile/update', 'ProfileController::update', ['filter' => 'auth']);
