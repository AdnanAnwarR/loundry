<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard Pelanggan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Selamat Datang di Sistem Laundry Kami!</h5>
        <p>Anda dapat melihat status pesanan Anda, memesan layanan baru, dan mengelola riwayat pesanan Anda di sini.</p>
        <a href="<?= base_url('user/pesanan/baru') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Pesanan Baru</a>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="card recent-sales overflow-auto">
      <div class="card-body">
        <h5 class="card-title">Pesanan Aktif <span>| Sedang Diproses</span></h5>
        <table class="table table-borderless datatable">
          <thead>
            <tr>
              <th scope="col"># ID</th>
              <th scope="col">Layanan</th>
              <th scope="col">Total Harga</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row"><a href="#">#ORD-001</a></th>
              <td>Cuci Kering + Setrika</td>
              <td>Rp 45.000</td>
              <td><span class="badge bg-warning">Proses</span></td>
            </tr>
            <tr>
              <th scope="row"><a href="#">#ORD-002</a></th>
              <td>Cuci Selimut Besar</td>
              <td>Rp 60.000</td>
              <td><span class="badge bg-info">Dikonfirmasi</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Total Pengeluaran <span>| Bulan Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-wallet2"></i>
          </div>
          <div class="ps-3">
            <h6>Rp 105.000</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
