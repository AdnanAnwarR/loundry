<?php $baseUrl = base_url(); $niceAdminUrl = base_url('NiceAdmin/'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $title ?? 'Admin Panel' ?> - LaundryKu Admin</title>
  <meta name="description" content="Sistem Manajemen Laundry - Admin Panel">

  <link href="<?= $niceAdminUrl ?>assets/img/favicon.png" rel="icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="<?= $niceAdminUrl ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $niceAdminUrl ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= $niceAdminUrl ?>assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?= $niceAdminUrl ?>assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?= $niceAdminUrl ?>assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="<?= $niceAdminUrl ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="<?= base_url('admin/dashboard') ?>" class="logo d-flex align-items-center">
        <i class="bi bi-droplet-fill" style="font-size:1.5rem;color:#4154f1;margin-right:8px;"></i>
        <span class="d-none d-lg-block fw-bold">LaundryKu</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle" style="font-size:1.5rem;"></i>
            <span class="d-none d-md-block dropdown-toggle ps-2"><?= session()->get('name') ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?= session()->get('name') ?></h6>
              <span class="badge bg-danger">Admin</span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?= base_url('logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link <?= (current_url() == base_url('admin/dashboard') || current_url() == base_url('admin')) ? '' : 'collapsed' ?>" href="<?= base_url('admin/dashboard') ?>">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <!-- Kelola Layanan -->
      <li class="nav-item">
        <a class="nav-link <?= strpos(current_url(), 'admin/layanan') !== false ? '' : 'collapsed' ?>" data-bs-target="#layanan-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bag-heart"></i><span>Kelola Layanan</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="layanan-nav" class="nav-content collapse <?= strpos(current_url(), 'admin/layanan') !== false ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= base_url('admin/layanan') ?>">
              <i class="bi bi-circle"></i><span>Daftar Layanan</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('admin/layanan/create') ?>">
              <i class="bi bi-circle"></i><span>Tambah Layanan</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- Kelola Jadwal -->
      <li class="nav-item">
        <a class="nav-link <?= strpos(current_url(), 'admin/jadwal') !== false ? '' : 'collapsed' ?>" data-bs-target="#jadwal-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-calendar3"></i><span>Kelola Jadwal</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="jadwal-nav" class="nav-content collapse <?= strpos(current_url(), 'admin/jadwal') !== false ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= base_url('admin/jadwal') ?>">
              <i class="bi bi-circle"></i><span>Daftar Jadwal</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('admin/jadwal/create') ?>">
              <i class="bi bi-circle"></i><span>Tambah Jadwal</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- Semua Booking -->
      <li class="nav-item">
        <a class="nav-link <?= strpos(current_url(), 'admin/booking') !== false ? '' : 'collapsed' ?>" href="<?= base_url('admin/booking') ?>">
          <i class="bi bi-clipboard-check"></i>
          <span>Semua Booking</span>
        </a>
      </li>

      <li class="nav-heading">Manajemen SDM</li>

      <!-- Kelola Staff -->
      <li class="nav-item">
        <a class="nav-link <?= strpos(current_url(), 'admin/staff') !== false ? '' : 'collapsed' ?>" data-bs-target="#staff-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-badge"></i><span>Kelola Staff</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="staff-nav" class="nav-content collapse <?= strpos(current_url(), 'admin/staff') !== false ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?= base_url('admin/staff') ?>">
              <i class="bi bi-circle"></i><span>Daftar Staff</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('admin/staff/create') ?>">
              <i class="bi bi-circle"></i><span>Tambah Staff</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- Manajemen User -->
      <li class="nav-item">
        <a class="nav-link <?= strpos(current_url(), 'admin/users') !== false ? '' : 'collapsed' ?>" href="<?= base_url('admin/users') ?>">
          <i class="bi bi-people"></i>
          <span>Manajemen User</span>
        </a>
      </li>

      <li class="nav-heading">Akun</li>

      <!-- Logout -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?= base_url('logout') ?>">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li>

    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
        <i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="<?= $niceAdminUrl ?>assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?= $niceAdminUrl ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= $niceAdminUrl ?>assets/vendor/chart.js/chart.umd.js"></script>
  <script src="<?= $niceAdminUrl ?>assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?= $niceAdminUrl ?>assets/js/main.js"></script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
