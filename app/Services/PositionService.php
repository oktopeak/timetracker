<?php

namespace App\Services;

use App\Repositories\Implementations\PositionRepository;

class PositionService
{
    public function __construct(private readonly PositionRepository $positionRepo){}

    public function createPosition($data)
    {
        return $this->positionRepo->create($data);
    }

    public function getPositions()
    {
        return $this->positionRepo->getAll();
    }

    public function findPosition($id)
    {
        return $this->positionRepo->findById($id);
    }

    public function updatePosition($id, $data)
    {
        $position = $this->positionRepo->findById($id);

        if (!$position) {
            throw new \Exception('Position not found.', 404);
        }

        return $this->positionRepo->update($position, $data);
    }

    public function destroyPosition($id)
    {
        $position = $this->positionRepo->findById($id);

        if (!$position) {
            throw new \Exception('Position not found.', 404);
        }

        return $this->positionRepo->destroy($position);
    }
}
