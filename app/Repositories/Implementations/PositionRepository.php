<?php

namespace App\Repositories\Implementations;

use App\Models\Position;
use App\Repositories\Interfaces\PositionRepositoryInterface;

class PositionRepository extends Repository implements PositionRepositoryInterface
{
    public function __construct(Position $model)
    {
        parent::__construct($model);
    }
}
