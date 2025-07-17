<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWfoColumnsToAttendances extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_AddWfoColumnsToAttendances.php
public function up()
{
    $fields = [
        'work_type' => [
            'type' => 'ENUM',
            'constraint' => ['WFO', 'WFH'],
            'after' => 'user_id', // Menempatkan kolom setelah user_id
            'default' => 'WFO',
        ],
        'location_id' => [
            'type' => 'INT',
            'constraint' => 5,
            'unsigned' => true,
            'null' => true, // Boleh NULL untuk WFH
            'after' => 'work_type',
        ],
    ];

    $this->forge->addColumn('attendances', $fields);
    $this->forge->addForeignKey('location_id', 'work_locations', 'id', 'SET NULL', 'SET NULL');
}

public function down()
{
    $this->forge->dropForeignKey('attendances', 'attendances_location_id_foreign');
    $this->forge->dropColumn('attendances', ['work_type', 'location_id']);
}
}
