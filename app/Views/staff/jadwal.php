<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Jadwal Tugas Laundry
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="pagetitle">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/staff">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Tugas</li>
        </ol>
    </nav>
</div>

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

<section class="section">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="card-title text-dark fs-5 mb-4">Daftar Semua Pesanan Laundry yang Ditugaskan</h5>

            <!-- Tabel Tugas Staff -->
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
                                            // Memformat tanggal pengantaran agar lebih ramah bagi staff
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
</section>

<?= $this->endSection() ?>