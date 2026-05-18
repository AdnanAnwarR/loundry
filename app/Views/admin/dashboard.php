<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="pagetitle">
  <h1>Dashboard Admin</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row">

    <!-- Left side columns -->
    <div class="col-lg-8">
      <div class="row">

        <!-- Booking Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Total Booking <span>| Semua</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="ps-3">
                  <h6><?= $totalBooking ?></h6>
                  <span class="text-warning small pt-1 fw-bold"><?= $bookingPending ?></span>
                  <span class="text-muted small pt-2 ps-1">menunggu konfirmasi</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pendapatan Card -->
        <div class="col-xxl-4 col-md-6">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Pendapatan <span>| Total</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-currency-exchange"></i>
                </div>
                <div class="ps-3">
                  <h6>Rp <?= number_format($pendapatan, 0, ',', '.') ?></h6>
                  <span class="text-success small pt-1 fw-bold"><?= $bookingSelesai ?></span>
                  <span class="text-muted small pt-2 ps-1">booking selesai</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pelanggan Card -->
        <div class="col-xxl-4 col-xl-12">
          <div class="card info-card customers-card">
            <div class="card-body">
              <h5 class="card-title">Pelanggan <span>| Aktif</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6><?= $totalPelanggan ?></h6>
                  <span class="text-primary small pt-1 fw-bold"><?= $bookingProses ?></span>
                  <span class="text-muted small pt-2 ps-1">sedang diproses</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart Pendapatan -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Pendapatan <span>| 7 Hari Terakhir</span></h5>
              <div id="revenueChart"></div>
            </div>
          </div>
        </div>

        <!-- Booking Terbaru -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h5 class="card-title">Booking Terbaru <span>| 10 Terakhir</span></h5>
              <table class="table table-hover table-borderless">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($bookingTerbaru as $b): ?>
                  <tr>
                    <td><small class="text-muted"><?= esc($b->order_id) ?></small></td>
                    <td><?= esc($b->nama_pelanggan) ?></td>
                    <td><?= esc($b->nama_layanan) ?></td>
                    <td><?= date('d/m/Y', strtotime($b->tanggal)) ?></td>
                    <td>Rp <?= number_format($b->total_harga, 0, ',', '.') ?></td>
                    <td>
                      <?php
                        $badges = [
                          'pending'      => 'warning',
                          'dikonfirmasi' => 'info',
                          'proses'       => 'primary',
                          'selesai'      => 'success',
                          'dibatalkan'   => 'secondary',
                          'ditolak'      => 'danger',
                        ];
                        $badge = $badges[$b->status] ?? 'secondary';
                      ?>
                      <span class="badge bg-<?= $badge ?>"><?= ucfirst($b->status) ?></span>
                    </td>
                    <td>
                      <a href="<?= base_url('admin/booking/' . $b->id) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if (empty($bookingTerbaru)): ?>
                  <tr><td colspan="7" class="text-center text-muted py-4">Belum ada booking.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div><!-- End Left side columns -->

    <!-- Right side columns -->
    <div class="col-lg-4">

      <!-- Status Booking Pie Chart -->
      <div class="card">
        <div class="card-body pb-0">
          <h5 class="card-title">Status Booking <span>| Overview</span></h5>
          <div id="bookingStatusChart" style="min-height:300px;"></div>
        </div>
      </div>

      <!-- Layanan Terpopuler -->
      <div class="card">
        <div class="card-body pb-0">
          <h5 class="card-title">Layanan Terpopuler</h5>
          <div class="news">
            <?php foreach ($layananPopuler as $lp): ?>
            <div class="post-item clearfix d-flex align-items-center mb-3">
              <div class="flex-grow-1">
                <h6 class="mb-0"><?= esc($lp->nama_layanan) ?></h6>
                <div class="d-flex align-items-center mt-1">
                  <div class="progress flex-grow-1 me-2" style="height:6px;">
                    <?php
                      $maxPesan = $layananPopuler[0]->total_pesan ?? 1;
                      $pct = $maxPesan > 0 ? round(($lp->total_pesan / $maxPesan) * 100) : 0;
                    ?>
                    <div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div>
                  </div>
                  <span class="badge bg-primary"><?= $lp->total_pesan ?> pesanan</span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($layananPopuler)): ?>
              <p class="text-muted text-center py-3">Belum ada data.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Ringkasan Status</h5>
          <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
              <span><i class="bi bi-hourglass text-warning me-2"></i>Pending</span>
              <span class="badge bg-warning"><?= $bookingPending ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
              <span><i class="bi bi-play-circle text-primary me-2"></i>Proses</span>
              <span class="badge bg-primary"><?= $bookingProses ?></span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
              <span><i class="bi bi-check-circle text-success me-2"></i>Selesai</span>
              <span class="badge bg-success"><?= $bookingSelesai ?></span>
            </div>
          </div>
        </div>
      </div>

    </div><!-- End Right side columns -->

  </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('NiceAdmin/assets/vendor/apexcharts/apexcharts.min.js') ?>"></script>
<script>
// Chart data dari PHP
const chartDates = <?= json_encode(array_column($chartData, 'date')) ?>;
const chartTotals = <?= json_encode(array_column($chartData, 'total')) ?>;

// Revenue Line Chart
new ApexCharts(document.querySelector("#revenueChart"), {
  series: [{ name: 'Pendapatan (Rp)', data: chartTotals }],
  chart: { height: 250, type: 'area', toolbar: { show: false } },
  colors: ['#4154f1'],
  fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
  stroke: { curve: 'smooth', width: 2 },
  xaxis: { categories: chartDates },
  dataLabels: { enabled: false },
  yaxis: { labels: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } },
  tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } }
}).render();

// Booking Status Pie Chart
new ApexCharts(document.querySelector("#bookingStatusChart"), {
  series: [<?= $bookingPending ?>, <?= $bookingProses ?>, <?= $bookingSelesai ?>],
  chart: { type: 'donut', height: 300 },
  labels: ['Pending', 'Proses', 'Selesai'],
  colors: ['#ffc107', '#4154f1', '#2eca6a'],
  legend: { position: 'bottom' }
}).render();
</script>
<?= $this->endSection() ?>
