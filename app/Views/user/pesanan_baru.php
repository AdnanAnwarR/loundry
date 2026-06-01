<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Buat Pesanan Laundry Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="card-title fs-4 mb-4 text-primary"><i class="bi bi-calendar-plus me-2"></i>Form Booking Laundry</h5>
                
                <!-- Form Booking Laundry -->
                <form action="<?= base_url('user/pesanan/store') ?>" method="post" id="form-booking">
                    <?= csrf_field() ?>

                    <!-- Step 1: Pilih Layanan -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary mb-3"><i class="bi bi-tags me-1"></i> 1. Pilih Layanan (Bisa pilih lebih dari satu)</label>
                        <?php if (empty($layanan)): ?>
                            <div class="alert alert-warning">Tidak ada layanan aktif saat ini. Silakan hubungi admin.</div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($layanan as $item): ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border p-3 service-card" style="cursor: pointer; transition: all 0.2s;" onclick="toggleCheckbox('layanan_<?= $item->id ?>')">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input service-checkbox me-3" type="checkbox" name="layanan_ids[]" id="layanan_<?= $item->id ?>" value="<?= $item->id ?>" data-harga="<?= $item->harga ?>" data-nama="<?= esc($item->nama_layanan) ?>" onclick="event.stopPropagation(); updateEstimasiHarga();">
                                                <div class="form-check-label w-100">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold text-dark"><?= esc($item->nama_layanan) ?></span>
                                                        <span class="badge bg-light text-primary border border-primary">Rp <?= number_format($item->harga, 0, ',', '.') ?> / kg</span>
                                                    </div>
                                                    <small class="text-muted d-block mt-1"><?= esc($item->deskripsi) ?></small>
                                                    <small class="text-muted d-block mt-1"><i class="bi bi-clock me-1"></i> Durasi estimasi: <?= esc($item->durasi) ?> menit</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Step 2: Berat Pakaian -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="berat" class="form-label fw-bold text-secondary"><i class="bi bi-speedometer2 me-1"></i> 2. Total Berat Pakaian (Kg)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="berat" name="berat" min="0.5" step="0.1" value="1.0" required oninput="updateEstimasiHarga();">
                                <span class="input-group-text">Kg</span>
                            </div>
                            <small class="text-muted">Masukkan perkiraan berat pakaian Anda. Setiap layanan dihitung per kilo.</small>
                        </div>

                        <!-- Step 3: Waktu Booking -->
                        <div class="col-md-6">
                            <label for="jadwal_id" class="form-label fw-bold text-secondary"><i class="bi bi-clock-history me-1"></i> 3. Pilih Jadwal Booking</label>
                            <select class="form-select" id="jadwal_id" name="jadwal_id" required>
                                <option value="" disabled selected>-- Pilih Hari & Waktu Pengantaran --</option>
                                <?php if (empty($jadwal)): ?>
                                    <option value="" disabled>Jadwal penuh atau belum tersedia.</option>
                                <?php else: ?>
                                    <?php foreach ($jadwal as $slot): ?>
                                        <?php 
                                            // Format tanggal agar lebih mudah dibaca
                                            $tgl = date('d M Y', strtotime($slot->tanggal));
                                            $sisa = $slot->kapasitas - $slot->terisi;
                                        ?>
                                        <option value="<?= $slot->id ?>">
                                            <?= $tgl ?> - Pukul <?= date('H:i', strtotime($slot->slot_waktu)) ?> (Sisa Slot: <?= $sisa ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Jadwal yang ditampilkan ditentukan oleh admin laundry.</small>
                        </div>
                    </div>

                    <!-- Step 4: Catatan Tambahan -->
                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-bold text-secondary"><i class="bi bi-chat-left-dots me-1"></i> 4. Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Contoh: Tolong dipisah pakaian putih, ada noda tinta di kemeja biru, dll."></textarea>
                    </div>

                    <!-- Ringkasan Estimasi Harga -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-calculator me-1"></i> Ringkasan Estimasi Pesanan</h6>
                            <div id="estimasi-detail" class="mb-2">
                                <p class="text-muted mb-0">Silakan pilih layanan dan masukkan berat pakaian untuk melihat ringkasan.</p>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 fw-bold">Estimasi Total Harga:</span>
                                <span class="fs-4 fw-bold text-primary" id="estimasi-total">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('user') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-check-circle me-1"></i> Konfirmasi Booking</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Fungsi untuk toggle checkbox saat card di-klik
    function toggleCheckbox(checkboxId) {
        const checkbox = document.getElementById(checkboxId);
        checkbox.checked = !checkbox.checked;
        
        // Dapatkan elemen card pembungkus
        const card = checkbox.closest('.service-card');
        if (checkbox.checked) {
            card.classList.add('border-primary', 'bg-light');
        } else {
            card.classList.remove('border-primary', 'bg-light');
        }
        
        updateEstimasiHarga();
    }

    // Fungsi untuk memperbarui harga estimasi secara real-time
    function updateEstimasiHarga() {
        const checkboxes = document.querySelectorAll('.service-checkbox');
        const beratInput = document.getElementById('berat');
        const berat = parseFloat(beratInput.value) || 0;
        
        let total = 0;
        let detailHtml = '<ul class="list-group list-group-flush bg-transparent">';
        let selectedCount = 0;

        checkboxes.forEach(cb => {
            const card = cb.closest('.service-card');
            if (cb.checked) {
                card.classList.add('border-primary', 'bg-light');
                const harga = parseFloat(cb.getAttribute('data-harga')) || 0;
                const nama = cb.getAttribute('data-nama');
                const subtotal = harga * berat;
                total += subtotal;
                selectedCount++;

                detailHtml += `
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-1 border-0">
                        <span><i class="bi bi-check2 text-success me-2"></i>${nama} (${berat} kg x Rp ${formatRupiah(harga)})</span>
                        <span class="fw-bold">Rp ${formatRupiah(subtotal)}</span>
                    </li>
                `;
            } else {
                card.classList.remove('border-primary', 'bg-light');
            }
        });

        detailHtml += '</ul>';

        if (selectedCount === 0) {
            document.getElementById('estimasi-detail').innerHTML = '<p class="text-muted mb-0">Silakan pilih layanan dan masukkan berat pakaian untuk melihat ringkasan.</p>';
            document.getElementById('estimasi-total').innerText = 'Rp 0';
        } else {
            document.getElementById('estimasi-detail').innerHTML = detailHtml;
            document.getElementById('estimasi-total').innerText = 'Rp ' + formatRupiah(total);
        }
    }

    // Helper untuk memformat angka ke rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Inisialisasi awal saat halaman diload
    document.addEventListener('DOMContentLoaded', () => {
        updateEstimasiHarga();
    });
</script>
<?= $this->endSection() ?>
