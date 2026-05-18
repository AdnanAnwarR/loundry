<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Edit Staff</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/staff') ?>">Staff</a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body mt-3">
          <h5 class="card-title">Form Edit Staff</h5>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                  <li><?= esc($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('admin/staff/update/' . $staff->id) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="name" name="name"
                     value="<?= old('name', $staff->name) ?>" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="email" name="email"
                     value="<?= old('email', $staff->email) ?>" required>
            </div>

            <div class="mb-3">
              <label for="no_hp" class="form-label">No HP <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="no_hp" name="no_hp"
                     value="<?= old('no_hp', $staff->no_hp) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" name="is_active">
                <option value="1" <?= $staff->is_active ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= !$staff->is_active ? 'selected' : '' ?>>Nonaktif</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="password" class="form-label">Password Baru</label>
              <input type="password" class="form-control" id="password" name="password"
                     placeholder="Kosongkan jika tidak ingin mengubah password">
              <small class="text-muted">Isi hanya jika ingin mengubah password.</small>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
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
