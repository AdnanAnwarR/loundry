<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Kelola Jadwal</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Jadwal</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h5 class="card-title mb-0">Daftar Jadwal Tersedia</h5>
            <a href="<?= base_url('admin/jadwal/create') ?>" class="btn btn-primary">
              <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
            </a>
          </div>

          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Slot Waktu</th>
                <th>Kapasitas</th>
                <th>Terisi</th>
                <th>Sisa Slot</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($jadwal as $i => $j): ?>
              <?php $sisa = $j->kapasitas - $j->terisi; ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= date('d M Y', strtotime($j->tanggal)) ?></strong><br>
                  <small class="text-muted"><?= date('l', strtotime($j->tanggal)) ?></small>
                </td>
                <td><span class="badge bg-info"><?= esc($j->slot_waktu) ?></span></td>
                <td><?= $j->kapasitas ?></td>
                <td><?= $j->terisi ?></td>
                <td>
                  <?php if ($sisa > 0): ?>
                    <span class="badge bg-success"><?= $sisa ?> tersedia</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Penuh</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (strtotime($j->tanggal) >= strtotime(date('Y-m-d'))): ?>
                    <span class="badge bg-success">Mendatang</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Lewat</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="<?= base_url('admin/jadwal/edit/' . $j->id) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="<?= base_url('admin/jadwal/delete/' . $j->id) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                       onclick="return confirm('Yakin hapus jadwal ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($jadwal)): ?>
                <tr><td colspan="8" class="text-center text-muted py-5">Belum ada jadwal.</td></tr>
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
