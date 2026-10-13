<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AddUserAvatar extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('avatar', 'users')) {
            if (! $this->forge->addColumn('users', [
                'avatar' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true, 'default' => null],
            ])) {
                throw new RuntimeException('The optional avatar column could not be added.');
            }
        }
    }

    public function down()
    {
        throw new RuntimeException('Avatar references are preserved on rollback. Restore a backup or use a reviewed forward migration instead of dropping this column.');
    }
}
