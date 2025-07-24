<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendances'; // Pastikan nama tabel benar
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // --- PASTIKAN SEMUA FIELD INI ADA ---
    protected $allowedFields    = [
        'user_id',
        'attendance_date',
        'time_in',
        'time_out',
        'latitude',
        'longitude',
        'address',          // <-- WAJIB ADA
        'shift',            // <-- WAJIB ADA
        'work_location_type',// <-- WAJIB ADA
        'photo_in',
        'photo_out',
        'status',
    ];
    // ------------------------------------

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}