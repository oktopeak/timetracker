<?php

namespace App\Services;

use App\Repositories\Implementations\TaskRepository;

class TaskService
{
    public function __construct(private readonly TaskRepository $taskRepo){}

    public function getTasksByUserAndDate($userId, $teamId, $year, $month){
        return $this->taskRepo->getTasksByUserAndDate($userId, $teamId, $year, $month);
    }

    public function createTask($data)
    {
        return $this->taskRepo->create($data);
    }

    public function findWith($id, $with)
    {
        return $this->taskRepo->findWith($id, $with);
    }

    public function updateTask($task, $data)
    {
        return $this->taskRepo->update($task, $data);
    }

    public function destroyTask($task)
    {
        return $this->taskRepo->destroy($task);
    }
}
