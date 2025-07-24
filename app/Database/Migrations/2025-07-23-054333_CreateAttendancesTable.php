<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT', 'constraint' => 11, 'unsigned'   => true,
            ],
            'attendance_date' => [
                'type' => 'DATE', 'null' => true,
            ],
            'time_in' => [
                'type' => 'TIME', 'null' => true,
            ],
            'time_out' => [
                'type' => 'TIME', 'null' => true,
            ],
            'latitude' => [
                'type'       => 'VARCHAR', 'constraint' => '50', 'null' => true,
            ],
            'longitude' => [
                'type'       => 'VARCHAR', 'constraint' => '50', 'null' => true,
            ],
            'address' => [
                'type' => 'TEXT', 'null' => true,
            ],
            'shift' => [
                'type'       => 'VARCHAR', 'constraint' => '100', 'null' => true,
            ],
            'work_location_type' => [
                'type'       => "ENUM('WFO', 'WFA')", 'null' => true,
            ],
            'photo_in' => [
                'type'       => 'VARCHAR', 'constraint' => '255', 'null' => true,
            ],
            'photo_out' => [
                'type'       => 'VARCHAR', 'constraint' => '255', 'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR', 'constraint' => '50', 'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME', 'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME', 'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendances');
    }

    public function down()
    {
        $this->forge->dropTable('attendances');
    }
}