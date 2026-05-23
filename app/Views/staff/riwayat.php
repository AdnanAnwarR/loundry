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
            <div class="d-flex mb-2">
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
                                <td>-</td>
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