<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\AttendanceModel;

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
            'role' => 'required|in_list[pegawai,admin]'
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
        $userModel = new UserModel();
        $data = $this->request->getJSON(true);

        // Validasi
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[pegawai,admin]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 400);
        }

        // Hash password sebelum disimpan
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        if ($userModel->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Pegawai baru berhasil ditambahkan.']);
        }

        return $this->fail('Gagal menambahkan pegawai.', 500);
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
}
