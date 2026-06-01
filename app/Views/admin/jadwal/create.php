<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Tambah Jadwal</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/jadwal') ?>">Jadwal</a></li>
      <li class="breadcrumb-item active">Tambah</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body mt-3">
          <h5 class="card-title">Form Tambah Jadwal</h5>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                  <li><?= esc($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('admin/jadwal/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="tanggal" name="tanggal"
                     value="<?= old('tanggal') ?>" min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
              <label for="slot_waktu" class="form-label">Slot Waktu (Jam) <span class="text-danger">*</span></label>
              <input type="time" class="form-control" id="slot_waktu" name="slot_waktu" value="<?= old('slot_waktu') ?>" required>
            </div>

            <div class="mb-4">
              <label for="kapasitas" class="form-label">Kapasitas (maks booking) <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                     value="<?= old('kapasitas', 5) ?>" min="1" max="50" required>
              <small class="text-muted">Jumlah maksimal booking untuk slot ini</small>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan Jadwal
              </button>
              <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary">
                <i class="bi bi-x-lg me-1"></i> Batal
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
