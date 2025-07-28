<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganToOvertime extends Migration
{
    public function up()
    {
        $this->forge->addColumn('overtime_submissions', [
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status', // meletakkan kolom setelah kolom status
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('overtime_submissions', 'keterangan');
    }
}