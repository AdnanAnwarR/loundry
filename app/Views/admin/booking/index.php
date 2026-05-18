<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Semua Booking</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Booking</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
            <h5 class="card-title mb-0">Daftar Semua Booking</h5>
          </div>

          <!-- Filter -->
          <form method="get" action="<?= base_url('admin/booking') ?>" class="row g-3 mb-4">
            <div class="col-md-3">
              <label class="form-label">Filter Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">-- Semua Status --</option>
                <?php foreach (['pending', 'dikonfirmasi', 'proses', 'selesai', 'dibatalkan', 'ditolak'] as $s): ?>
                  <option value="<?= $s ?>" <?= $statusFilter == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Filter Tanggal</label>
              <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= $tanggalFilter ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Filter Layanan</label>
              <select name="layanan_id" class="form-select form-select-sm">
                <option value="">-- Semua Layanan --</option>
                <?php foreach ($layanan as $l): ?>
                  <option value="<?= $l->id ?>" <?= $layananFilter == $l->id ? 'selected' : '' ?>><?= esc($l->nama_layanan) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-funnel me-1"></i> Filter
              </button>
              <a href="<?= base_url('admin/booking') ?>" class="btn btn-secondary btn-sm">Reset</a>
            </div>
          </form>

          <table class="table table-hover datatable">
            <thead class="table-light">
              <tr>
                <th>Order ID</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Tanggal / Slot</th>
                <th>Total</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $badges = [
                  'pending'      => 'warning',
                  'dikonfirmasi' => 'info',
                  'proses'       => 'primary',
                  'selesai'      => 'success',
                  'dibatalkan'   => 'secondary',
                  'ditolak'      => 'danger',
                ];
                $bayarBadges = [
                  'belum_dibayar' => 'warning',
                  'sudah_dibayar' => 'success',
                  'gagal'         => 'danger',
                ];
              ?>
              <?php foreach ($booking as $b): ?>
              <tr>
                <td><small><?= esc($b->order_id) ?></small></td>
                <td><?= esc($b->nama_pelanggan) ?></td>
                <td><?= esc($b->nama_layanan) ?></td>
                <td>
                  <strong><?= date('d/m/Y', strtotime($b->tanggal)) ?></strong><br>
                  <small class="text-muted"><?= esc($b->slot_waktu) ?></small>
                </td>
                <td>Rp <?= number_format($b->total_harga, 0, ',', '.') ?></td>
                <td><span class="badge bg-<?= $badges[$b->status] ?? 'secondary' ?>"><?= ucfirst($b->status) ?></span></td>
                <td>
                  <span class="badge bg-<?= $bayarBadges[$b->status_pembayaran] ?? 'secondary' ?>">
                    <?= str_replace('_', ' ', ucfirst($b->status_pembayaran)) ?>
                  </span>
                </td>
                <td>
                  <a href="<?= base_url('admin/booking/' . $b->id) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> Detail
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($booking)): ?>
                <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada booking ditemukan.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
  const dataTable = new simpleDatatables.DataTable(".datatable");
</script>
<?= $this->endSection() ?>
