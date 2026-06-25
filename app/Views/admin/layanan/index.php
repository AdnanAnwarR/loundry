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
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title text-dark fs-5 m-0">Daftar Layanan Laundry</h5>
            <!-- Tombol Tambah Layanan Trigger Modal -->
            <button type="button" class="btn btn-primary fw-bold px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addLayananModal">
              <i class="bi bi-plus-circle me-1"></i> Tambah Layanan
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
                  <th>Nama Layanan</th>
                  <th>Harga</th>
                  <th>Durasi</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $currentPage = $pager->getCurrentPage();
                  $perPage = 10;
                  $startNumber = ($currentPage - 1) * $perPage;
                  foreach ($layanan as $i => $l): 
                ?>
                <tr>
                  <td><?= $startNumber + $i + 1 ?></td>
                  <td>
                    <span class="text-dark fw-semibold"><?= esc($l->nama_layanan) ?></span>
                    <?php if ($l->deskripsi): ?>
                      <br><small class="text-muted d-block" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= esc($l->deskripsi) ?></small>
                    <?php endif; ?>
                  </td>
                  <td><span class="text-dark fw-medium">Rp <?= number_format($l->harga, 0, ',', '.') ?> / kg</span></td>
                  <td><span class="badge bg-light text-dark border"><i class="bi bi-clock me-1 text-primary"></i><?= $l->durasi ?> menit</span></td>
                  <td>
                    <?php if ($l->is_active): ?>
                      <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <!-- Tombol Edit Modal -->
                      <button type="button" class="btn btn-sm btn-outline-warning edit-layanan-btn" 
                              data-id="<?= $l->id ?>" 
                              data-nama="<?= esc($l->nama_layanan) ?>" 
                              data-harga="<?= $l->harga ?>" 
                              data-durasi="<?= $l->durasi ?>"
                              data-deskripsi="<?= esc($l->deskripsi) ?>"
                              data-isactive="<?= $l->is_active ?>"
                              title="Edit Layanan">
                        <i class="bi bi-pencil"></i>
                      </button>

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
                  <tr><td colspan="6" class="text-center text-muted py-5">Belum ada layanan laundry.</td></tr>
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

<!-- Modal Tambah Layanan -->
<div class="modal fade" id="addLayananModal" tabindex="-1" aria-labelledby="addLayananModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="addLayananModalLabel"><i class="bi bi-plus-circle me-2"></i>Tambah Layanan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/layanan/store') ?>" method="post">
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
            <label for="add_nama_layanan" class="form-label fw-semibold text-secondary">Nama Layanan</label>
            <input type="text" class="form-control" id="add_nama_layanan" name="nama_layanan" value="<?= old('nama_layanan') ?>" placeholder="Contoh: Cuci Kering, Cuci Setrika..." required>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="add_harga" class="form-label fw-semibold text-secondary">Harga (Rp)</label>
              <input type="number" class="form-control" id="add_harga" name="harga" value="<?= old('harga') ?>" placeholder="15000" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="add_durasi" class="form-label fw-semibold text-secondary">Durasi (menit)</label>
              <input type="number" class="form-control" id="add_durasi" name="durasi" value="<?= old('durasi', 60) ?>" placeholder="60" required>
            </div>
          </div>
          <div class="mb-3">
            <small class="text-muted d-block mb-2">Estimasi durasi pengerjaan dalam menit</small>
          </div>

          <div class="mb-3">
            <label for="add_deskripsi" class="form-label fw-semibold text-secondary">Deskripsi</label>
            <textarea class="form-control" id="add_deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan detail layanan ini..."><?= old('deskripsi') ?></textarea>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Layanan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Layanan -->
<div class="modal fade" id="editLayananModal" tabindex="-1" aria-labelledby="editLayananModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="editLayananModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Layanan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editLayananForm" action="" method="post">
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
            <label for="edit_nama_layanan" class="form-label fw-semibold text-secondary">Nama Layanan</label>
            <input type="text" class="form-control" id="edit_nama_layanan" name="nama_layanan" placeholder="Contoh: Cuci Kering, Cuci Setrika..." required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_harga" class="form-label fw-semibold text-secondary">Harga (Rp)</label>
              <input type="number" class="form-control" id="edit_harga" name="harga" placeholder="15000" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_durasi" class="form-label fw-semibold text-secondary">Durasi (menit)</label>
              <input type="number" class="form-control" id="edit_durasi" name="durasi" placeholder="60" required>
            </div>
          </div>
          <div class="mb-3">
            <small class="text-muted d-block mb-2">Estimasi durasi pengerjaan dalam menit</small>
          </div>

          <div class="mb-3">
            <label for="edit_deskripsi" class="form-label fw-semibold text-secondary">Deskripsi</label>
            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan detail layanan ini..."></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_is_active" class="form-label fw-semibold text-secondary">Status Layanan</label>
            <select class="form-select" id="edit_is_active" name="is_active" required>
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Layanan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const addLayananModal = new bootstrap.Modal(document.getElementById('addLayananModal'));
  const editLayananModal = new bootstrap.Modal(document.getElementById('editLayananModal'));
  
  // Handlers for Edit buttons
  const editButtons = document.querySelectorAll('.edit-layanan-btn');
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      prefillEditModal(this);
      editLayananModal.show();
    });
  });

  // Prefill helper
  function prefillEditModal(btn) {
    const id = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama');
    const harga = btn.getAttribute('data-harga');
    const durasi = btn.getAttribute('data-durasi');
    const deskripsi = btn.getAttribute('data-deskripsi');
    const isactive = btn.getAttribute('data-isactive');

    const form = document.getElementById('editLayananForm');
    form.setAttribute('action', '<?= base_url("admin/layanan/update/") ?>/' + id);

    document.getElementById('edit_nama_layanan').value = nama;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_durasi').value = durasi;
    document.getElementById('edit_deskripsi').value = deskripsi;
    document.getElementById('edit_is_active').value = isactive;
  }

  // Handle open modal on validation failure redirects
  <?php if (session()->getFlashdata('open_add_modal')): ?>
    addLayananModal.show();
  <?php endif; ?>

  <?php if (session()->getFlashdata('open_edit_id')): ?>
    const failedId = '<?= session()->getFlashdata('open_edit_id') ?>';
    const failedBtn = document.querySelector(`.edit-layanan-btn[data-id="${failedId}"]`);
    if (failedBtn) {
      prefillEditModal(failedBtn);
      // Let's restore old inputs if they exist
      <?php if (old('nama_layanan')): ?>
        document.getElementById('edit_nama_layanan').value = '<?= esc(old('nama_layanan')) ?>';
      <?php endif; ?>
      <?php if (old('harga')): ?>
        document.getElementById('edit_harga').value = '<?= esc(old('harga')) ?>';
      <?php endif; ?>
      <?php if (old('durasi')): ?>
        document.getElementById('edit_durasi').value = '<?= esc(old('durasi')) ?>';
      <?php endif; ?>
      <?php if (old('deskripsi')): ?>
        document.getElementById('edit_deskripsi').value = '<?= esc(old('deskripsi')) ?>';
      <?php endif; ?>
      <?php if (old('is_active') !== null): ?>
        document.getElementById('edit_is_active').value = '<?= esc(old('is_active')) ?>';
      <?php endif; ?>
      editLayananModal.show();
    }
  <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
