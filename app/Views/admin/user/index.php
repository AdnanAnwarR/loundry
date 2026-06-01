<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Manajemen User</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Manajemen User</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mt-3">Daftar Pelanggan Terdaftar</h5>

          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Status Akun</th>
                <th>Bergabung</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $i => $u): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-2"
                         style="width:36px;height:36px;color:#fff;font-weight:bold;">
                      <?= strtoupper(substr($u->name, 0, 1)) ?>
                    </div>
                    <?= esc($u->name) ?>
                  </div>
                </td>
                <td><?= esc($u->email) ?></td>
                <td><?= esc($u->no_hp) ?></td>
                <td>
                  <?php if ($u->is_active): ?>
                    <span class="badge bg-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d M Y', strtotime($u->created_at)) ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <form method="post" action="<?= base_url('admin/users/toggle/' . $u->id) ?>" class="d-inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-<?= $u->is_active ? 'warning' : 'success' ?>"
                              title="<?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>"
                              onclick="return confirm('Ubah status akun ini?')">
                        <i class="bi bi-<?= $u->is_active ? 'person-x' : 'person-check' ?>"></i>
                        <?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
                      </button>
                    </form>
                    <a href="<?= base_url('admin/users/delete/' . $u->id) ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Yakin hapus user ini? Semua data booking akan ikut terhapus!')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($users)): ?>
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada user terdaftar.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>

          <!-- Tautan Paginasi Server-side Terpaginasi 10 data -->
          <div class="mt-3">
            <?= $pager->links() ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
