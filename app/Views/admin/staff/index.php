<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Kelola Staff</h1>
  <nav> 
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Staff</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h5 class="card-title mb-0">Daftar Staff / Teknisi</h5>
            <a href="<?= base_url('admin/staff/create') ?>" class="btn btn-primary">
              <i class="bi bi-person-plus me-1"></i> Tambah Staff
            </a>
          </div>

          <table class="table table-hover datatable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Status</th>
                <th>Bergabung</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($staff as $i => $s): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                         style="width:36px;height:36px;color:#fff;font-weight:bold;">
                      <?= strtoupper(substr($s->name, 0, 1)) ?>
                    </div>
                    <?= esc($s->name) ?>
                  </div>
                </td>
                <td><?= esc($s->email) ?></td>
                <td><?= esc($s->no_hp) ?></td>
                <td>
                  <?php if ($s->is_active): ?>
                    <span class="badge bg-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d M Y', strtotime($s->created_at)) ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="<?= base_url('admin/staff/edit/' . $s->id) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="<?= base_url('admin/staff/delete/' . $s->id) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                       onclick="return confirm('Yakin hapus staff ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($staff)): ?>
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada data staff.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
  const dataTable = new simpleDatatables.DataTable(".datatable");
</script>
<?= $this->endSection() ?>
