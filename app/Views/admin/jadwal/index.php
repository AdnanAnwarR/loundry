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
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title text-dark fs-5 m-0">Daftar Jadwal Tersedia</h5>
            <!-- Tombol Tambah Jadwal Trigger Modal -->
            <button type="button" class="btn btn-primary fw-bold px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
              <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
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
                <?php 
                  $currentPage = $pager->getCurrentPage();
                  $perPage = 10;
                  $startNumber = ($currentPage - 1) * $perPage;
                  foreach ($jadwal as $i => $j): 
                ?>
                <?php $sisa = $j->kapasitas - $j->terisi; ?>
                <tr>
                  <td><?= $startNumber + $i + 1 ?></td>
                  <td>
                    <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($j->tanggal)) ?></span><br>
                    <small class="text-muted"><?= date('l', strtotime($j->tanggal)) ?></small>
                  </td>
                  <td><span class="badge bg-info-light text-info border border-info-subtle px-2 py-1"><i class="bi bi-clock me-1"></i><?= esc($j->slot_waktu) ?></span></td>
                  <td><?= $j->kapasitas ?></td>
                  <td><?= $j->terisi ?></td>
                  <td>
                    <?php if ($sisa > 0): ?>
                      <span class="badge bg-success-light text-success border border-success-subtle"><?= $sisa ?> tersedia</span>
                    <?php else: ?>
                      <span class="badge bg-danger-light text-danger border border-danger-subtle">Penuh</span>
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
                      <!-- Tombol Edit Modal -->
                      <button type="button" class="btn btn-sm btn-outline-warning edit-jadwal-btn" 
                              data-id="<?= $j->id ?>" 
                              data-tanggal="<?= $j->tanggal ?>" 
                              data-slotwaktu="<?= esc($j->slot_waktu) ?>" 
                              data-kapasitas="<?= $j->kapasitas ?>"
                              title="Edit Jadwal">
                        <i class="bi bi-pencil"></i>
                      </button>

                      <a href="<?= base_url('admin/jadwal/delete/' . $j->id) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                         onclick="return confirm('Yakin hapus jadwal ini?')">
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($jadwal)): ?>
                  <tr><td colspan="8" class="text-center text-muted py-5">Belum ada jadwal tersedia.</td></tr>
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

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="addJadwalModalLabel"><i class="bi bi-calendar-plus me-2"></i>Tambah Jadwal</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/jadwal/store') ?>" method="post">
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
            <label for="add_tanggal" class="form-label fw-semibold text-secondary">Tanggal</label>
            <input type="date" class="form-control" id="add_tanggal" name="tanggal" value="<?= old('tanggal') ?>" required>
          </div>

          <div class="mb-3">
            <label for="add_slot_waktu" class="form-label fw-semibold text-secondary">Slot Waktu (Jam)</label>
            <input type="time" class="form-control" id="add_slot_waktu" name="slot_waktu" value="<?= old('slot_waktu') ?>" required>
          </div>

          <div class="mb-3">
            <label for="add_kapasitas" class="form-label fw-semibold text-secondary">Kapasitas (maks booking)</label>
            <input type="number" class="form-control" id="add_kapasitas" name="kapasitas" value="<?= old('kapasitas', 5) ?>" placeholder="5" required>
            <small class="text-muted d-block mt-1">Jumlah maksimal booking untuk slot ini</small>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="editJadwalModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Jadwal</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editJadwalForm" action="" method="post">
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
            <label for="edit_tanggal" class="form-label fw-semibold text-secondary">Tanggal</label>
            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
          </div>

          <div class="mb-3">
            <label for="edit_slot_waktu" class="form-label fw-semibold text-secondary">Slot Waktu (Jam)</label>
            <input type="time" class="form-control" id="edit_slot_waktu" name="slot_waktu" required>
          </div>

          <div class="mb-3">
            <label for="edit_kapasitas" class="form-label fw-semibold text-secondary">Kapasitas (maks booking)</label>
            <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" required>
            <small class="text-muted d-block mt-1">Jumlah maksimal booking untuk slot ini</small>
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
  const addJadwalModal = new bootstrap.Modal(document.getElementById('addJadwalModal'));
  const editJadwalModal = new bootstrap.Modal(document.getElementById('editJadwalModal'));
  
  // Handlers for Edit buttons
  const editButtons = document.querySelectorAll('.edit-jadwal-btn');
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      prefillEditModal(this);
      editJadwalModal.show();
    });
  });

  // Prefill helper
  function prefillEditModal(btn) {
    const id = btn.getAttribute('data-id');
    const tanggal = btn.getAttribute('data-tanggal');
    const slotwaktu = btn.getAttribute('data-slotwaktu');
    const kapasitas = btn.getAttribute('data-kapasitas');

    const form = document.getElementById('editJadwalForm');
    form.setAttribute('action', '<?= base_url("admin/jadwal/update/") ?>/' + id);

    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_slot_waktu').value = slotwaktu;
    document.getElementById('edit_kapasitas').value = kapasitas;
  }

  // Handle open modal on validation failure redirects
  <?php if (session()->getFlashdata('open_add_modal')): ?>
    addJadwalModal.show();
  <?php endif; ?>

  <?php if (session()->getFlashdata('open_edit_id')): ?>
    const failedId = '<?= session()->getFlashdata('open_edit_id') ?>';
    const failedBtn = document.querySelector(`.edit-jadwal-btn[data-id="${failedId}"]`);
    if (failedBtn) {
      prefillEditModal(failedBtn);
      // Let's restore old inputs if they exist
      <?php if (old('tanggal')): ?>
        document.getElementById('edit_tanggal').value = '<?= esc(old('tanggal')) ?>';
      <?php endif; ?>
      <?php if (old('slot_waktu')): ?>
        document.getElementById('edit_slot_waktu').value = '<?= esc(old('slot_waktu')) ?>';
      <?php endif; ?>
      <?php if (old('kapasitas')): ?>
        document.getElementById('edit_kapasitas').value = '<?= esc(old('kapasitas')) ?>';
      <?php endif; ?>
      editJadwalModal.show();
    }
  <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
