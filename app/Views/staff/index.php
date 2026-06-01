<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard Staff
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <!-- Card Tugas Baru / Belum Diambil -->
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card sales-card">
      <div class="card-body">
        <h5 class="card-title">Tugas Baru <span>| Belum Diambil</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
            <i class="bi bi-bell"></i>
          </div>
          <div class="ps-3">
            <h6><?= esc($pesananBaru) ?></h6>
            <span class="text-muted small pt-2 ps-1">tugas perlu diambil</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Sedang Diproses -->
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card revenue-card">
      <div class="card-body">
        <h5 class="card-title">Sedang Diproses <span>| Saat Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-warning">
            <i class="bi bi-gear"></i>
          </div>
          <div class="ps-3">
            <h6><?= esc($sedangDiproses) ?></h6>
            <span class="text-muted small pt-2 ps-1">laundry sedang dikerjakan</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Selesai -->
  <div class="col-xxl-4 col-md-4">
    <div class="card info-card customers-card">
      <div class="card-body">
        <h5 class="card-title">Selesai <span>| Total</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-success">
            <i class="bi bi-check-circle"></i>
          </div>
          <div class="ps-3">
            <h6><?= esc($selesai) ?></h6>
            <span class="text-muted small pt-2 ps-1">laundry telah diselesaikan</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
