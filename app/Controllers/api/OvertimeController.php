<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OvertimeSubmissionModel;
use CodeIgniter\API\ResponseTrait;

class OvertimeController extends BaseController
{
    use ResponseTrait;

    public function submit(){
        // 1. Ambil file foto bukti
        $evidenceFile = $this->request->getFile('evidence_photo');
        
        // 2. Validasi file
        if (!$evidenceFile || !$evidenceFile->isValid()) {
            return $this->fail('Foto bukti lembur wajib diunggah.', 400);
        }

        // 3. Pindahkan file ke folder public
        $newFileName = $evidenceFile->getRandomName();
        $evidenceFile->move(FCPATH . 'uploads/overtime_evidence', $newFileName);

        // 4. Siapkan data untuk disimpan
        $data = [
            'user_id'          => $this->request->getPost('user_id'),
            'overtime_type'    => $this->request->getPost('overtime_type'),
            'start_date'       => $this->request->getPost('start_date'),
            'end_date'         => $this->request->getPost('end_date'),
            'start_time'       => $this->request->getPost('start_time'),
            'end_time'         => $this->request->getPost('end_time'),
            'coworker_id'      => $this->request->getPost('coworker_id') ?: null,
            'evidence_photo'   => $newFileName, // Simpan nama file baru
            'location_address' => $this->request->getPost('location_address'),
            'latitude'         => $this->request->getPost('latitude'),
            'longitude'        => $this->request->getPost('longitude'),
        ];

        // 5. Simpan ke database menggunakan Model
        $model = new OvertimeSubmissionModel();
        if ($model->save($data)) {
            return $this->respondCreated([
                'status'  => 201,
                'message' => 'Pengajuan lembur berhasil dikirim.',
            ]);
        } else {
            return $this->fail('Gagal menyimpan data pengajuan lembur.', 400, $model->errors());
        }
    }

    // Fungsi untuk mengambil riwayat lembur
    public function history($userId){
    $model = new \App\Models\OvertimeSubmissionModel();

    // Ambil tanggal dari query parameter
    $startDate = $this->request->getGet('startDate');
    $endDate = $this->request->getGet('endDate');

    $query = $model->where('user_id', $userId);

    // Jika parameter tanggal ada, tambahkan filter
    if ($startDate && $endDate) {
        // Asumsi kolom tanggal lembur adalah 'start_date'
        $query->where('start_date >=', $startDate)
              ->where('start_date <=', $endDate);
    }

    $data = $query->orderBy('start_date', 'DESC')->findAll();

    return $this->respond($data);
}
}