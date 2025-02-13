<?php

namespace App\Repositories\Interfaces;

interface PersonalHolidayRepositoryInterface
{
    public function findByUserId($userId);
}
