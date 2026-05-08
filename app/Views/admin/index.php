<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <!-- Sales Card -->
  <div class="col-xxl-4 col-md-6">
    <div class="card info-card sales-card">
      <div class="card-body">
        <h5 class="card-title">Total Pesanan <span>| Hari Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-cart"></i>
          </div>
          <div class="ps-3">
            <h6>145</h6>
            <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">peningkatan</span>
          </div>
        </div>
      </div>
    </div>
  </div><!-- End Sales Card -->

  <!-- Revenue Card -->
  <div class="col-xxl-4 col-md-6">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Pendapatan <span>| Bulan Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-currency-dollar"></i>
          </div>
          <div class="ps-3">
            <h6>Rp 3.200.000</h6>
            <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">peningkatan</span>
          </div>
        </div>
      </div>
    </div>
  </div><!-- End Revenue Card -->

  <!-- Customers Card -->
  <div class="col-xxl-4 col-xl-12">
    <div class="card info-card customers-card">
      <div class="card-body">
        <h5 class="card-title">Pelanggan <span>| Total</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-people"></i>
          </div>
          <div class="ps-3">
            <h6>1,244</h6>
            <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">penurunan</span>
          </div>
        </div>
      </div>
    </div>
  </div><!-- End Customers Card -->
</div>
<?= $this->endSection() ?>
