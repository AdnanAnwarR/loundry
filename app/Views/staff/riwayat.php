<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Riwayat Pekerjaan Laundry
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="pagetitle">
    <!-- <h1>Jadwal Tugas Harian</h1> -->
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/staff">Dashboard</a></li>
            <li class="breadcrumb-item active">Riwayat Pekerjaan</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Riwayat Semua Pekerjaan Saya</h5>
            <div class="mb-4">
                <form method="get" action="<?= base_url('staff/riwayat-pekerjaan') ?>" class="row g-3 align-items-center">
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label small fw-bold text-muted mb-1">Filter Tanggal</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="<?= esc($tanggal) ?>">
                    </div>
                    <div class="col-md-4 col-sm-6 d-flex align-items-end gap-2" style="margin-top: 32px;">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="<?= base_url('staff/riwayat-pekerjaan') ?>" class="btn btn-secondary btn-sm fw-bold">Reset</a>
                    </div>
                </form>
            </div>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Tanggal</th>
                        <th>Slot</th>
                        <th>Status</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tugasRiwayat)) : ?>
                        <?php foreach ($tugasRiwayat as $key => $item) : ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= esc($item->pelanggan) ?></td>
                                <td><?= esc($item->layanan) ?></td>
                                <td><?= esc($item->tanggal) ?></td>
                                <td><?= esc(date('H:i', strtotime($item->slot_waktu))) /* Menampilkan jam slot waktu booking */ ?> WIB</td>
                                <td><?= status_badge($item->status) ?></td>
                                <td><?= esc($item->rating ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                Belum ada riwayat pekerjaan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="mt-3">
                <?= $pager->links() ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>