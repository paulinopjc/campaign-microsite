<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimezoneToCampaigns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('campaigns', [
            'timezone' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'UTC'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('campaigns', 'timezone');
    }
}
