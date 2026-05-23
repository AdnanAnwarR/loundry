<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Jadwal Tugas Harian Laundry
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="pagetitle">
    <!-- <h1>Jadwal Tugas Harian</h1> -->
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/staff">Dashboard</a></li>
            <li class="breadcrumb-item active">Jadwal Tugas Harian</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <?php

                use CodeIgniter\I18n\Time;

                $tanggalFormat = Time::parse($tanggal ?? date('Y-m-d'))
                    ->toLocalizedString('EEEE, d MMMM yyyy');

            ?>
            <h5 class="card-title">Jadwal Tugas - <?= esc($tanggalFormat) ?></h5>
            <div class="d-flex mb-2">
                <form action="" method="get">
                    <div class="row g-2 align-items-end">

                        <div class="col-auto">
                            <label for="date" class="form-label">
                                Pilih Tanggal <span class="text-danger">*</span>
                            </label>

                            <input 
                                type="date" 
                                class="form-control" 
                                id="date" 
                                name="date"
                                value="<?= esc($tanggal ?? date('Y-m-d')) ?>"
                                required
                            >
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>

                    </div>
                </form>
            </div>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Jam</th>
                        <th>Tugas</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tugasHariIni)) : ?>
                        <?php foreach ($tugasHariIni as $key => $item) : ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= esc($item->jam) ?></td>
                                <td><?= esc($item->tugas) ?></td>
                                <td><?= esc($item->penanggung_jawab) ?></td>
                                <td><?= status_badge($item->status) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                Tidak ada pekerjaan untuk anda pada <?= esc($tanggalFormat) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="mt-3">
                <?= $pager->only(['date'])->links() ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>