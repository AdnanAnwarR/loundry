<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Tambah Layanan</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/layanan') ?>">Layanan</a></li>
      <li class="breadcrumb-item active">Tambah</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body mt-3">
          <h5 class="card-title">Form Tambah Layanan</h5>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                  <li><?= esc($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('admin/layanan/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="nama_layanan" class="form-label">Nama Layanan <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_layanan" name="nama_layanan"
                     value="<?= old('nama_layanan') ?>" placeholder="Contoh: Cuci Kering, Cuci Setrika..." required>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="harga" name="harga"
                       value="<?= old('harga') ?>" placeholder="15000" min="0" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="durasi" class="form-label">Durasi (menit) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="durasi" name="durasi"
                       value="<?= old('durasi') ?>" placeholder="60" min="1" required>
                <small class="text-muted">Estimasi durasi pengerjaan dalam menit</small>
              </div>
            </div>

            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                        placeholder="Jelaskan detail layanan ini..."><?= old('deskripsi') ?></textarea>
            </div>



            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan Layanan
              </button>
              <a href="<?= base_url('admin/layanan') ?>" class="btn btn-secondary">
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

