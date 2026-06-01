<?= $this->extend($layout) ?>

<?= $this->section('page_title') ?>
Edit Profil
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="pagetitle">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url(session()->get('role')) ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil Saya</li>
        </ol>
    </nav>
</div>

<!-- Notifikasi Sukses -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Notifikasi Error -->
<?php if (session()->getFlashdata('errors')): ?>
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i> Mohon perbaiki kesalahan berikut:
    <ul class="mb-0 mt-1">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <!-- Card Foto Profil -->
        <div class="card shadow-sm border-0 text-center py-4">
            <div class="card-body">
                <div class="position-relative d-inline-block mb-3">
                    <?php 
                        $fotoPath = $user->foto ? base_url('uploads/profile/' . $user->foto) : base_url('NiceAdmin/assets/img/profile-img.jpg');
                    ?>
                    <img id="avatar-preview" src="<?= $fotoPath ?>" alt="Foto Profil" class="rounded-circle border border-3 border-primary shadow-sm" style="width: 140px; height: 140px; object-fit: cover;">
                </div>
                <h5 class="fw-bold text-dark mb-1"><?= esc($user->name) ?></h5>
                <p class="text-secondary small mb-2"><?= esc($user->email) ?></p>
                <span class="badge bg-primary px-3 py-2 rounded-pill"><?= ucfirst(esc($user->role)) ?></span>
                
                <div class="mt-3">
                    <p class="text-muted small mb-0">No. HP: <strong><?= esc($user->no_hp) ?></strong></p>
                    <p class="text-muted small">Terdaftar sejak: <?= date('d M Y', strtotime($user->created_at)) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Form Pengaturan Akun -->
        <div class="card shadow-sm border-0 p-4">
            <div class="card-body">
                <h5 class="card-title text-dark fs-5 mb-4">Pengaturan Profil</h5>
                
                <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row mb-3">
                        <label for="name" class="col-sm-3 col-form-label text-secondary fw-semibold">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="name" name="name" value="<?= esc($user->name) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label for="email" class="col-sm-3 col-form-label text-secondary fw-semibold">Alamat Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc($user->email) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label for="password" class="col-sm-3 col-form-label text-secondary fw-semibold">Kata Sandi Baru</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                            <div class="form-text small text-muted">Gunakan minimal 6 karakter.</div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <label for="foto" class="col-sm-3 col-form-label text-secondary fw-semibold">Foto Profil</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(event)">
                            <div class="form-text small text-muted">Format file yang diperbolehkan: JPG, JPEG, PNG. Maksimal 2MB.</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                            <a href="<?= base_url(session()->get('role')) ?>" class="btn btn-light px-3">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('avatar-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<?= $this->endSection() ?>