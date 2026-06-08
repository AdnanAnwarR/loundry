<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Detail Booking</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('admin/booking') ?>">Booking</a></li>
      <li class="breadcrumb-item active">Detail</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">

    <!-- Detail Info -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mt-2">Informasi Booking</h5>

          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Order ID</div>
            <div class="col-sm-8"><code><?= esc($booking->order_id) ?></code></div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Pelanggan</div>
            <div class="col-sm-8"><?= esc($booking->nama_pelanggan) ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Email</div>
            <div class="col-sm-8"><?= esc($booking->email) ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">No HP</div>
            <div class="col-sm-8"><?= esc($booking->no_hp) ?></div>
          </div>
          <hr>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Daftar Layanan</div>
            <div class="col-sm-8">
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Nama Layanan</th>
                      <th>Harga / kg</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $item): ?>
                      <tr>
                        <td><span class="fw-bold text-dark"><?= esc($item->nama_layanan) ?></span></td>
                        <td>Rp <?= number_format($item->harga, 0, ',', '.') ?></td>
                        <td class="fw-bold text-dark">Rp <?= number_format($item->total_harga, 0, ',', '.') ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Tanggal</div>
            <div class="col-sm-8"><?= date('d F Y', strtotime($booking->tanggal)) ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Slot Waktu</div>
            <div class="col-sm-8"><?= esc($booking->slot_waktu) ?></div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Catatan</div>
            <div class="col-sm-8"><?= esc($booking->catatan) ?: '<em class="text-muted">Tidak ada catatan</em>' ?></div>
          </div>
          <hr>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Total Harga Pesanan</div>
            <div class="col-sm-8"><strong class="fs-5 text-primary">Rp <?= number_format($grandTotal, 0, ',', '.') ?></strong></div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Status Booking</div>
            <div class="col-sm-8">
              <?php
                $badges = [
                  'pending' => 'warning', 'dikonfirmasi' => 'info',
                  'proses' => 'primary', 'selesai' => 'success',
                  'dibatalkan' => 'secondary', 'ditolak' => 'danger',
                ];
              ?>
              <span class="badge bg-<?= $badges[$booking->status] ?? 'secondary' ?> fs-6"><?= ucfirst($booking->status) ?></span>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Status Pembayaran</div>
            <div class="col-sm-8">
              <?php
                $payBadges = ['belum_dibayar' => 'warning', 'sudah_dibayar' => 'success', 'gagal' => 'danger'];
              ?>
              <span class="badge bg-<?= $payBadges[$booking->status_pembayaran] ?? 'secondary' ?>">
                <?= str_replace('_', ' ', ucfirst($booking->status_pembayaran)) ?>
              </span>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Staff Ditugaskan</div>
            <div class="col-sm-8">
              <?php if ($booking->nama_staff): ?>
                <span class="badge bg-info"><?= esc($booking->nama_staff) ?></span>
              <?php else: ?>
                <span class="badge bg-light text-secondary border">Belum ditugaskan</span>
              <?php endif; ?>
            </div>
          </div>
          <?php if ($booking->rating): ?>
          <hr>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Rating</div>
            <div class="col-sm-8">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="bi bi-star<?= $i <= $booking->rating ? '-fill text-warning' : ' text-muted' ?>"></i>
              <?php endfor; ?>
              (<?= $booking->rating ?>/5)
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-4 fw-semibold text-muted">Ulasan</div>
            <div class="col-sm-8"><?= esc($booking->ulasan) ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Action Panel -->
    <div class="col-lg-4">

      <!-- Aksi Booking -->
      <?php if ($booking->status === 'pending'): ?>
      <div class="card border-warning">
        <div class="card-body">
          <h5 class="card-title text-warning"><i class="bi bi-hourglass me-2"></i>Booking Menunggu</h5>
          <p class="text-muted small">Konfirmasi atau tolak booking ini.</p>

          <form method="post" action="<?= base_url('admin/booking/konfirmasi/' . $booking->id) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success w-100 mb-2">
              <i class="bi bi-check-circle me-1"></i> Konfirmasi Booking
            </button>
          </form>

          <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#tolakModal">
            <i class="bi bi-x-circle me-1"></i> Tolak Booking
          </button>
        </div>
      </div>
      <?php elseif ($booking->status === 'dikonfirmasi' && !$booking->staf_id): ?>
      <div class="card border-info">
        <div class="card-body">
          <h5 class="card-title text-info"><i class="bi bi-person-badge me-2"></i>Assign Staff</h5>
          <form method="post" action="<?= base_url('admin/booking/assign-staff/' . $booking->id) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label">Pilih Staff/Teknisi</label>
              <select class="form-select" name="staf_id" required>
                <option value="">-- Pilih Staff --</option>
                <?php foreach ($allStaff as $s): ?>
                  <option value="<?= $s->id ?>"><?= esc($s->name) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-info w-100 text-white">
              <i class="bi bi-person-check me-1"></i> Assign & Mulai Proses
            </button>
          </form>
        </div>
      </div>
      <?php endif; ?>

      <!-- Back Button -->
      <div class="card">
        <div class="card-body">
          <a href="<?= base_url('admin/booking') ?>" class="btn btn-secondary w-100">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Modal Tolak -->
<div class="modal fade" id="tolakModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Tolak Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/booking/tolak/' . $booking->id) ?>">
        <?= csrf_field() ?>
        <div class="modal-body">
          <p>Masukkan alasan penolakan:</p>
          <textarea name="alasan" class="form-control" rows="3" placeholder="Alasan penolakan..." required></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
