<?php

namespace App\Models;

use CodeIgniter\Model;

class OvertimeSubmissionModel extends Model
{
    protected $table            = 'overtime_submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'overtime_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'coworker_id',
        'evidence_photo',
        'location_address',
        'latitude',
        'longitude',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}