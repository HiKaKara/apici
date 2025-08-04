<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\OvertimeSubmissionModel;

class AdminController extends ResourceController{
    protected $format = 'json';

    public function getAllEmployees(){
        $userModel = new UserModel();
        // $adminId = $this->request->user->id; // Contoh jika menggunakan JWT
        // $employees = $userModel->where('id !=', $adminId)->findAll();
        $employees = $userModel->findAll();
        return $this->respond($employees);
    }

    public function updateEmployee($id = null){
        $userModel = new UserModel();
        $data = $this->request->getJSON(true);

        if (!$userModel->find($id)) {
            return $this->failNotFound('User tidak ditemukan.');
        }

        // Validasi
        $rules = [
            'name' => 'required',
            'role' => 'required|in_list[admin,pegawai]'
        ];

        // Password hanya diupdate jika diisi
        if (!empty($data['password'])) {
            $rules['password'] = 'min_length[6]';
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            // Hapus password dari data update jika kosong
            unset($data['password']);
        }

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 400);
        }

        if ($userModel->update($id, $data)) {
            return $this->respondUpdated(['status' => 'success', 'message' => 'Data pegawai berhasil diperbarui.']);
        }

        return $this->fail($userModel->errors());
    }

    public function createEmployee(){
    $model = new UserModel();

    // 1. Ambil data JSON dari request.
    // Menggunakan getJSON(true) untuk mendapatkan array asosiatif.
    $data = $this->request->getJSON(true);
    
    if (empty($data)) {
        return $this->fail('Data JSON yang dikirim kosong atau tidak valid.', 400);
    }

    // 2. Definisikan aturan validasi.
    $rules = [
        'name'     => 'required',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,pegawai]',
        'position' => 'required',
    ];

    // 3. Lakukan validasi terhadap array data menggunakan validateData().
    if (!$this->validateData($data, $rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = array_shift($errors);
            return $this->fail($errorMessage, 400);
        }

    if ($model->insert($data)) {
            // PERBAIKAN 3: Standardisasi pesan sukses.
            return $this->respondCreated([
                'status'  => 201,
                'message' => 'Pegawai berhasil ditambahkan'
            ]);
        }

    // 4. Simpan data ke database.
    if ($model->insert($data)) {
        return $this->respondCreated(['status' => 201, 'messages' => ['success' => 'Pegawai berhasil ditambahkan']]);
    }

    // Jika insert gagal, kembalikan error dari model untuk pesan yang lebih spesifik.
    return $this->fail($model->errors() ?? 'Gagal menyimpan data ke database.', 500);
    }
    public function getAllAttendanceHistory(){
        $model = new AttendanceModel();
        $history = $model
            ->select('attendances.*, users.name')
            ->join('users', 'users.id = attendances.user_id')
            ->orderBy('attendance_date', 'DESC')
            ->findAll();
            
        return $this->respond($history);
    }
    public function getAllOvertimeHistory(){
        $model = new OvertimeSubmissionModel();
        $history = $model
            ->select('overtime_submissions.*, users.name')
            ->join('users', 'users.id = overtime_submissions.user_id')
            ->orderBy('start_date', 'DESC')
            ->findAll();

        return $this->respond($history);
    }
    public function dashboardSummary(){
        date_default_timezone_set('Asia/Jakarta');
        $db = \Config\Database::connect();

        // 1. Ambil Total Presensi per User
        $attendanceCounts = $db->table('attendances')
            ->select('user_id, users.name, COUNT(attendances.id) as total_attendance')
            ->join('users', 'users.id = attendances.user_id')
            ->groupBy('user_id, users.name')
            ->orderBy('total_attendance', 'DESC')
            ->get()->getResultArray();

        // 2. Ambil Checklist Checkout Hari Ini
        $today = date('Y-m-d');
        $todayChecklists = $db->table('attendances')
            ->select('user_id, users.name, checkout_checklist, time_out')
            ->join('users', 'users.id = attendances.user_id')
            ->where('attendance_date', $today)
            ->where('checkout_checklist IS NOT NULL')
            ->orderBy('time_out', 'DESC')
            ->get()->getResultArray();

        return $this->respond([
            'attendance_counts' => $attendanceCounts,
            'today_checklists' => $todayChecklists
        ]);
    }
    public function updateOvertimeStatus($id = null){
    $model = new OvertimeSubmissionModel();
    $overtime = $model->find($id);

    if (!$overtime) {
        return $this->failNotFound('Data lembur tidak ditemukan.');
    }
    $newStatus = $this->request->getJsonVar('status');

    if (!in_array($newStatus, ['approved', 'rejected'])) {
        return $this->fail('Status tidak valid. Hanya boleh "approved" atau "rejected".', 400);
    }

    if ($model->update($id, ['status' => $newStatus])) {
        return $this->respondUpdated(['status' => 'success', 'message' => 'Status lembur berhasil diperbarui.']);
    }

    return $this->fail('Gagal memperbarui status lembur.', 500);
    }
}
