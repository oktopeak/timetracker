<?php

namespace App\Repositories\Implementations;

use App\Models\User;
use App\Models\Vacation;
use App\Repositories\Interfaces\VacationRepositoryInterface;

class VacationRepository extends Repository implements VacationRepositoryInterface
{
    public function __construct(Vacation $model)
    {
        parent::__construct($model);
    }

    public function findByUserId($userId)
    {
        $currentYear = now()->year;

        if(!User::find($userId)){
            return null;
        }

        return Vacation::where('user_id', $userId)
            ->where('year', $currentYear)
            ->first();
    }

    public function getUsersVacationHistory($userId)
    {
        return Vacation::where('user_id', $userId)
            ->orderBy('year', 'desc')
            ->get(['year', 'number_of_days', 'days_left']);
    }
}
