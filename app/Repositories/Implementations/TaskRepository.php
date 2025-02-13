<?php

namespace App\Repositories\Implementations;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class TaskRepository extends Repository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function getTasksByUserAndDate($userId=null, $teamId=null, $year=null, $month=null)
    {
        $query = Task::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($teamId) {
            $query->where('team_id', $teamId);
        }
        if ($year !== null) {
            $query->whereYear('created_at', $year);
        }
        if ($month !== null) {
            $query->whereMonth('created_at', $month);
        }
        return $query->whereHas('user', function ($query) {
            $query->whereNull('deleted_at');
        })->get();
    }
}
