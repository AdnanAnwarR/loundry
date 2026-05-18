<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\LayananModel;
use App\Models\JadwalModel;
use App\Models\PesananModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $layananModel;
    protected $jadwalModel;
    protected $pesananModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->layananModel = new LayananModel();
        $this->jadwalModel  = new JadwalModel();
        $this->pesananModel = new PesananModel();
    }

    // ============================================================
    // DASHBOARD
    // ============================================================
    public function dashboard()
    {
        $db = \Config\Database::connect();

        // Statistik booking
        $totalBooking    = $this->pesananModel->countAll();
        $bookingPending  = $this->pesananModel->where('status', 'pending')->countAllResults();
        $bookingProses   = $this->pesananModel->where('status', 'proses')->countAllResults();
        $bookingSelesai  = $this->pesananModel->where('status', 'selesai')->countAllResults();

        // Total pendapatan (booking selesai + sudah dibayar)
        $pendapatan = $db->table('pesanan')
            ->selectSum('total_harga')
            ->where('status', 'selesai')
            ->where('status_pembayaran', 'sudah_dibayar')
            ->get()->getRow();

        // Total pelanggan aktif
        $totalPelanggan = $this->userModel->where('role', 'pelanggan')->where('is_active', 1)->countAllResults();

        // Layanan terpopuler (top 5)
        $layananPopuler = $db->table('pesanan p')
            ->select('l.nama_layanan, COUNT(p.id) as total_pesan')
            ->join('layanan l', 'l.id = p.layanan_id')
            ->groupBy('p.layanan_id')
            ->orderBy('total_pesan', 'DESC')
            ->limit(5)
            ->get()->getResultObject();

        // Booking terbaru (10 terakhir)
        $bookingTerbaru = $db->table('pesanan p')
            ->select('p.*, u.name as nama_pelanggan, l.nama_layanan, j.tanggal, j.slot_waktu')
            ->join('users u', 'u.id = p.user_id')
            ->join('layanan l', 'l.id = p.layanan_id')
            ->join('jadwal j', 'j.id = p.jadwal_id')
            ->orderBy('p.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultObject();

        // Data chart pendapatan 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $total = $db->table('pesanan')
                ->selectSum('total_harga')
                ->where('DATE(created_at)', $date)
                ->where('status_pembayaran', 'sudah_dibayar')
                ->get()->getRow();
            $chartData[] = [
                'date'  => date('d M', strtotime($date)),
                'total' => $total->total_harga ?? 0
            ];
        }

        return view('admin/dashboard', [
            'title'          => 'Dashboard Admin',
            'totalBooking'   => $totalBooking,
            'bookingPending' => $bookingPending,
            'bookingProses'  => $bookingProses,
            'bookingSelesai' => $bookingSelesai,
            'pendapatan'     => $pendapatan->total_harga ?? 0,
            'totalPelanggan' => $totalPelanggan,
            'layananPopuler' => $layananPopuler,
            'bookingTerbaru' => $bookingTerbaru,
            'chartData'      => $chartData,
        ]);
    }

    // ============================================================
    // KELOLA LAYANAN
    // ============================================================
    public function layananIndex()
    {
        $layanan = $this->layananModel->orderBy('created_at', 'DESC')->findAll();
        return view('admin/layanan/index', [
            'title'   => 'Kelola Layanan',
            'layanan' => $layanan,
        ]);
    }

    public function layananCreate()
    {
        return view('admin/layanan/create', ['title' => 'Tambah Layanan']);
    }

    public function layananStore()
    {
        $rules = [
            'nama_layanan' => 'required|min_length[3]',
            'harga'        => 'required|numeric',
            'durasi'       => 'required|integer',
            'deskripsi'    => 'permit_empty',
            'foto'         => 'permit_empty|is_image[foto]|max_size[foto,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fotoName = null;
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/layanan/', $fotoName);
        }

        $this->layananModel->save([
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'harga'        => $this->request->getPost('harga'),
            'durasi'       => $this->request->getPost('durasi'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'foto'         => $fotoName,
            'is_active'    => 1,
        ]);

        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function layananEdit($id)
    {
        $layanan = $this->layananModel->find($id);
        if (!$layanan) return redirect()->to('/admin/layanan')->with('error', 'Layanan tidak ditemukan.');
        return view('admin/layanan/edit', ['title' => 'Edit Layanan', 'layanan' => $layanan]);
    }

    public function layananUpdate($id)
    {
        $layanan = $this->layananModel->find($id);
        if (!$layanan) return redirect()->to('/admin/layanan')->with('error', 'Layanan tidak ditemukan.');

        $rules = [
            'nama_layanan' => 'required|min_length[3]',
            'harga'        => 'required|numeric',
            'durasi'       => 'required|integer',
            'foto'         => 'permit_empty|is_image[foto]|max_size[foto,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'harga'        => $this->request->getPost('harga'),
            'durasi'       => $this->request->getPost('durasi'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'is_active'    => $this->request->getPost('is_active') ?? 1,
        ];

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama
            if ($layanan->foto && file_exists(FCPATH . 'uploads/layanan/' . $layanan->foto)) {
                unlink(FCPATH . 'uploads/layanan/' . $layanan->foto);
            }
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/layanan/', $fotoName);
            $data['foto'] = $fotoName;
        }

        $this->layananModel->update($id, $data);
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function layananDelete($id)
    {
        $layanan = $this->layananModel->find($id);
        if ($layanan && $layanan->foto && file_exists(FCPATH . 'uploads/layanan/' . $layanan->foto)) {
            unlink(FCPATH . 'uploads/layanan/' . $layanan->foto);
        }
        $this->layananModel->delete($id);
        return redirect()->to('/admin/layanan')->with('success', 'Layanan berhasil dihapus!');
    }

    public function layananToggle($id)
    {
        $layanan = $this->layananModel->find($id);
        if ($layanan) {
            $this->layananModel->update($id, ['is_active' => $layanan->is_active ? 0 : 1]);
        }
        return redirect()->to('/admin/layanan')->with('success', 'Status layanan diperbarui.');
    }

    // ============================================================
    // KELOLA JADWAL
    // ============================================================
    public function jadwalIndex()
    {
        $jadwal = $this->jadwalModel->orderBy('tanggal', 'ASC')->orderBy('slot_waktu', 'ASC')->findAll();
        return view('admin/jadwal/index', ['title' => 'Kelola Jadwal', 'jadwal' => $jadwal]);
    }

    public function jadwalCreate()
    {
        return view('admin/jadwal/create', ['title' => 'Tambah Jadwal']);
    }

    public function jadwalStore()
    {
        $rules = [
            'tanggal'    => 'required|valid_date',
            'slot_waktu' => 'required',
            'kapasitas'  => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jadwalModel->save([
            'tanggal'    => $this->request->getPost('tanggal'),
            'slot_waktu' => $this->request->getPost('slot_waktu'),
            'kapasitas'  => $this->request->getPost('kapasitas'),
            'terisi'     => 0,
        ]);

        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function jadwalEdit($id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) return redirect()->to('/admin/jadwal')->with('error', 'Jadwal tidak ditemukan.');
        return view('admin/jadwal/edit', ['title' => 'Edit Jadwal', 'jadwal' => $jadwal]);
    }

    public function jadwalUpdate($id)
    {
        $rules = [
            'tanggal'    => 'required|valid_date',
            'slot_waktu' => 'required',
            'kapasitas'  => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jadwalModel->update($id, [
            'tanggal'    => $this->request->getPost('tanggal'),
            'slot_waktu' => $this->request->getPost('slot_waktu'),
            'kapasitas'  => $this->request->getPost('kapasitas'),
        ]);

        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function jadwalDelete($id)
    {
        $this->jadwalModel->delete($id);
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil dihapus!');
    }

    // ============================================================
    // KELOLA BOOKING
    // ============================================================
    public function bookingIndex()
    {
        $db = \Config\Database::connect();

        $statusFilter  = $this->request->getGet('status');
        $tanggalFilter = $this->request->getGet('tanggal');
        $layananFilter = $this->request->getGet('layanan_id');

        $builder = $db->table('pesanan p')
            ->select('p.*, u.name as nama_pelanggan, l.nama_layanan, j.tanggal, j.slot_waktu, s.name as nama_staff')
            ->join('users u', 'u.id = p.user_id')
            ->join('layanan l', 'l.id = p.layanan_id')
            ->join('jadwal j', 'j.id = p.jadwal_id')
            ->join('users s', 's.id = p.staf_id', 'left')
            ->orderBy('p.created_at', 'DESC');

        if ($statusFilter) $builder->where('p.status', $statusFilter);
        if ($tanggalFilter) $builder->where('j.tanggal', $tanggalFilter);
        if ($layananFilter) $builder->where('p.layanan_id', $layananFilter);

        $booking  = $builder->get()->getResultObject();
        $layanan  = $this->layananModel->findAll();
        $allStaff = $this->userModel->where('role', 'staff')->where('is_active', 1)->findAll();

        return view('admin/booking/index', [
            'title'         => 'Kelola Booking',
            'booking'       => $booking,
            'layanan'       => $layanan,
            'statusFilter'  => $statusFilter,
            'tanggalFilter' => $tanggalFilter,
            'layananFilter' => $layananFilter,
        ]);
    }

    public function bookingShow($id)
    {
        $db = \Config\Database::connect();
        $booking = $db->table('pesanan p')
            ->select('p.*, u.name as nama_pelanggan, u.email, u.no_hp, l.nama_layanan, l.harga, j.tanggal, j.slot_waktu, s.name as nama_staff')
            ->join('users u', 'u.id = p.user_id')
            ->join('layanan l', 'l.id = p.layanan_id')
            ->join('jadwal j', 'j.id = p.jadwal_id')
            ->join('users s', 's.id = p.staf_id', 'left')
            ->where('p.id', $id)
            ->get()->getRow();

        if (!$booking) return redirect()->to('/admin/booking')->with('error', 'Booking tidak ditemukan.');

        $allStaff = $this->userModel->where('role', 'staff')->where('is_active', 1)->findAll();

        return view('admin/booking/show', [
            'title'    => 'Detail Booking',
            'booking'  => $booking,
            'allStaff' => $allStaff,
        ]);
    }

    public function bookingKonfirmasi($id)
    {
        $this->pesananModel->update($id, ['status' => 'dikonfirmasi']);
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Booking berhasil dikonfirmasi!');
    }

    public function bookingTolak($id)
    {
        $alasan = $this->request->getPost('alasan') ?? 'Ditolak oleh admin.';
        $this->pesananModel->update($id, ['status' => 'ditolak', 'catatan' => $alasan]);
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Booking telah ditolak.');
    }

    public function bookingAssignStaff($id)
    {
        $stafId = $this->request->getPost('staf_id');
        $this->pesananModel->update($id, ['staf_id' => $stafId, 'status' => 'proses']);
        return redirect()->to('/admin/booking/' . $id)->with('success', 'Staff berhasil di-assign dan status diubah ke Proses!');
    }

    // ============================================================
    // KELOLA STAFF
    // ============================================================
    public function staffIndex()
    {
        $staff = $this->userModel->where('role', 'staff')->orderBy('created_at', 'DESC')->findAll();
        return view('admin/staff/index', ['title' => 'Kelola Staff', 'staff' => $staff]);
    }

    public function staffCreate()
    {
        return view('admin/staff/create', ['title' => 'Tambah Staff']);
    }

    public function staffStore()
    {
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'no_hp'    => 'required|min_length[10]|is_unique[users.no_hp]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->save([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'password'  => $this->request->getPost('password'),
            'role'      => 'staff',
            'is_active' => 1,
        ]);

        return redirect()->to('/admin/staff')->with('success', 'Staff berhasil ditambahkan!');
    }

    public function staffEdit($id)
    {
        $staff = $this->userModel->find($id);
        if (!$staff || $staff->role !== 'staff') return redirect()->to('/admin/staff')->with('error', 'Staff tidak ditemukan.');
        return view('admin/staff/edit', ['title' => 'Edit Staff', 'staff' => $staff]);
    }

    public function staffUpdate($id)
    {
        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,$id]",
            'no_hp' => "required|min_length[10]|is_unique[users.no_hp,id,$id]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ];

        $newPwd = $this->request->getPost('password');
        if ($newPwd) $data['password'] = $newPwd;

        $this->userModel->update($id, $data);
        return redirect()->to('/admin/staff')->with('success', 'Data staff berhasil diperbarui!');
    }

    public function staffDelete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/staff')->with('success', 'Staff berhasil dihapus.');
    }

    // ============================================================
    // MANAJEMEN USER
    // ============================================================
    public function usersIndex()
    {
        $users = $this->userModel->where('role', 'pelanggan')->orderBy('created_at', 'DESC')->findAll();
        return view('admin/users/index', ['title' => 'Manajemen User', 'users' => $users]);
    }

    public function usersToggle($id)
    {
        $user = $this->userModel->find($id);
        if ($user) {
            $this->userModel->update($id, ['is_active' => $user->is_active ? 0 : 1]);
        }
        return redirect()->to('/admin/users')->with('success', 'Status user diperbarui.');
    }

    public function usersDelete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus.');
    }
}
