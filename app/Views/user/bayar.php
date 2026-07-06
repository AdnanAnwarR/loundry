<?= $this->extend('layout/main') ?>

<?= $this->section('page_title') ?>
Pembayaran Pesanan
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fs-4 mb-0 text-primary"><i class="bi bi-wallet2 me-2"></i>Konfirmasi Pembayaran</h5>
                    <span class="badge bg-danger fs-6 py-2 px-3"><?= esc($orderId) ?></span>
                </div>

                <!-- Detail Order -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Layanan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark"><?= esc($item->nama_layanan) ?></span>
                                        <small class="d-block text-muted">Harga: Rp <?= number_format($item->harga, 0, ',', '.') ?> / kg</small>
                                    </td>
                                    <td class="text-end fw-bold">Rp <?= number_format($item->total_harga, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-primary border-top border-dark">
                                <td class="fw-bold text-dark fs-5">Total yang Harus Dibayar:</td>
                                <td class="text-end fw-bold text-primary fs-5">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Informasi Jadwal & Catatan -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-calendar3 me-1"></i> Jadwal Pengantaran</h6>
                            <p class="mb-0 text-dark">
                                <strong>Tanggal:</strong> <?= date('d F Y', strtotime($jadwal->tanggal)) ?><br>
                                <strong>Waktu:</strong> Pukul <?= date('H:i', strtotime($jadwal->slot_waktu)) ?> WIB
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-chat-left-text me-1"></i> Catatan Pelanggan</h6>
                            <p class="mb-0 text-dark">
                                <?= esc(!empty($items) && isset($items[0]->catatan) ? str_replace("[Berat: ", "Berat: ", $items[0]->catatan) : '-') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert Pembatalan -->
                <div class="alert alert-warning border-warning d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                    <div>
                        <strong class="text-dark">PENTING!</strong> Sesuai ketentuan, pesanan yang <strong>sudah dibayar tidak dapat dibatalkan</strong> dengan alasan apa pun.
                    </div>
                </div>

                <!-- Form Konfirmasi Metode Pembayaran / Midtrans Snap -->
                <?php if (strpos($pembayaran->snap_token, 'SNAP-') === 0): ?>
                    <!-- Fallback Form Pembayaran Manual / Simulator -->
                    <div class="alert alert-info border-info-subtle mb-4 bg-light text-dark p-3 rounded">
                        <i class="bi bi-info-circle-fill me-2 text-info"></i> Kredensial Midtrans belum dikonfigurasi atau dinonaktifkan. Menggunakan mode Simulasi Pembayaran Manual.
                    </div>
                    <form action="<?= base_url('user/pesanan/proses-bayar/' . $orderId) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label for="metode_bayar" class="form-label fw-bold text-secondary">Pilih Metode Pembayaran (Simulasi)</label>
                            <select class="form-select form-select-lg border-primary" id="metode_bayar" name="metode_bayar" required>
                                <option value="Transfer Bank BCA">Transfer Bank (BCA) - Virtual Account</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank (Mandiri) - Virtual Account</option>
                                <option value="QRIS / E-Wallet">QRIS (Gopay, OVO, Dana, LinkAja)</option>
                                <option value="Tunai di Toko">Bayar Tunai di Counter Laundry</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('user') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                            <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow-sm"><i class="bi bi-credit-card me-1"></i> Bayar Sekarang</button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Pembayaran Resmi Menggunakan Midtrans Snap -->
                    <div class="text-center py-3 mb-4 bg-light border rounded">
                        <p class="text-dark fw-medium mb-3"><i class="bi bi-shield-check text-success me-1"></i> Keamanan pembayaran dijamin penuh oleh Midtrans.</p>
                        <div class="d-flex justify-content-between align-items-center px-3">
                            <a href="<?= base_url('user') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                            <button type="button" id="pay-button" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm"><i class="bi bi-credit-card-2-front me-2"></i> Bayar Sekarang</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (isset($pembayaran) && strpos($pembayaran->snap_token, 'SNAP-') !== 0): ?>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $clientKey ?>"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            // Trigger snap popup
            snap.pay('<?= $pembayaran->snap_token ?>', {
                onSuccess: function(result){
                    window.location.href = "<?= base_url('user/pesanan/proses-bayar/' . $orderId) ?>?status=success&result=" + encodeURIComponent(JSON.stringify(result));
                },
                onPending: function(result){
                    window.location.href = "<?= base_url('user/pesanan/proses-bayar/' . $orderId) ?>?status=pending&result=" + encodeURIComponent(JSON.stringify(result));
                },
                onError: function(result){
                    alert("Pembayaran gagal! Silakan coba beberapa saat lagi.");
                },
                onClose: function(){
                    alert('Anda menutup popup pembayaran sebelum menyelesaikan transaksi.');
                }
            });
        };
    </script>
<?php endif; ?>
<?= $this->endSection() ?>
