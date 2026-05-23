<?php

if (!function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $status = strtolower(trim($status));

        $badges = [
            'pending'       => 'warning',
            'dikonfirmasi'  => 'info',
            'proses'        => 'primary',
            'selesai'       => 'success',
            'dibatalkan'    => 'secondary',
            'ditolak'       => 'danger',
        ];

        $badgeClass = $badges[$status] ?? 'dark';

        return '<span class="badge bg-' . $badgeClass . '">' 
                . esc(ucwords($status)) . 
               '</span>';
    }
}