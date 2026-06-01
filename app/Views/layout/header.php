<!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="<?= base_url('/') ?>" class="logo d-flex align-items-center">
        <img src="<?= base_url('NiceAdmin/assets/img/logo.png') ?>" alt="">
        <span class="d-none d-lg-block">LaundryKu</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <?php 
              $userFoto = session()->get('foto');
              $fotoPath = $userFoto ? base_url('uploads/profile/' . $userFoto) : base_url('NiceAdmin/assets/img/profile-img.jpg');
            ?>
            <img src="<?= $fotoPath ?>" alt="Profile" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?= session()->get('name') ?? 'Guest' ?></span>
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?= session()->get('name') ?? 'Guest' ?></h6>
              <span><?= ucfirst(session()->get('role') ?? 'Visitor') ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <?php if (session()->get('logged_in')): ?>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('profile') ?>">
                  <i class="bi bi-person"></i>
                  <span>Profil Saya</span>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('logout') ?>">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </a>
              </li>
            <?php else: ?>
              <li>
                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('login') ?>">
                  <i class="bi bi-box-arrow-in-right"></i>
                  <span>Login</span>
                </a>
              </li>
            <?php endif; ?>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->