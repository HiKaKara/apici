<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendancesTable extends Migration
{
    // app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateAttendancesTable.php
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        'attendance_date' => ['type' => 'DATE'],
        'time_in' => ['type' => 'TIME', 'null' => true],
        'time_out' => ['type' => 'TIME', 'null' => true],
        'status' => [
            'type' => 'ENUM',
            'constraint' => ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti', 'Alpa'],
            'default' => 'Alpa',
        ],
        'notes' => ['type' => 'TEXT', 'null' => true],
        'location_in' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true], // Format: "latitude,longitude"
        'location_out' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
        'photo_in' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true], // Path ke file foto selfie
        'photo_out' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    // Menambahkan unique key untuk memastikan satu user hanya bisa absen sekali per hari
    $this->forge->addUniqueKey(['user_id', 'attendance_date']);
    $this->forge->createTable('attendances');
}

public function down()
{
    $this->forge->dropTable('attendances');
}
}
