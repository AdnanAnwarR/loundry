<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Riwayat Pesanan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="pagetitle">
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('user') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Riwayat Pesanan</li>
    </ol>
  </nav>
</div>

<!-- Flash Message Notifications -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i> Mohon perbaiki ulasan Anda:
    <ul class="mb-0 mt-1">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-12">
    <div class="card recent-sales overflow-auto shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="card-title text-dark fs-5 mb-4">Riwayat Semua Booking Laundry Anda</h5>
        
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th scope="col">Order ID</th>
              <th scope="col">Daftar Layanan</th>
              <th scope="col">Jadwal Pengantaran</th>
              <th scope="col">Total Biaya</th>
              <th scope="col">Status Pengerjaan</th>
              <th scope="col">Status Pembayaran</th>
              <th scope="col" style="width: 250px;">Rating & Ulasan</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($bookings)): ?>
              <?php foreach ($bookings as $booking): ?>
                <tr>
                  <th scope="row"><span class="badge bg-secondary py-2 px-2"><?= esc($booking->order_id) ?></span></th>
                  <td>
                    <span class="text-dark fw-bold"><?= esc($booking->layanan_list) ?></span>
                    <small class="d-block text-muted"><?= esc($booking->catatan) ?></small>
                  </td>
                  <td>
                    <span class="text-dark"><?= date('d M Y', strtotime($booking->tanggal)) ?></span>
                    <small class="d-block text-secondary">Pukul <?= date('H:i', strtotime($booking->slot_waktu)) ?></small>
                  </td>
                  <td class="fw-bold text-dark">Rp <?= number_format($booking->grand_total, 0, ',', '.') ?></td>
                  <td>
                    <?php 
                      // Menampilkan badge status pengerjaan laundry
                      $status = strtolower($booking->status_pesanan);
                      if ($status === 'selesai') {
                          echo '<span class="badge bg-success text-white"><i class="bi bi-check-all me-1"></i>Selesai</span>';
                      } elseif ($status === 'dibatalkan') {
                          echo '<span class="badge bg-danger text-white"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>';
                      } elseif ($status === 'ditolak') {
                          echo '<span class="badge bg-dark text-white"><i class="bi bi-hand-thumbs-down me-1"></i>Ditolak</span>';
                      } else {
                          echo '<span class="badge bg-secondary">' . esc($booking->status_pesanan) . '</span>';
                      }
                    ?>
                    <?php if ($booking->nama_staff): ?>
                      <div class="mt-1">
                        <span class="badge bg-light text-info border border-info" style="font-size: 0.75rem;"><i class="bi bi-person-badge me-1"></i>Staff: <?= esc($booking->nama_staff) ?></span>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php 
                      // Menampilkan badge status pembayaran
                      $payStatus = strtolower($booking->status_pembayaran ?? 'belum_dibayar');
                      if ($payStatus === 'sudah_dibayar') {
                          echo '<span class="badge bg-success"><i class="bi bi-credit-card-2-front-fill me-1"></i>Lunas</span>';
                      } elseif ($payStatus === 'gagal') {
                          echo '<span class="badge bg-danger"><i class="bi bi-x-square me-1"></i>Gagal</span>';
                      } else {
                          echo '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Belum Dibayar</span>';
                      }
                    ?>
                  </td>
                  <td>
                    <?php if ($status === 'selesai'): ?>
                        <?php if ($booking->rating === null): ?>
                            <!-- Tombol untuk membuka Modal Ulasan -->
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#ulasanModal" 
                                    data-orderid="<?= esc($booking->order_id) ?>">
                                <i class="bi bi-star-fill me-1"></i> Beri Ulasan
                            </button>
                        <?php else: ?>
                            <div class="text-warning mb-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi <?= $i <= $booking->rating ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <small class="text-dark d-block" style="max-width: 220px; word-wrap: break-word;">
                                "<?= esc($booking->ulasan) ?>"
                            </small>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted small">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat booking laundry.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Tautan Paginasi Terpaginasi 10 data -->
        <div class="mt-3">
          <?= $pager->links() ?>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Modal Beri Ulasan & Rating -->
<div class="modal fade" id="ulasanModal" tabindex="-1" aria-labelledby="ulasanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="ulasanModalLabel"><i class="bi bi-chat-heart me-2"></i> Beri Ulasan & Penilaian</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="ulasanForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body p-4">
          
          <div class="text-center mb-4">
            <label class="form-label d-block text-secondary fw-semibold mb-2">Pilih Rating Bintang</label>
            <div class="rating-stars fs-3 text-secondary" style="cursor: pointer;">
              <i class="bi bi-star star-btn" data-value="1"></i>
              <i class="bi bi-star star-btn" data-value="2"></i>
              <i class="bi bi-star star-btn" data-value="3"></i>
              <i class="bi bi-star star-btn" data-value="4"></i>
              <i class="bi bi-star star-btn" data-value="5"></i>
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="" required>
          </div>

          <div class="mb-3">
            <label for="ulasan" class="form-label text-secondary fw-semibold">Ulasan Anda</label>
            <textarea class="form-control" name="ulasan" id="ulasan" rows="4" placeholder="Bagikan pengalaman Anda menggunakan layanan laundry kami..." minlength="5" maxlength="1000" required></textarea>
          </div>

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Kirim Ulasan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const ulasanModal = document.getElementById('ulasanModal');
  const ulasanForm = document.getElementById('ulasanForm');
  const ratingInput = document.getElementById('ratingInput');
  const stars = document.querySelectorAll('.star-btn');

  // Event modal dibuka untuk set action URL form
  ulasanModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const orderId = button.getAttribute('data-orderid');
    
    // Set Action URL form dinamis
    ulasanForm.setAttribute('action', '<?= base_url('user/pesanan/ulasan/') ?>/' + orderId);
    
    // Reset form dan bintang
    ulasanForm.reset();
    ratingInput.value = '';
    stars.forEach(s => s.className = 'bi bi-star star-btn text-secondary');
  });

  // Logika Klik Bintang Ulasan
  stars.forEach(star => {
    star.addEventListener('click', function() {
      const val = parseInt(this.getAttribute('data-value'));
      ratingInput.value = val;
      
      stars.forEach(s => {
        const sVal = parseInt(s.getAttribute('data-value'));
        if (sVal <= val) {
          s.className = 'bi bi-star-fill star-btn text-warning';
        } else {
          s.className = 'bi bi-star star-btn text-secondary';
        }
      });
    });
    
    // Hover effect untuk visualisasi
    star.addEventListener('mouseover', function() {
      const val = parseInt(this.getAttribute('data-value'));
      stars.forEach(s => {
        const sVal = parseInt(s.getAttribute('data-value'));
        if (sVal <= val) {
          s.classList.add('text-warning');
        }
      });
    });

    star.addEventListener('mouseout', function() {
      const val = ratingInput.value ? parseInt(ratingInput.value) : 0;
      stars.forEach(s => {
        const sVal = parseInt(s.getAttribute('data-value'));
        if (sVal > val) {
          s.classList.remove('text-warning');
        }
      });
    });
  });
});
</script>

<?= $this->endSection() ?>
