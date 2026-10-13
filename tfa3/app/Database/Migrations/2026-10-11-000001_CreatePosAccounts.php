<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class CreatePosAccounts extends Migration
{
    public function up()
    {
        // Existing installations keep their tables and records untouched.
        // The sample rows are copied only when their table is first created.
        $attributes = $this->db->DBDriver === 'MySQLi'
            ? ['ENGINE' => 'InnoDB', 'CHARACTER SET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_general_ci']
            : [];

        if (! $this->db->tableExists('customers')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'full_name' => ['type' => 'VARCHAR', 'constraint' => 150],
                'email' => ['type' => 'VARCHAR', 'constraint' => 254],
                'phone' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => ''],
            ]);
            $this->forge->addKey('id', true);

            if (! $this->forge->createTable('customers', false, $attributes)) {
                throw new RuntimeException('The customers table could not be created.');
            }

            $inserted = $this->db->table('customers')->insertBatch([
                ['full_name' => 'Kathryn Bernardo', 'email' => 'kathryn.bnernardo@example.com', 'phone' => '0917 123 4567'],
                ['full_name' => 'Nadine Lustre', 'email' => 'nadine.lustre@example.com', 'phone' => '0918 234 5678'],
                ['full_name' => 'Liza Soberano', 'email' => 'liza.soberano@example.com', 'phone' => '0919 345 6789'],
                ['full_name' => 'Justin Bieber', 'email' => 'justin.bieber@example.com', 'phone' => '0920 456 7890'],
                ['full_name' => 'Shawn Mendez', 'email' => 'shawn.mendez@example.com', 'phone' => '0921 567 8901'],
            ]);

            if ($inserted === false) {
                throw new RuntimeException('The original customer records could not be copied.');
            }
        }

        if (! $this->db->tableExists('users')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'username' => ['type' => 'VARCHAR', 'constraint' => 60],
                'full_name' => ['type' => 'VARCHAR', 'constraint' => 150],
                'role' => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => ''],
            ]);
            $this->forge->addKey('id', true);

            if (! $this->forge->createTable('users', false, $attributes)) {
                throw new RuntimeException('The users table could not be created.');
            }

            $inserted = $this->db->table('users')->insertBatch([
                ['username' => 'admin01', 'full_name' => 'Charlize Nadine Carino', 'role' => 'Administrator'],
                ['username' => 'cashier01', 'full_name' => 'Salie Ann Carino', 'role' => 'Cashier'],
                ['username' => 'cashier02', 'full_name' => 'Edzel Arce', 'role' => 'Cashier'],
                ['username' => 'supervisor01', 'full_name' => 'Charles Untalan', 'role' => 'Supervisor'],
                ['username' => 'inventory01', 'full_name' => 'Carl Navarro', 'role' => 'Inventory Clerk'],
            ]);

            if ($inserted === false) {
                throw new RuntimeException('The original user records could not be copied.');
            }
        }
    }

    public function down()
    {
        throw new RuntimeException('This migration preserves account records and cannot be rolled back automatically. Restore a database backup or write a reviewed forward migration.');
    }
}
