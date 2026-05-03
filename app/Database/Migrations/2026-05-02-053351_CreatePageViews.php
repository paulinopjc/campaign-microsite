<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePageViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'campaign_id' => ['type' => 'INT'],
            'utm_source' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'utm_medium' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'viewed_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('campaign_id');
        $this->forge->addKey('viewed_at');
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', '', 'CASCADE');
        $this->forge->createTable('page_views');
    }

    public function down()
    {
        $this->forge->dropTable('page_views');
    }
}