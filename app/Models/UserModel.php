<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    
    // Di CI4, fitur hidden attributes, casts yang canggih (seperti isAdmin),
    // sangat dianjurkan menggunakan 'Entity' class. Tapi untuk kemudahan
    // transisi, kita pakai object biasa dulu.
    protected $returnType       = 'object'; 
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'name',
        'email',
        'no_hp',
        'role',
        'is_active',
        'foto',
        'password',
        'email_verified_at',
        'remember_token'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Pengganti 'casts => password => hashed' di Laravel.
    // CI4 mengeksekusi fungsi callback ini sebelum proses insert dan update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    // Pengganti relasi hasMany Pesanan (sebagai Customer)
    public function getPesanan($userId)
    {
        $pesananModel = new PesananModel();
        return $pesananModel->where('user_id', $userId)->findAll();
    }

    // Pengganti relasi hasMany Pekerjaan (sebagai Staff)
    public function getPekerjaan($stafId)
    {
        $pesananModel = new PesananModel();
        return $pesananModel->where('staf_id', $stafId)->findAll();
    }

    // -------------------------------------------------------------
    // CATATAN PENTING: 
    // -------------------------------------------------------------
    // Fungsi-fungsi pengecekan baris seperti isAdmin(), isStaff(), isPelanggan() 
    // di CI4 IDEALNYA diletakkan di dalam class Entity (App\Entities\User).
    // Karena CI4 Model merepresentasikan tabel secara utuh, bukan baris data (Active Record seperti Eloquent).
    //
    // Jika Anda memakai returnType = 'object', pemanggilan bisa dilakukan dengan mengecek propertinya secara langsung di view/controller:
    // Contoh: if ($user->role === 'admin') { ... }
}
