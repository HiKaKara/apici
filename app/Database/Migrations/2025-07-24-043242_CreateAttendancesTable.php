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
            'latitude_in' => [
                'type' => 'VARCHAR', 'constraint' => '50', 'null' => true,
            ],
            'longitude_in' => [
                'type' => 'VARCHAR', 'constraint' => '50', 'null' => true,
            ],
            'address_in' => [
                'type' => 'TEXT', 'null' => true,
            ],
            'latitude_out' => [
                'type' => 'VARCHAR', 'constraint' => '50', 'null' => true,
            ],
            'longitude_out' => [
                'type' => 'VARCHAR', 'constraint' => '50', 'null' => true,
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
