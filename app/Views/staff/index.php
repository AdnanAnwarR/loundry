<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard Staff
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card sales-card">
      <div class="card-body">
        <h5 class="card-title">Pesanan Baru <span>| Belum Diproses</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-bell"></i>
          </div>
          <div class="ps-3">
            <h6>12</h6>
            <span class="text-muted small pt-2 ps-1">menunggu konfirmasi</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-4 col-md-4">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Sedang Diproses <span>| Hari Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-warning">
            <i class="bi bi-gear"></i>
          </div>
          <div class="ps-3">
            <h6>8</h6>
            <span class="text-muted small pt-2 ps-1">cucian sedang berjalan</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-4 col-md-4">
    <div class="card info-card customers-card">
      <div class="card-body">
        <h5 class="card-title">Selesai <span>| Hari Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-success">
            <i class="bi bi-check-circle"></i>
          </div>
          <div class="ps-3">
            <h6>24</h6>
            <span class="text-muted small pt-2 ps-1">siap diambil/dikirim</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
