<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PosAccountSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            ['full_name' => 'Maya Santos', 'email' => 'maya.santos@example.com', 'phone' => '0917 123 4567', 'created_at' => '2026-10-01 09:00:00'],
            ['full_name' => 'Paolo Reyes', 'email' => 'paolo.reyes@example.com', 'phone' => '0918 234 5678', 'created_at' => '2026-10-02 09:30:00'],
            ['full_name' => 'Lena Garcia', 'email' => 'lena.garcia@example.com', 'phone' => null, 'created_at' => '2026-10-03 10:00:00'],
            ['full_name' => 'Nico Flores', 'email' => 'nico.flores@example.com', 'phone' => '0920 456 7890', 'created_at' => '2026-10-04 10:30:00'],
            ['full_name' => 'Ella Navarro', 'email' => 'ella.navarro@example.com', 'phone' => '0921 567 8901', 'created_at' => '2026-10-05 11:00:00'],
        ];

        $customerTable = $this->db->table('customers');
        foreach ($customers as $customer) {
            $exists = $customerTable
                ->where('full_name', $customer['full_name'])
                ->where('email', $customer['email'])
                ->countAllResults() > 0;

            if (! $exists) {
                $customerTable->insert($customer);
            }
        }

        $users = [
            ['username' => 'admin01', 'full_name' => 'Alyssa Cruz', 'created_at' => '2026-10-01 08:00:00'],
            ['username' => 'cashier01', 'full_name' => 'Marco Lim', 'created_at' => '2026-10-02 08:30:00'],
            ['username' => 'cashier02', 'full_name' => 'Jessa Ramos', 'created_at' => '2026-10-03 09:00:00'],
            ['username' => 'supervisor01', 'full_name' => 'Daniel Co', 'created_at' => '2026-10-04 09:30:00'],
            ['username' => 'inventory01', 'full_name' => 'Bianca Tan', 'created_at' => '2026-10-05 10:00:00'],
        ];

        $userTable = $this->db->table('users');
        foreach ($users as $user) {
            if ($userTable->where('username', $user['username'])->countAllResults() === 0) {
                $userTable->insert($user);
            }
        }
    }
}
