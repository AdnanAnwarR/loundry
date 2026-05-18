<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Kelola Layanan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Layanan</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h5 class="card-title mb-0">Daftar Layanan</h5>
            <a href="<?= base_url('admin/layanan/create') ?>" class="btn btn-primary">
              <i class="bi bi-plus-circle me-1"></i> Tambah Layanan
            </a>
          </div>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                  <li><?= esc($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <table class="table table-hover datatable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Foto</th>
                <th>Nama Layanan</th>
                <th>Harga</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($layanan as $i => $l): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <?php if ($l->foto): ?>
                    <img src="<?= base_url('uploads/layanan/' . $l->foto) ?>" alt="<?= esc($l->nama_layanan) ?>" class="rounded" style="width:60px;height:45px;object-fit:cover;">
                  <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:45px;">
                      <i class="bi bi-image text-muted"></i>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= esc($l->nama_layanan) ?></strong>
                  <?php if ($l->deskripsi): ?>
                    <br><small class="text-muted"><?= esc(substr($l->deskripsi, 0, 60)) ?>...</small>
                  <?php endif; ?>
                </td>
                <td>Rp <?= number_format($l->harga, 0, ',', '.') ?></td>
                <td><?= $l->durasi ?> menit</td>
                <td>
                  <?php if ($l->is_active): ?>
                    <span class="badge bg-success">Aktif</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Nonaktif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="<?= base_url('admin/layanan/edit/' . $l->id) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form method="post" action="<?= base_url('admin/layanan/toggle/' . $l->id) ?>" class="d-inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-<?= $l->is_active ? 'secondary' : 'success' ?>" title="<?= $l->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>">
                        <i class="bi bi-<?= $l->is_active ? 'toggle-on' : 'toggle-off' ?>"></i>
                      </button>
                    </form>
                    <a href="<?= base_url('admin/layanan/delete/' . $l->id) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                       onclick="return confirm('Yakin hapus layanan ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($layanan)): ?>
                <tr><td colspan="7" class="text-center text-muted py-5">Belum ada layanan. <a href="<?= base_url('admin/layanan/create') ?>">Tambah sekarang</a></td></tr>
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
