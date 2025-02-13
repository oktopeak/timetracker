<?php

namespace App\Repositories\Implementations;

use App\Models\LeaveType;
use App\Repositories\Interfaces\LeaveTypeRepositoryInterface;


class LeaveTypeRepository extends Repository implements LeaveTypeRepositoryInterface
{
    public function __construct(LeaveType $model)
    {
        parent::__construct($model);
    }
}
