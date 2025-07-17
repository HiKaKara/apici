<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\AttendanceModel;

class Attendance extends ResourceController
{
    /**
     * Menerima data check in dari Flutter
     */
    public function checkin()
    {
        $model = new AttendanceModel();

        // Ambil data dari request
        $userId = $this->request->getVar('user_id');
        $today = date('Y-m-d');

        // --- PERBAIKAN DI SINI ---
        // Periksa apakah pengguna sudah check in hari ini
        $alreadyCheckedIn = $model->where('user_id', $userId)
                                  ->where('attendance_date', $today)
                                  ->first();

        if ($alreadyCheckedIn) {
            // Jika sudah ada, kirim error 409 Conflict
            return $this->fail('Anda sudah melakukan Check In hari ini.', 409);
        }
        // --- AKHIR PERBAIKAN ---

        $shift = $this->request->getVar('shift');
        $latitude = $this->request->getVar('latitude');
        $longitude = $this->request->getVar('longitude');
        $address = $this->request->getVar('address');
        $photo = $this->request->getFile('photo_in');

        // Validasi file foto
        if (!$photo || !$photo->isValid() || $photo->hasMoved()) {
            return $this->fail($photo ? $photo->getErrorString() . '(' . $photo->getError() . ')' : 'File foto tidak ditemukan.');
        }

        // Pindahkan foto ke folder public
        $newName = $photo->getRandomName();
        $photo->move(FCPATH . 'uploads/attendances', $newName);

        // Siapkan data untuk disimpan ke database
        $data = [
            'user_id'         => $userId,
            'attendance_date' => $today,
            'time_in'         => date('H:i:s'),
            'status'          => 'Hadir',
            'location_in'     => "$latitude,$longitude",
            'notes'           => "Shift: $shift, Alamat: $address",
            'photo_in'        => $newName,
            'work_type'       => 'WFO',
        ];

        // Simpan ke database
        if ($model->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Check In berhasil direkam.']);
        }

        return $this->fail('Gagal menyimpan data presensi.');
    }

    /**
     * Menerima data check out dan memperbarui record yang ada
     */
    public function checkout()
    {
        $model = new AttendanceModel();
        $userId = $this->request->getVar('user_id');
        $latitude = $this->request->getVar('latitude');
        $longitude = $this->request->getVar('longitude');
        $photo = $this->request->getFile('photo_out');

        $attendanceData = $model->where('user_id', $userId)
                                ->where('attendance_date', date('Y-m-d'))
                                ->first();

        if (!$attendanceData) {
            return $this->failNotFound('Data Check In untuk hari ini tidak ditemukan. Silakan Check In terlebih dahulu.');
        }

        if (!$photo || !$photo->isValid() || $photo->hasMoved()) {
            return $this->fail($photo ? $photo->getErrorString() . '(' . $photo->getError() . ')' : 'File foto tidak ditemukan.');
        }

        $newName = $photo->getRandomName();
        $photo->move(FCPATH . 'uploads/attendances', $newName);

        $data = [
            'time_out'     => date('H:i:s'),
            'location_out' => "$latitude,$longitude",
            'photo_out'    => $newName,
        ];

        if ($model->update($attendanceData['id'], $data)) {
            return $this->respondUpdated(['status' => 200, 'message' => 'Check Out berhasil direkam.']);
        }

        return $this->fail('Gagal memperbarui data presensi.');
    }

    /**
     * Mengambil riwayat presensi untuk pengguna tertentu.
     */
    public function history($userId = null)
    {
        if ($userId === null) {
            return $this->fail('User ID harus disediakan.', 400);
        }

        $model = new AttendanceModel();
        $history = $model->where('user_id', $userId)
                         ->orderBy('attendance_date', 'DESC')
                         ->findAll();

        if (empty($history)) {
            return $this->respond([]);
        }

        return $this->respond($history);
    }
}
