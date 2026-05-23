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
              <a class="nav-link <?= (service('request')->getPath() == 'staff/jadwal-tugas-harian') ? 'active' : 'collapsed'; ?>" href="/staff/jadwal-tugas-harian">
                  <i class="bi bi-calendar-check"></i>
                  <span>Jadwal Tugas Harian</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link <?= (service('request')->getPath() == 'staff/riwayat-pekerjaan') ? 'active' : 'collapsed'; ?>" href="/staff/riwayat-pekerjaan">
                  <i class="bi bi-clock-history"></i>
                  <span>Riwayat Pekerjaan</span>
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