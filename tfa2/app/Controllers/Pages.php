<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index(): string
    {
        return view('pages/index', ['title' => 'Home']);
    }

    public function about(): string
    {
        return view('pages/about', ['title' => 'About']);
    }
}
