<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index(): string
    {
        $users = [
            ['username' => 'admin01', 'full_name' => 'Charlize Nadine Carino', 'role' => 'Administrator'],
            ['username' => 'cashier01', 'full_name' => 'Salie Ann Carino', 'role' => 'Cashier'],
            ['username' => 'cashier02', 'full_name' => 'Edzel Arce', 'role' => 'Cashier'],
            ['username' => 'supervisor01', 'full_name' => 'Charles Untalan', 'role' => 'Supervisor'],
            ['username' => 'inventory01', 'full_name' => 'Carl Navarro', 'role' => 'Inventory Clerk'],
        ];

        return view('users/index', [
            'title' => 'User Accounts',
            'users' => $users,
        ]);
    }
}
