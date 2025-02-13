<?php

namespace App\Services;

use App\Repositories\Implementations\LeaveTypeRepository;

class LeaveTypeService
{
    public function __construct(private readonly LeaveTypeRepository $leaveTypeRepo){}

    public function getAll()
    {
        return $this->leaveTypeRepo->getAll();
    }

    public function createLeaveType($data)
    {
        return $this->leaveTypeRepo->create($data);
    }

    public function updateLeaveType($id, $data)
    {
        $leaveType = $this->leaveTypeRepo->findById($id);

        if (!$leaveType) {
            throw new \Exception('Leave type not found.', 404);
        }

        return $this->leaveTypeRepo->update($leaveType, $data);
    }

    public function destroy($id)
    {
        $leaveType = $this->leaveTypeRepo->findById($id);

        if (!$leaveType) {
            throw new \Exception('Leave type not found.', 404);
        }

        return $this->leaveTypeRepo->destroy($leaveType);
    }
}
