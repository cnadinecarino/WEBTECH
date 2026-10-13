<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['title', 'status', 'task_date', 'created_at'];
    protected $useTimestamps = false;

    public function getTodayTasks(?string $today = null): array
    {
        return $this->where('task_date', $today ?? date('Y-m-d'))
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function getAllTasks(): array
    {
        return $this->orderBy('task_date', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
