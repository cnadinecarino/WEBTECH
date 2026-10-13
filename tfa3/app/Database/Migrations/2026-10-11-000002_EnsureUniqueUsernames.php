<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class EnsureUniqueUsernames extends Migration
{
    public function up()
    {
        // GROUP BY uses the column's existing collation, including case and
        // accent comparison rules. Never rename or delete conflicting accounts.
        $duplicates = $this->db->table('users')
            ->select('username, COUNT(*) AS account_count', false)
            ->groupBy('username')
            ->having('COUNT(*) > 1', null, false)
            ->get()
            ->getResultArray();

        if ($duplicates !== []) {
            $conflicts = [];

            foreach ($duplicates as $duplicate) {
                $accounts = $this->db->table('users')
                    ->select('id')
                    ->where('username', $duplicate['username'])
                    ->orderBy('id')
                    ->get()
                    ->getResultArray();

                $conflicts[] = json_encode($duplicate['username']) . ' (IDs: '
                    . implode(', ', array_column($accounts, 'id')) . ')';
            }

            throw new RuntimeException(
                'Cannot add the username unique constraint. Conflicting accounts: '
                . implode('; ', $conflicts)
                . '. Review these records manually; no account was changed.',
            );
        }

        foreach ($this->db->getIndexData('users') as $index) {
            if (in_array(strtoupper($index->type), ['UNIQUE', 'PRIMARY'], true)
                && $index->fields === ['username']) {
                return;
            }
        }

        $this->forge->addUniqueKey('username', 'users_username_unique');

        if (! $this->forge->processIndexes('users')) {
            throw new RuntimeException('The username unique constraint could not be added. No account was changed.');
        }
    }

    public function down()
    {
        throw new RuntimeException('Username uniqueness is retained on rollback. Use a reviewed forward migration if the constraint must change.');
    }
}
