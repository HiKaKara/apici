<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    // Nama tabel di database
    protected $table            = 'attendances';

    // Primary key dari tabel
    protected $primaryKey       = 'id';

    // Kolom-kolom yang diizinkan untuk diisi melalui metode insert() dan update()
    protected $allowedFields    = [
        'user_id',
        'attendance_date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'location_in',
        'location_out',
        'photo_in',
        'photo_out',
        'work_type'
    ];

    // Mengaktifkan fitur auto-timestamp untuk created_at dan updated_at
    protected $useTimestamps = true;
}