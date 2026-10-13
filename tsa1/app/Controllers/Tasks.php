<?php

namespace App\Controllers;

use App\Models\TaskModel;

class Tasks extends BaseController
{
    public function index(): string
    {
        return view('pages/tasks', [
            'pageTitle' => 'All tasks',
            'activePage' => 'tasks',
            'tasks' => (new TaskModel())->getAllTasks(),
        ]);
    }
}
