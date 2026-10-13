<?php

namespace App\Controllers;

use App\Models\TaskModel;

class Home extends BaseController
{
    public function index(): string
    {
        $tasks = new TaskModel();

        return view('pages/home', [
            'pageTitle' => 'Today',
            'activePage' => 'home',
            'today' => date('Y-m-d'),
            'tasks' => $tasks->getTodayTasks(),
        ]);
    }
}
