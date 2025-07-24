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
    $userId = $this->request->getVar('user_id');
    $today = date('Y-m-d');

    if (empty($userId)) {
        return $this->fail('User ID tidak boleh kosong.', 400);
    }

    $alreadyCheckedIn = $model->where('user_id', $userId)
                              ->where('attendance_date', $today)
                              ->first();

    if ($alreadyCheckedIn) {
        return $this->fail('Anda sudah melakukan Check In hari ini.', 409);
    }

    $photo = $this->request->getFile('photo_in');

    if (!$photo || !$photo->isValid()) {
        return $this->fail('File foto tidak ditemukan atau tidak valid.', 400);
    }

    $newName = $photo->getRandomName();
    $photo->move(FCPATH . 'uploads/attendances', $newName);

    // Siapkan data untuk disimpan
    $data = [
        'user_id'            => $userId,
        'latitude'           => $this->request->getVar('latitude'),
        'longitude'          => $this->request->getVar('longitude'),
        'address'            => $this->request->getVar('address'),
        'shift'              => $this->request->getVar('shift'),
        'work_location_type' => $this->request->getVar('work_location_type'),
        'time_in'            => date('H:i:s'),
        'attendance_date'    => $today,
        'photo_in'           => $newName, // Menggunakan variabel $newName yang benar
    ];
    
    // Simpan ke database
    if ($model->insert($data)) {
        return $this->respondCreated(['status' => 201, 'message' => 'Check In berhasil direkam.']);
    }

    return $this->fail($model->errors(), 400);
}

    /**
     * Menerima data check out dan memperbarui record yang ada
     */
    public function checkout()
    {
        $model = new AttendanceModel();
        $userId = $this->request->getVar('user_id');
        $photo = $this->request->getFile('photo_out');

        // Cari data check-in hari ini
        $attendanceData = $model->where('user_id', $userId)
                                ->where('attendance_date', date('Y-m-d'))
                                ->first();

        if (!$attendanceData) {
            return $this->failNotFound('Data Check In untuk hari ini tidak ditemukan.');
        }

        // Cek apakah sudah check out sebelumnya
        if (!empty($attendanceData['time_out'])) {
            return $this->fail('Anda sudah melakukan Check Out hari ini.', 409);
        }

        if (!$photo || !$photo->isValid() || $photo->hasMoved()) {
            return $this->fail('File foto tidak ditemukan atau tidak valid.', 400);
        }

        $newName = $photo->getRandomName();
        $photo->move(FCPATH . 'uploads/attendances', $newName);

        $data = [
            'time_out'  => date('H:i:s'),
            'photo_out' => $newName,
        ];

        if ($model->update($attendanceData['id'], $data)) {
            return $this->respondUpdated(['status' => 200, 'message' => 'Check Out berhasil direkam.']);
        }

        return $this->fail($model->errors(), 400);
    }

    /**
     * Mengambil riwayat presensi untuk pengguna tertentu.
     */
    public function history($userId)
    {
        $model = new AttendanceModel();

        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $query = $model->where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->where('attendance_date >=', $startDate)
                  ->where('attendance_date <=', $endDate);
        }

        $data = $query->orderBy('attendance_date', 'DESC')->findAll();
        return $this->respond($data);
    }

    public function validateWfoIp()
    {
        $ipRangeStart = '10.14.72.1';
        $ipRangeEnd   = '10.14.72.254';
        $allowedSpecificIps = ['192.168.137.177', '::1'];
        $requesterIp = $this->request->getIPAddress();
        $requesterIpLong  = ip2long($requesterIp);
        $ipRangeStartLong = ip2long($ipRangeStart);
        $ipRangeEndLong   = ip2long($ipRangeEnd);
        $isInRange = ($requesterIpLong >= $ipRangeStartLong && $requesterIpLong <= $ipRangeEndLong);
        $isInSpecificList = in_array($requesterIp, $allowedSpecificIps);

        if ($isInRange || $isInSpecificList) {
            return $this->respond(['status' => 'ok', 'message' => 'IP valid.']);
        }
        return $this->fail('Akses WFO hanya diizinkan dari jaringan kantor.', 403);
    }
}