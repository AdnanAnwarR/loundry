<?php

/**
 * Simulasi Pengiriman Pesan WhatsApp ke Pelanggan
 * 
 * Fungsi ini mencatat pengiriman WhatsApp ke file log simulasi
 * dan menyimpan statusnya di session untuk ditampilkan sebagai
 * alert simulasi di halaman web.
 * 
 * @param string $no_hp Nomor HP penerima
 * @param string $message Isi pesan yang dikirim
 * @return bool
 */
function kirim_wa($no_hp, $message)
{
    // Mengambil instance session CodeIgniter
    $session = session();
    
    // Format timestamp untuk pencatatan log
    $timestamp = date('Y-m-d H:i:s');
    
    // Susun isi baris log simulasi
    $log_entry = "[{$timestamp}] WA KE: {$no_hp} | PESAN: {$message}" . PHP_EOL;
    
    // Tentukan path penyimpanan file log simulasi WhatsApp di direktori writable/logs/
    $log_file = WRITEPATH . 'logs/whatsapp_simulation.log';
    
    // Tulis/tambahkan log simulasi ke file log
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Simpan pesan simulasi ke session agar bisa ditampilkan di UI sebagai notifikasi pop-up
    $simulation_alerts = $session->get('wa_simulation_alerts') ?? [];
    $simulation_alerts[] = [
        'no_hp'   => $no_hp,
        'message' => $message,
        'time'    => date('H:i')
    ];
    $session->set('wa_simulation_alerts', $simulation_alerts);
    
    // Mengembalikan nilai true sebagai tanda simulasi berhasil diproses
    return true;
}
