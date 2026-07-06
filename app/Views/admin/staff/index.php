<?= $this->extend('layout/admin_layout') ?> <!-- Menggunakan template/layout utama halaman admin -->
<?= $this->section('content') ?> <!-- Memulai bagian content yang akan ditampilkan pada layout -->

<div class="pagetitle">
      <!-- Menampilkan judul halaman -->
  <h1>Kelola Staff</h1>

   <!-- Breadcrumb sebagai navigasi halaman pengguna mengetahui posisi halaman yang sedang dibuka, 
    yaitu Dashboard kemudian Staff.-->
  <nav> 
    <ol class="breadcrumb">
       <!-- Link kembali ke Dashboard -->
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <!-- Halaman yang sedang aktif -->
      <li class="breadcrumb-item active">Staff</li>
    </ol>
  </nav>
</div>

<section class="section">
  <!-- Container utama halaman -->
  <div class="row">
    <div class="col-lg-12">
       <!-- Card untuk membungkus isi halaman -->
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <!-- Judul tabel dan tombol tambah staff -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title text-dark fs-5 m-0">Daftar Staff / Teknisi</h5>

            <!-- Tombol Tambah Staff Trigger Modal -->
            <button type="button" class="btn btn-primary fw-bold px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
              <i class="bi bi-person-plus me-1"></i> Tambah Staff
            </button>
          </div>

          <!-- Menampilkan pesan error jika validasi gagal dan bukan berasal dari modal -->
          <?php if (session()->getFlashdata('errors') && !session()->getFlashdata('open_add_modal') && !session()->getFlashdata('open_edit_id')): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3">
              <ul class="mb-0">
                  <!-- Menampilkan seluruh daftar error -->
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

            <!-- Membuat tabel yang responsive -->
          <div class="table-responsive">
             <!-- Tabel daftar staff -->
            <table class="table table-hover align-middle">
              <!-- Header tabel -->
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
                <?php 
                 // Mengambil halaman yang sedang aktif
                  $currentPage = $pager->getCurrentPage();
                 // Menentukan jumlah data setiap halaman menampilkan maksimal sepuluh data.
                  $perPage = 10;
                 // Menghitung nomor awal pada halaman aktif Kode ini menghitung nomor urut 
                 // agar tetap berlanjut ketika berpindah halaman."
                  $startNumber = ($currentPage - 1) * $perPage;
                    // Melakukan perulangan data staff
                  foreach ($staff as $i => $s): 
                ?>
                <tr>
                   <!-- Menampilkan nomor urut -->
                  <td><?= $startNumber + $i + 1 ?></td>
                     <!-- Kolom Nama -->
                  <td>
                    <div class="d-flex align-items-center">
                         <!-- Avatar berupa huruf pertama nama -->
                      <div class="avatar rounded-circle bg-primary-light text-primary d-flex align-items-center justify-content-center me-2 fw-bold"
                           style="width:36px;height:36px;background-color: #efe8ff;font-size:14px;">
                        <!-- mengambil huruf pertama dari nama staff menggunakan fungsi substr() dan strtoupper(). -->
                           <?= strtoupper(substr($s->name, 0, 1)) ?> 
                      </div>

                          <!-- Menampilkan nama staff -->
                      <span class="text-dark fw-semibold"><?= esc($s->name) ?></span>
                    </div>
                  </td>

                    <!-- Menampilkan email -->
                  <td><?= esc($s->email) ?></td>
                  <!-- Menampilkan nomor HP -->
                  <td><?= esc($s->no_hp) ?></td>

                   <!-- Menampilkan status staff -->
                  <td>
                    <?php if ($s->is_active): ?>

                       <!-- Badge jika aktif hijau-->
                      <span class="badge bg-success">Aktif</span> 
                    <?php else: ?>
                       <!-- Badge jika nonaktif abu" -->
                      <span class="badge bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                    <!-- Menampilkan tanggal bergabung -->
                  <td><?= date('d M Y', strtotime($s->created_at)) ?></td>
                  <!-- Tombol aksi -->
                  <td>
                    <div class="d-flex gap-1">
                          <!-- Tombol Edit Modal -->
                          <!-- Mengirim ID staff data-id -->
                            <!-- Mengirim nama staff-name -->
                              <!-- Mengirim email staff -->
                                <!-- Mengirim nomor HP -->
                                 <!-- Mengirim status aktif -->
                      <button type="button" class="btn btn-sm btn-outline-warning edit-staff-btn"    
                              data-id="<?= $s->id ?>" 
                              data-name="<?= esc($s->name) ?>"
                              data-email="<?= esc($s->email) ?>" 
                              data-nohp="<?= esc($s->no_hp) ?>" 
                              data-isactive="<?= $s->is_active ?>"
                              title="Edit Staff">
                        <i class="bi bi-pencil"></i>
                      </button>

                        <!-- Tombol Hapus -->
                      <a href="<?= base_url('admin/staff/delete/' . $s->id) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"

                         onclick="return confirm('Yakin hapus staff ini?')"> <!-- Konfirmasi sebelum menghapus -->
                        <i class="bi bi-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                 <!-- Jika data staff kosong -->
                <?php if (empty($staff)): ?>
                  <tr><td colspan="7" class="text-center text-muted py-5">Belum ada data staff.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

            <!-- Menampilkan navigasi pagination untuk menampilkan navigasi halaman jika jumlah data lebih dari sepuluh-->
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
    <!-- Membuat popup (modal) Bootstrap -->
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg"> <!-- Isi modal -->
      <div class="modal-header bg-primary text-white"> <!-- Header modal -->
        <h5 class="modal-title fw-bold" id="addStaffModalLabel"><i class="bi bi-person-plus me-2"></i>Tambah Staff Baru</h5>
          <!-- Tombol menutup modal -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Form tambah staff -->
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
              <!-- Mengembalikan input sebelumnya jika validasi gagal -->
            <input type="text" class="form-control" id="add_name" name="name" value="<?= old('name') ?>" placeholder="Nama lengkap staff..." required>
          </div>
            <!-- Input Email -->
          <div class="mb-3">
            <label for="add_email" class="form-label fw-semibold text-secondary">Alamat Email</label>
            <input type="email" class="form-control" id="add_email" name="email" value="<?= old('email') ?>" placeholder="staff@example.com" required>
          </div>

          <!-- Input Nomor HP -->
          <div class="mb-3">
            <label for="add_no_hp" class="form-label fw-semibold text-secondary">No Handphone</label>
            <input type="text" class="form-control" id="add_no_hp" name="no_hp" value="<?= old('no_hp') ?>" placeholder="Minimal 10 digit" required>
          </div>
           <!-- Input Password -->
          <div class="mb-3">
            <label for="add_password" class="form-label fw-semibold text-secondary">Password</label>
            <input type="password" class="form-control" id="add_password" name="password" placeholder="Minimal 6 karakter..." required>
          </div>

        </div>
         <!-- Footer modal -->
        <div class="modal-footer bg-light">
           <!-- Tombol batal -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <!-- Tombol simpan -->
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Staff</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Staff -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">    <!-- Menampilkan modal di tengah layar -->
    <div class="modal-content border-0 shadow-lg">  <!-- Isi modal -->
      <div class="modal-header bg-primary text-white"><!-- Header modal -->
        <h5 class="modal-title fw-bold" id="editStaffModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Informasi Staff</h5>
        <!-- Tombol menutup modal -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <!-- Form edit data -->
      <form id="editStaffForm" action="" method="post">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          
         <!-- Menampilkan pesan error jika update gagal -->
          <?php if (session()->getFlashdata('errors') && session()->getFlashdata('open_edit_id')): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-3">
              <h6 class="fw-bold mb-1">Gagal menyimpan data:</h6>
              <ul class="mb-0 small">
                 <!-- Menampilkan semua pesan error -->
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

           <!-- Input Nama -->
          <div class="mb-3">
            <label for="edit_name" class="form-label fw-semibold text-secondary">Nama Lengkap</label>
            <input type="text" class="form-control" id="edit_name" name="name" placeholder="Nama lengkap staff..." required>
          </div>
           <!-- Input Email -->
          <div class="mb-3">
            <label for="edit_email" class="form-label fw-semibold text-secondary">Alamat Email</label>
            <input type="email" class="form-control" id="edit_email" name="email" placeholder="staff@example.com" required>
          </div>
          <!-- Input Nomor HP -->
          <div class="mb-3">
            <label for="edit_no_hp" class="form-label fw-semibold text-secondary">No Handphone</label>
            <input type="text" class="form-control" id="edit_no_hp" name="no_hp" placeholder="Minimal 10 digit" required>
          </div>
            <!-- Input Password Baru -->
          <div class="mb-3">
            <label for="edit_password" class="form-label fw-semibold text-secondary">Password Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Minimal 6 karakter...">
          </div>
             <!-- Status Staff -->
          <div class="mb-3">
            <label for="edit_is_active" class="form-label fw-semibold text-secondary">Status Staff</label>
            <select class="form-select" id="edit_is_active" name="is_active" required>
                <!-- Pilihan status, Admin juga dapat mengubah status 
                 staff menjadi aktif atau nonaktif. -->  
            <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>

        </div>
        <div class="modal-footer bg-light">
            <!-- Tombol batal -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <!-- Tombol simpan perubahan -->
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // Menjalankan JavaScript setelah seluruh halaman selesai dimuat
document.addEventListener('DOMContentLoaded', function () {
   // Membuat objek modal Tambah Staff
  const addStaffModal = new bootstrap.Modal(document.getElementById('addStaffModal'));
    // Membuat objek modal Edit Staff
  const editStaffModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
  
  // Mengambil semua tombol edit
  const editButtons = document.querySelectorAll('.edit-staff-btn');
  // Menambahkan event click pada setiap tombol edit
  editButtons.forEach(btn => {
    btn.addEventListener('click', function () {
  // Mengisi data staff ke dalam form edit
      prefillEditModal(this);
      editStaffModal.show();  // Menampilkan modal edit
    });
  });

  // Fungsi untuk mengisi data ke modal edit
  function prefillEditModal(btn) {
  
   // Mengambil data dari atribut data-* pada tombol edit
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const email = btn.getAttribute('data-email');
    const nohp = btn.getAttribute('data-nohp');
    const isactive = btn.getAttribute('data-isactive');
  // Mengambil form edit
    const form = document.getElementById('editStaffForm');
     // Mengubah action form sesuai ID staff
    form.setAttribute('action', '<?= base_url("admin/staff/update/") ?>/' + id);
  // Mengisi field nama
    document.getElementById('edit_name').value = name;
    // Mengisi field email
    document.getElementById('edit_email').value = email;
     // Mengisi field nomor HP
    document.getElementById('edit_no_hp').value = nohp;
  // Mengisi status aktif/nonaktif
    document.getElementById('edit_is_active').value = isactive;
     // Mengosongkan password setiap kali modal dibuka
    document.getElementById('edit_password').value = ''; // Reset password field
  }

  // Membuka kembali modal Tambah
  // jika validasi gagal
  <?php if (session()->getFlashdata('open_add_modal')): ?>
      // Membuka modal tambah otomatis
    addStaffModal.show();
  <?php endif; ?>

  // Membuka kembali modal Edit
  // jika validasi gagal
  <?php if (session()->getFlashdata('open_edit_id')): ?>

  // Mengambil ID staff yang gagal diupdate
    const failedId = '<?= session()->getFlashdata('open_edit_id') ?>';
  // Mencari tombol edit berdasarkan ID tersebut
    const failedBtn = document.querySelector(`.edit-staff-btn[data-id="${failedId}"]`);

     // Jika tombol ditemukan
    if (failedBtn) {
    // Isi kembali data staff ke modal
      prefillEditModal(failedBtn);
     // Mengembalikan nama yang sebelumnya diinput
      <?php if (old('name')): ?>
        document.getElementById('edit_name').value = '<?= esc(old('name')) ?>';
      <?php endif; ?>

       // Mengembalikan email sebelumnya
      <?php if (old('email')): ?>
        document.getElementById('edit_email').value = '<?= esc(old('email')) ?>';
      <?php endif; ?>

       // Mengembalikan nomor HP sebelumnya
      <?php if (old('no_hp')): ?>
        document.getElementById('edit_no_hp').value = '<?= esc(old('no_hp')) ?>';
      <?php endif; ?>

        // Mengembalikan status sebelumnya
      <?php if (old('is_active') !== null): ?>
        document.getElementById('edit_is_active').value = '<?= esc(old('is_active')) ?>';
      <?php endif; ?>

      // Menampilkan kembali modal edit
      editStaffModal.show();
    }
  <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
<!-- Mengakhiri section script -->
