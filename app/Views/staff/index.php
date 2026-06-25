<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Dashboard Staff
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- WhatsApp Simulation Alerts -->
<?php if (session()->get('wa_simulation_alerts')): ?>
    <?php 
        $waAlerts = session()->get('wa_simulation_alerts'); 
        // Hapus alerts setelah dibaca agar tidak muncul berulang kali
        session()->remove('wa_simulation_alerts');
    ?>
    <?php foreach ($waAlerts as $alert): ?>
        <div class="alert alert-info border-info border-start border-4 shadow-sm mb-3" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-whatsapp me-2 text-success"></i> [SIMULASI WHATSAPP] Notifikasi Terkirim Otomatis!</h6>
            <p class="mb-0 text-dark"><strong>Nomor HP Pelanggan:</strong> <?= esc($alert['no_hp']) ?></p>
            <p class="mb-0 text-muted"><strong>Isi Pesan:</strong> "<?= esc($alert['message']) ?>"</p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="row">
  <!-- Card Tugas Baru / Belum Diambil -->
  <div class="col-xxl-4 col-md-4 mb-4">
    <div class="card info-card sales-card h-100 shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title">Tugas Baru <span>| Belum Diambil</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-light text-warning" style="width: 48px; height: 48px; background-color: #fff3cd;">
            <i class="bi bi-bell"></i>
          </div>
          <div class="ps-3">
            <h6 class="mb-0 fs-4 fw-bold"><?= esc($pesananBaru) ?></h6>
            <span class="text-muted small pt-2 ps-1">tugas perlu diambil</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Sedang Diproses -->
  <div class="col-xxl-4 col-md-4 mb-4">
    <div class="card info-card revenue-card h-100 shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title">Sedang Diproses <span>| Saat Ini</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary-light" style="width: 48px; height: 48px; background-color: #efe8ff; color: #4154f1;">
            <i class="bi bi-gear"></i>
          </div>
          <div class="ps-3">
            <h6 class="mb-0 fs-4 fw-bold"><?= esc($sedangDiproses) ?></h6>
            <span class="text-muted small pt-2 ps-1">laundry sedang dikerjakan</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Selesai -->
  <div class="col-xxl-4 col-md-4 mb-4">
    <div class="card info-card customers-card h-100 shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title">Selesai <span>| Total</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center text-success bg-success-light" style="width: 48px; height: 48px; background-color: #e8f5e9;">
            <i class="bi bi-check-circle"></i>
          </div>
          <div class="ps-3">
            <h6 class="mb-0 fs-4 fw-bold"><?= esc($selesai) ?></h6>
            <span class="text-muted small pt-2 ps-1">laundry telah diselesaikan</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tampilan Semua Tugas Yang Masuk Dari Admin -->
<div class="row mt-2">
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="card-title text-dark fs-5 mb-4"><i class="bi bi-list-task me-2 text-primary"></i>Tugas Masuk Dari Admin</h5>

        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th style="width: 50px;">No</th>
                <th>Order ID</th>
                <th>Jadwal Pengantaran</th>
                <th>Layanan Laundry</th>
                <th>Nama Pelanggan</th>
                <th>Catatan Laundry</th>
                <th>Status Pengerjaan</th>
                <th class="text-center" style="width: 180px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($tugasStaff)) : ?>
                <?php 
                   $currentPage = $pager->getCurrentPage();
                   $perPage = 10;
                   $startNumber = ($currentPage - 1) * $perPage;
                   foreach ($tugasStaff as $key => $item) : 
                ?>
                  <tr>
                    <td><?= $startNumber + $key + 1 ?></td>
                    <td><span class="badge bg-secondary py-2 px-2"><?= esc($item->order_id) ?></span></td>
                    <td>
                      <?php
                        $tglObj = \CodeIgniter\I18n\Time::parse($item->tanggal_booking);
                        $formattedTgl = $tglObj->toLocalizedString('d MMMM yyyy');
                      ?>
                      <i class="bi bi-calendar-event me-1 text-primary"></i> <?= esc($formattedTgl) ?>
                      <small class="d-block text-muted mt-1"><i class="bi bi-clock"></i> <?= esc(date('H:i', strtotime($item->jam))) ?> WIB</small>
                    </td>
                    <td class="fw-bold text-dark"><?= esc($item->tugas) ?></td>
                    <td>
                      <span class="text-dark fw-semibold"><?= esc($item->nama_pelanggan) ?></span>
                      <small class="d-block text-muted"><i class="bi bi-telephone"></i> <?= esc($item->no_hp_pelanggan) ?></small>
                    </td>
                    <td><small class="text-secondary"><?= esc($item->catatan_pesanan ?? '-') ?></small></td>
                    <td>
                      <?php 
                        $status = strtolower($item->status);
                        if ($status === 'proses') {
                            echo '<span class="badge bg-primary text-white"><i class="bi bi-gear-fill me-1"></i>Sedang Dikerjakan</span>';
                        } elseif ($status === 'selesai') {
                            echo '<span class="badge bg-success text-white"><i class="bi bi-check-all me-1"></i>Selesai</span>';
                        } elseif ($status === 'dikonfirmasi') {
                            echo '<span class="badge bg-warning text-dark"><i class="bi bi-pin-angle me-1"></i>Belum Diambil</span>';
                        } else {
                            echo '<span class="badge bg-secondary text-dark">' . esc($item->status) . '</span>';
                        }
                      ?>
                    </td>
                    <td class="text-center">
                      <?php if ($status === 'dikonfirmasi') : ?>
                        <!-- Tombol Ambil Pekerjaan -->
                        <form action="<?= base_url('staff/tugas/ambil/' . $item->order_id) ?>" method="post" onsubmit="return confirm('Ambil dan kerjakan pesanan laundry ini?')">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn btn-sm btn-info fw-bold text-white px-3">
                            <i class="bi bi-hand-index-thumb me-1"></i> Ambil
                          </button>
                        </form>
                      <?php elseif ($status === 'proses') : ?>
                        <!-- Tombol Selesaikan Pekerjaan -->
                        <form action="<?= base_url('staff/tugas/selesai/' . $item->order_id) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin laundry order ini telah SELESAI?')">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                            <i class="bi bi-check-circle me-1"></i> Selesai
                          </button>
                        </form>
                      <?php elseif ($status === 'selesai') : ?>
                        <span class="text-success"><i class="bi bi-check2-all me-1"></i>Selesai</span>
                      <?php else : ?>
                        <span class="text-secondary">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    Belum ada pesanan laundry yang ditugaskan oleh admin untuk Anda.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          <?= $pager->links() ?>
        </div>

      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
