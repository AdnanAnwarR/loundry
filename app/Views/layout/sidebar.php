<!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <?php if (session()->get('role') == 'staff'): ?>

          <li class="nav-item">
              <a class="nav-link <?= (service('request')->getPath() == 'staff') ? 'active' : 'collapsed'; ?>" href="/staff">
                  <i class="bi bi-grid"></i>
                  <span>Dashboard</span>
              </a>
          </li>

          <li class="nav-item">
              <!-- Tautan menuju halaman jadwal tugas staff yang baru -->
              <a class="nav-link <?= (service('request')->getPath() == 'staff/jadwal-tugas') ? 'active' : 'collapsed'; ?>" href="/staff/jadwal-tugas">
                  <i class="bi bi-calendar-check"></i>
                  <span>Jadwal Tugas</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?= (service('request')->getPath() == 'staff/riwayat-pekerjaan') ? 'active' : 'collapsed'; ?>" href="/staff/riwayat-pekerjaan">
                  <i class="bi bi-clock-history"></i>
                  <span>Riwayat Pekerjaan</span>
              </a>
          </li>

      <?php endif; ?>

      <?php if (session()->get('role') == 'pelanggan'): ?>

          <li class="nav-item">
              <a class="nav-link <?= (service('request')->getPath() == 'user') ? 'active' : 'collapsed'; ?>" href="/user">
                  <i class="bi bi-grid"></i>
                  <span>Dashboard</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?= (service('request')->getPath() == 'user/pesanan/baru') ? 'active' : 'collapsed'; ?>" href="/user/pesanan/baru">
                  <i class="bi bi-plus-circle"></i>
                  <span>Buat Pesanan Baru</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?= (service('request')->getPath() == 'user/history') ? 'active' : 'collapsed'; ?>" href="/user/history">
                  <i class="bi bi-clock-history"></i>
                  <span>Riwayat Pesanan</span>
              </a>
          </li>

      <?php endif; ?>
      
      <li class="nav-item">
        <small>AKUN</small>
      </li>
      
      <li class="nav-item">
          <a class="nav-link <?= (service('uri')->getSegment(1) == 'logout') ? 'active' : 'collapsed'; ?>" href="/logout">
              <i class="bi bi-calendar-check"></i>
              <span>Logout</span>
          </a>
      </li><!-- End Dashboard Nav -->
    
    </ul>

  </aside><!-- End Sidebar-->