<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Message Notifications -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- WhatsApp Notification Simulation Alert -->
<?php if (session()->get('wa_simulation_alerts')): ?>
    <?php 
        $waAlerts = session()->get('wa_simulation_alerts'); 
        // Hapus alerts setelah dibaca agar tidak muncul berulang kali
        session()->remove('wa_simulation_alerts');
    ?>
    <?php foreach ($waAlerts as $alert): ?>
        <div class="alert alert-info border-info border-start border-4 shadow-sm" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-whatsapp me-2 text-success"></i> [SIMULASI WHATSAPP] Pesan Terkirim!</h6>
            <p class="mb-0 text-dark"><strong>Ke:</strong> <?= esc($alert['no_hp']) ?></p>
            <p class="mb-0 text-muted"><strong>Isi Pesan:</strong> "<?= esc($alert['message']) ?>"</p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="row">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="card-title text-primary fs-5 mb-2">Selamat Datang di LaundryKu, <?= esc(session()->get('name')) ?>!</h5>
        <p class="text-secondary mb-3">Anda dapat memesan layanan laundry baru secara online, memantau pengerjaan secara real-time, dan mengelola pembayaran dengan mudah.</p>
        <a href="<?= base_url('user/pesanan/baru') ?>" class="btn btn-primary fw-bold px-4 py-2"><i class="bi bi-plus-circle me-1"></i> Buat Pesanan Laundry Baru</a>
      </div>
    </div>

    <!-- Active and History Bookings -->
    <div class="card recent-sales overflow-auto shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="card-title text-dark fs-5 mb-4">Daftar Booking Laundry Anda</h5>
        
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th scope="col">Order ID</th>
              <th scope="col">Daftar Layanan</th>
              <th scope="col">Jadwal Pengantaran</th>
              <th scope="col">Total Biaya</th>
              <th scope="col">Status Pengerjaan</th>
              <th scope="col">Status Pembayaran</th>
              <th scope="col" class="text-center">Aksi</th>
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
                      if ($status === 'pending') {
                          echo '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';
                      } elseif ($status === 'dikonfirmasi') {
                          echo '<span class="badge bg-info text-white"><i class="bi bi-check-circle me-1"></i>Dikonfirmasi</span>';
                      } elseif ($status === 'proses') {
                          echo '<span class="badge bg-primary text-white"><i class="bi bi-gear-fill me-1"></i>Sedang Diproses</span>';
                      } elseif ($status === 'selesai') {
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
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                      <?php if ($booking->status_pesanan !== 'dibatalkan' && $booking->status_pesanan !== 'ditolak' && $booking->status_pesanan !== 'selesai'): ?>
                        <?php if ($booking->status_pembayaran !== 'sudah_dibayar'): ?>
                          
                          <!-- Tombol Bayar -->
                          <a href="<?= base_url('user/pesanan/bayar/' . $booking->order_id) ?>" class="btn btn-sm btn-success fw-bold"><i class="bi bi-wallet2 me-1"></i> Bayar</a>
                          
                          <!-- Form Pembatalan -->
                          <form action="<?= base_url('user/pesanan/batal/' . $booking->order_id) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan laundry ini?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Batal</button>
                          </form>
                          
                        <?php else: ?>
                          <span class="text-muted small"><i class="bi bi-lock-fill"></i> Tidak bisa batal</span>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="text-secondary">-</span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">Belum ada booking laundry.</td>
              </tr>
            <?php endif; ?>
        </table>

        <!-- Tautan Paginasi Terpaginasi 10 data -->
        <div class="mt-3">
          <?= $pager->links() ?>
        </div>

      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Info Card Pengeluaran -->
    <div class="card info-card revenue-card shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="card-title text-secondary fs-6 mb-3">Total Pengeluaran <span>| Bulan Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle bg-success-light text-success d-flex align-items-center justify-content-center p-3 fs-3" style="background-color: #e8f5e9;">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 fw-bold mb-0 text-success">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h6>
            <span class="text-muted small pt-2">Dari transaksi berstatus lunas</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Petunjuk Laundry -->
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="card-title text-dark fs-6 mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Alur Pemesanan</h5>
        <div class="activity">
          <div class="d-flex align-items-start mb-3">
            <div class="badge bg-primary me-3 py-2">1</div>
            <div>
              <p class="mb-0 fw-bold text-dark">Buat Booking</p>
              <small class="text-muted">Pilih layanan, estimasi berat pakaian, dan pilih jadwal pengantaran.</small>
            </div>
          </div>
          <div class="d-flex align-items-start mb-3">
            <div class="badge bg-primary me-3 py-2">2</div>
            <div>
              <p class="mb-0 fw-bold text-dark">Bayar Nominal</p>
              <small class="text-muted">Lakukan pembayaran via bank/QRIS sebelum pesanan diproses.</small>
            </div>
          </div>
          <div class="d-flex align-items-start mb-3">
            <div class="badge bg-primary me-3 py-2">3</div>
            <div>
              <p class="mb-0 fw-bold text-dark">Pengerjaan Staff</p>
              <small class="text-muted">Admin menunjuk staff untuk mencuci laundry Anda.</small>
            </div>
          </div>
          <div class="d-flex align-items-start">
            <div class="badge bg-primary me-3 py-2">4</div>
            <div>
              <p class="mb-0 fw-bold text-dark">Selesai & Notifikasi WA</p>
              <small class="text-muted">Selesai dicuci, notifikasi WhatsApp akan dikirim otomatis ke nomor Anda.</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
