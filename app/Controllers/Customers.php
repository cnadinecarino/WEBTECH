<?php

namespace App\Controllers;

class Customers extends BaseController
{
    public function index(): string
    {
        $customers = [
            ['full_name' => 'Kathryn Bernardo', 'email' => 'kathryn.bnernardo@example.com', 'phone' => '0917 123 4567'],
            ['full_name' => 'Nadine Lustre', 'email' => 'nadine.lustre@example.com', 'phone' => '0918 234 5678'],
            ['full_name' => 'Liza Soberano', 'email' => 'liza.soberano@example.com', 'phone' => '0919 345 6789'],
            ['full_name' => 'Justin Bieber', 'email' => 'justin.bieber@example.com', 'phone' => '0920 456 7890'],
            ['full_name' => 'Shawn Mendez', 'email' => 'shawn.mendez@example.com', 'phone' => '0921 567 8901'],
        ];

        return view('customers/index', [
            'title'     => 'Customer Accounts',
            'customers' => $customers,
        ]);
    }
}
