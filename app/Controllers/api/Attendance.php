<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\AttendanceModel;

class Attendance extends ResourceController{
    /**
     * Menerima data check in dari Flutter
     */
    // app/Controllers/Api/AttendanceController.php

public function validateWfoIp(){
    // --- Bagian Konfigurasi ---

    // 1. Definisikan rentang IP yang diizinkan
    $ipRangeStart = '10.14.72.1';
    $ipRangeEnd   = '10.14.72.254';

    // 2. Definisikan IP spesifik lainnya yang diizinkan
    $allowedSpecificIps = [
        '192.168.137.177'
    ];

    // --- Bagian Logika Validasi ---

    // Dapatkan IP pengguna yang melakukan request
    $requesterIp = $this->request->getIPAddress();

    // Konversi IP ke format angka untuk perbandingan rentang
    $requesterIpLong  = ip2long($requesterIp);
    $ipRangeStartLong = ip2long($ipRangeStart);
    $ipRangeEndLong   = ip2long($ipRangeEnd);

    // Cek Kondisi 1: Apakah IP berada di dalam rentang?
    $isInRange = ($requesterIpLong >= $ipRangeStartLong && $requesterIpLong <= $ipRangeEndLong);

    // Cek Kondisi 2: Apakah IP ada di dalam daftar spesifik?
    $isInSpecificList = in_array($requesterIp, $allowedSpecificIps);

    // Jika salah satu kondisi terpenuhi, izinkan akses
    if ($isInRange || $isInSpecificList) {
        return $this->respond(['status' => 'ok', 'message' => 'IP valid.']);
    }

    // Jika kedua kondisi tidak terpenuhi, tolak akses
    return $this->fail('Akses WFO hanya diizinkan dari jaringan kantor.', 403);
}
    public function checkin(){
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
        'user_id' => $this->request->getPost('user_id'),
        'latitude' => $this->request->getPost('latitude'),
        'longitude' => $this->request->getPost('longitude'),
        'address' => $this->request->getPost('address'),
        'shift'   => $this->request->getPost('shift'),
        'photo_in' => $newFileName,
        'work_location_type' => $this->request->getPost('work_location_type'),
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
    public function checkout(){
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
    public function history($userId)
{
    $model = new \App\Models\AttendanceModel(); 

    // Ambil tanggal dari query parameter di URL (?startDate=...&endDate=...)
    $startDate = $this->request->getGet('startDate');
    $endDate = $this->request->getGet('endDate');

    // Bangun query dasar
    $query = $model->where('user_id', $userId);

    // Jika parameter tanggal ada, tambahkan filter ke query
    if ($startDate && $endDate) {
        $query->where('attendance_date >=', $startDate)
              ->where('attendance_date <=', $endDate);
    }

    // Eksekusi query dan urutkan hasilnya
    $data = $query->orderBy('attendance_date', 'DESC')->findAll();

    return $this->respond($data);
}

}
