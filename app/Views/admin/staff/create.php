<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Tambah Staff</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/staff') ?>">Staff</a></li>
      <li class="breadcrumb-item active">Tambah</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body mt-3">
          <h5 class="card-title">Form Tambah Staff / Teknisi</h5>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                  <li><?= esc($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('admin/staff/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="name" name="name"
                     value="<?= old('name') ?>" placeholder="Nama staff" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="email" name="email"
                     value="<?= old('email') ?>" placeholder="email@example.com" required>
            </div>

            <div class="mb-3">
              <label for="no_hp" class="form-label">No HP <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="no_hp" name="no_hp"
                     value="<?= old('no_hp') ?>" placeholder="08xxxxxxxxxx" required>
            </div>

            <div class="mb-4">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" id="password" name="password"
                     placeholder="Minimal 6 karakter" required>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Tambah Staff
              </button>
              <a href="<?= base_url('admin/staff') ?>" class="btn btn-secondary">
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
