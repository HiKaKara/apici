<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWorkTypeToAttendance extends Migration
{
    public function up()
    {
        $this->forge->addColumn('attendances', [ // GANTI dengan nama tabel absensi Anda
            'work_location_type' => [
                'type'       => "ENUM('WFO', 'WFA')",
                'after'      => 'shift', // Atur posisi kolom jika perlu
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('attendances', 'work_location_type'); // GANTI nama tabel jika perlu
    }
}