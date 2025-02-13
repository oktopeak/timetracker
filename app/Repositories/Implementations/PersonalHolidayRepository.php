<?php

namespace App\Repositories\Implementations;

use App\Models\PersonalHoliday;
use App\Models\User;
use App\Repositories\Interfaces\PersonalHolidayRepositoryInterface;

class PersonalHolidayRepository extends Repository implements PersonalHolidayRepositoryInterface
{
    public function __construct(PersonalHoliday $model)
    {
        parent::__construct($model);
    }

    public function findByUserId($userId)
    {
        if(!User::find($userId)){
            return null;
        }
        return PersonalHoliday::where('user_id', $userId)->get();
    }
}
