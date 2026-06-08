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
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title text-dark fs-5 m-0">Daftar Staff / Teknisi</h5>
            <!-- Tombol Tambah Staff Trigger Modal -->
            <button type="button" class="btn btn-primary fw-bold px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
              <i class="bi bi-person-plus me-1"></i> Tambah Staff
            </button>
          </div>

          <!-- Alert error global jika ada di luar modal -->
          <?php if (session()->getFlashdata('errors') && !session()->getFlashdata('open_add_modal') && !session()->getFlashdata('open_edit_id')): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                      <div class="avatar rounded-circle bg-primary-light text-primary d-flex align-items-center justify-content-center me-2 fw-bold"
                           style="width:36px;height:36px;background-color: #efe8ff;font-size:14px;">
                        <?= strtoupper(substr($s->name, 0, 1)) ?>
                      </div>
                      <span class="text-dark fw-semibold"><?= esc($s->name) ?></span>
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
                      <!-- Tombol Edit Modal -->
                      <button type="button" class="btn btn-sm btn-outline-warning edit-staff-btn" 
                              data-id="<?= $s->id ?>" 
                              data-name="<?= esc($s->name) ?>" 
                              data-email="<?= esc($s->email) ?>" 
                              data-nohp="<?= esc($s->no_hp) ?>" 
                              data-isactive="<?= $s->is_active ?>"
                              title="Edit Staff">
                        <i class="bi bi-pencil"></i>
                      </button>

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

          <!-- Tautan Paginasi -->
          <div class="mt-3">
            <?= $pager->links() ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal Tambah Staff -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="addStaffModalLabel"><i class="bi bi-person-plus me-2"></i>Tambah Staff Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/staff/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          
          <?php if (session()->getFlashdata('errors') && session()->getFlashdata('open_add_modal')): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3">
              <h6 class="fw-bold mb-1">Gagal menyimpan data:</h6>
              <ul class="mb-0 small">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label for="add_name" class="form-label fw-semibold text-secondary">Nama Lengkap</label>
            <input type="text" class="form-control" id="add_name" name="name" value="<?= old('name') ?>" placeholder="Nama lengkap staff..." required>
          </div>
          <div class="mb-3">
            <label for="add_email" class="form-label fw-semibold text-secondary">Alamat Email</label>
            <input type="email" class="form-control" id="add_email" name="email" value="<?= old('email') ?>" placeholder="staff@example.com" required>
          </div>
          <div class="mb-3">
            <label for="add_no_hp" class="form-label fw-semibold text-secondary">No Handphone</label>
            <input type="text" class="form-control" id="add_no_hp" name="no_hp" value="<?= old('no_hp') ?>" placeholder="Minimal 10 digit" required>
          </div>
          <div class="mb-3">
            <label for="add_password" class="form-label fw-semibold text-secondary">Password</label>
            <input type="password" class="form-control" id="add_password" name="password" placeholder="Minimal 6 karakter..." required>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Staff</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Staff -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="editStaffModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Informasi Staff</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editStaffForm" action="" method="post">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          
          <?php if (session()->getFlashdata('errors') && session()->getFlashdata('open_edit_id')): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3">
              <h6 class="fw-bold mb-1">Gagal menyimpan data:</h6>
              <ul class="mb-0 small">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label for="edit_name" class="form-label fw-semibold text-secondary">Nama Lengkap</label>
            <input type="text" class="form-control" id="edit_name" name="name" placeholder="Nama lengkap staff..." required>
          </div>
          <div class="mb-3">
            <label for="edit_email" class="form-label fw-semibold text-secondary">Alamat Email</label>
            <input type="email" class="form-control" id="edit_email" name="email" placeholder="staff@example.com" required>
          </div>
          <div class="mb-3">
            <label for="edit_no_hp" class="form-label fw-semibold text-secondary">No Handphone</label>
            <input type="text" class="form-control" id="edit_no_hp" name="no_hp" placeholder="Minimal 10 digit" required>
          </div>
          <div class="mb-3">
            <label for="edit_password" class="form-label fw-semibold text-secondary">Password Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Minimal 6 karakter...">
          </div>
          <div class="mb-3">
            <label for="edit_is_active" class="form-label fw-semibold text-secondary">Status Staff</label>
            <select class="form-select" id="edit_is_active" name="is_active" required>
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const addStaffModal = new bootstrap.Modal(document.getElementById('addStaffModal'));
  const editStaffModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
  
  // Handlers for Edit buttons
  const editButtons = document.querySelectorAll('.edit-staff-btn');
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      prefillEditModal(this);
      editStaffModal.show();
    });
  });

  // Prefill helper
  function prefillEditModal(btn) {
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const email = btn.getAttribute('data-email');
    const nohp = btn.getAttribute('data-nohp');
    const isactive = btn.getAttribute('data-isactive');

    const form = document.getElementById('editStaffForm');
    form.setAttribute('action', '<?= base_url("admin/staff/update/") ?>/' + id);

    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_no_hp').value = nohp;
    document.getElementById('edit_is_active').value = isactive;
    document.getElementById('edit_password').value = ''; // Reset password field
  }

  // Handle open modal on validation failure redirects
  <?php if (session()->getFlashdata('open_add_modal')): ?>
    addStaffModal.show();
  <?php endif; ?>

  <?php if (session()->getFlashdata('open_edit_id')): ?>
    const failedId = '<?= session()->getFlashdata('open_edit_id') ?>';
    const failedBtn = document.querySelector(`.edit-staff-btn[data-id="${failedId}"]`);
    if (failedBtn) {
      prefillEditModal(failedBtn);
      // Let's restore old inputs if they exist
      <?php if (old('name')): ?>
        document.getElementById('edit_name').value = '<?= esc(old('name')) ?>';
      <?php endif; ?>
      <?php if (old('email')): ?>
        document.getElementById('edit_email').value = '<?= esc(old('email')) ?>';
      <?php endif; ?>
      <?php if (old('no_hp')): ?>
        document.getElementById('edit_no_hp').value = '<?= esc(old('no_hp')) ?>';
      <?php endif; ?>
      <?php if (old('is_active') !== null): ?>
        document.getElementById('edit_is_active').value = '<?= esc(old('is_active')) ?>';
      <?php endif; ?>
      editStaffModal.show();
    }
  <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
