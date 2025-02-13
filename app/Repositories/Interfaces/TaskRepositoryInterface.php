<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    public function getTasksByUserAndDate($userId, $teamId, $year, $month);
}
