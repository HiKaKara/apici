<?php

namespace App\Controllers\api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\AttendanceModel;
use Config\Services;

class Attendance extends ResourceController
{
    protected $modelName = 'App\Models\AttendanceModel';
    protected $format    = 'json';


    public function checkin(){
        date_default_timezone_set('Asia/Jakarta');

        $userId = $this->request->getVar('user_id');
        $latitude = $this->request->getVar('latitude');
        $longitude = $this->request->getVar('longitude');
        $address = $this->request->getVar('address');
        $shift = $this->request->getVar('shift');
        $workLocationType = $this->request->getVar('work_location_type');
        $photo = $this->request->getFile('photo_in');

        $validation = Services::validation();
        $validation->setRules([
            'user_id' => 'required|numeric',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'shift' => 'required',
            'work_location_type' => 'required',
            'photo_in' => 'uploaded[photo_in]|mime_in[photo_in,image/jpg,image/jpeg,image/png]|max_size[photo_in,4096]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors(), 400);
        }

        $today = date('Y-m-d');
        $existingCheckin = $this->model->where('user_id', $userId)
                                       ->where('attendance_date', $today)
                                       ->first();

        if ($existingCheckin) {
            return $this->fail('Anda sudah melakukan check-in hari ini.', 409);
        }

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(ROOTPATH . 'public/uploads/attendances', $newName);

            $dataToInsert = [
                'user_id' => $userId,
                'attendance_date' => $today,
                'time_in' => date('H:i:s'),
                'latitude_in' => $latitude,
                'longitude_in' => $longitude,
                'address_in' => $address,
                'photo_in' => $newName,
                'shift' => $shift,
                'work_location_type' => $workLocationType,
            ];

            if ($this->model->insert($dataToInsert)) {
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Check-in berhasil dicatat.'
                ]);
            } else {
                return $this->fail($this->model->errors() ?? 'Gagal menyimpan data ke database.', 500);
            }
        }

        $errorString = $photo ? $photo->getErrorString() . '(' . $photo->getError() . ')' : 'File foto tidak ditemukan.';
        return $this->fail($errorString, 400);
    }

    public function checkout(){
        date_default_timezone_set('Asia/Jakarta');

        $userId = $this->request->getVar('user_id');
        $latitude = $this->request->getVar('latitude');
        $longitude = $this->request->getVar('longitude');
        $address = $this->request->getVar('address');
        $photo = $this->request->getFile('photo_out');
        $checklist = $this->request->getVar('checkout_checklist');

        $validation = Services::validation();
        $validation->setRules([
            'user_id' => 'required|numeric',
            'latitude' => 'required',
            'longitude' => 'required',
            'photo_out' => 'uploaded[photo_out]|mime_in[photo_out,image/jpg,image/jpeg,image/png]|max_size[photo_out,4096]',
            'checkout_checklist' => 'required|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors(), 400);
        }

        $today = date('Y-m-d');
        $attendance = $this->model->where('user_id', $userId)
                                  ->where('attendance_date', $today)
                                  ->first();

        if (!$attendance) {
            return $this->failNotFound('Anda belum melakukan check-in hari ini.');
        }

        if ($attendance['time_out'] !== null) {
            return $this->fail('Anda sudah melakukan check-out hari ini.', 409);
        }

        if ($photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(ROOTPATH . 'public/uploads/attendances', $newName);

            $dataToUpdate = [
                'time_out' => date('H:i:s'),
                'latitude_out' => $latitude,
                'longitude_out' => $longitude,
                'photo_out' => $newName,
                'checkout_checklist' => $checklist
            ];

            if (!empty($address)) {
                $dataToUpdate['address_out'] = $address;
            }

            if ($this->model->update($attendance['id'], $dataToUpdate)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Check-out berhasil.'
                ]);
            } else {
                return $this->fail($this->model->errors() ?? 'Gagal memperbarui data di database.', 500);
            }
        }

        return $this->fail($photo->getErrorString() . '(' . $photo->getError() . ')', 400);
    }

    public function history($userId){
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $builder = $this->model
            ->select('attendances.id, attendances.attendance_date, shifts.name as shift, attendances.time_in, attendances.time_out, attendances.status, attendances.work_location_type, attendances.photo_in, attendances.photo_out, attendances.latitude_in, attendances.longitude_in, attendances.checkout_checklist') // 1. Menambahkan semua kolom, termasuk checklist
            ->join('shifts', 'shifts.id = attendances.shift_id', 'left') // 2. Menggabungkan dengan tabel shift
            ->where('attendances.user_id', $userId);

        if ($startDate && $endDate) {
            $builder->where('attendance_date >=', $startDate)
                    ->where('attendance_date <=', $endDate);
        } else {
            $builder->where('MONTH(attendance_date)', date('m'))
                    ->where('YEAR(attendance_date)', date('Y'));
        }

        $history = $builder->orderBy('attendance_date', 'DESC')->findAll();

        if (empty($history)) {
            return $this->respond([]);
        }

        return $this->respond($history);
    }

    public function validateWfoIp()
    {
        $allowedIps = config('Office')->allowedIps;
        $userIp = $this->request->getIPAddress();

        foreach ($allowedIps as $allowedIp) {
            if ($this->ip_in_range($userIp, $allowedIp)) {
                return $this->respond(['status' => 'success', 'message' => 'IP terverifikasi.'], 200);
            }
        }

        return $this->fail('Akses ditolak. Anda tidak berada di jaringan kantor yang diizinkan.', 403);
    }

    /**
     * Helper function to check if an IP is within a CIDR range.
     */
    private function ip_in_range($ip, $range): bool
    {
        if (strpos($range, '/') === false) {
            // This is a single IP, not a range
            return $ip === $range;
        }

        // It's a CIDR range
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; // Discard host bits

        return ($ip & $mask) === $subnet;
    }
}
