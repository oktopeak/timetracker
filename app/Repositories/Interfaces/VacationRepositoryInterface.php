<?php

namespace App\Repositories\Interfaces;

interface VacationRepositoryInterface
{
    public function findByUserId($userId);
    public function getUsersVacationHistory($userId);
}
