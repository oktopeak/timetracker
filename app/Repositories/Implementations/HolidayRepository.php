<?php

namespace App\Repositories\Implementations;

use App\Models\Holiday;
use App\Repositories\Interfaces\HolidayRepositoryInterface;

class HolidayRepository extends Repository implements HolidayRepositoryInterface
{
    public function __construct(Holiday $model)
    {
        parent::__construct($model);
    }

}
