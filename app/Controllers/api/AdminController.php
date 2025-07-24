<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel; // Ganti dengan nama model user Anda
use App\Models\AttendanceModel; // Ganti dengan nama model absensi Anda

class AdminController extends BaseController
{
    use ResponseTrait;

    // Fungsi untuk mengambil semua data pegawai
    public function getAllEmployees()
    {
        $userModel = new UserModel();
        $employees = $userModel->findAll();
        return $this->respond($employees);
    }

    // Fungsi untuk mengubah role pegawai
    public function updateUserRole($userId)
    {
        $userModel = new UserModel();
        $newRole = $this->request->getJSON()->role;

        if (empty($newRole)) {
            return $this->fail('Role baru harus diisi.', 400);
        }

        $data = ['role' => $newRole];

        if ($userModel->update($userId, $data)) {
            return $this->respondUpdated(['status' => 200, 'message' => 'Role pegawai berhasil diperbarui.']);
        } else {
            return $this->fail($userModel->errors(), 400);
        }
    }

    // Fungsi untuk mengambil semua riwayat presensi dengan filter tanggal
    public function getAttendanceHistory()
    {
        $attendanceModel = new AttendanceModel();
        
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $query = $attendanceModel->select('attendances.*, users.name as employee_name')
                                 ->join('users', 'users.id = attendances.user_id');

        if ($startDate && $endDate) {
            $query->where('attendances.attendance_date >=', $startDate)
                  ->where('attendances.attendance_date <=', $endDate);
        }

        $history = $query->orderBy('attendances.attendance_date', 'DESC')->findAll();

        return $this->respond($history);
    }
}