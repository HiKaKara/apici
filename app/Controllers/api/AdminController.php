<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\AttendanceModel;

class AdminController extends ResourceController
{
    protected $format = 'json';

    public function getAllEmployees()
    {
        $userModel = new UserModel();
        $employees = $userModel->findAll();
        return $this->respond($employees);
    }

    public function updateUserRole($id = null)
    {
        $userModel = new UserModel();
        $data = $this->request->getJSON();

        if (!$userModel->find($id)) {
            return $this->failNotFound('User tidak ditemukan.');
        }

        if ($userModel->update($id, ['role' => $data->role])) {
            return $this->respondUpdated(['status' => 'success', 'message' => 'Role user berhasil diperbarui.']);
        }

        return $this->fail($userModel->errors());
    }

    // FUNGSI BARU: Untuk mengambil data dashboard admin
    public function dashboardSummary()
    {
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
