<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $today = new \DateTimeImmutable('today');
        $tasks = [
            ['Review lecture notes', 'pending', 0],
            ['Submit web technology activity', 'in progress', 0],
            ['Reply to project group', 'completed', 0],
            ['Organize research references', 'pending', 0],
            ['Read database chapter', 'completed', -2],
            ['Buy supplies for class', 'pending', -1],
            ['Prepare presentation slides', 'in progress', 1],
            ['Meet with project adviser', 'pending', 3],
        ];

        foreach ($tasks as [$title, $status, $dayOffset]) {
            $taskDate = $today->modify(($dayOffset >= 0 ? '+' : '') . $dayOffset . ' days')->format('Y-m-d');
            $exists = $this->db->table('tasks')->where('title', $title)->where('task_date', $taskDate)->countAllResults();
            if ($exists === 0) {
                $this->db->table('tasks')->insert([
                    'title' => $title,
                    'status' => $status,
                    'task_date' => $taskDate,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $userExists = $this->db->table('users')->where('username', 'demo_student')->countAllResults();
        if ($userExists === 0) {
            $this->db->table('users')->insert([
                'username' => 'demo_student',
                'full_name' => 'Alex Morgan',
                'email' => 'alex.morgan@example.test',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
