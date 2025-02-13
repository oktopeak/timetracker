<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface LeaveRepositoryInterface
{
    public function getPendingLeaveRequests();
    public function getApprovedRequests($year, $month);
    public function updateStatus($user, $leave, $status);
    public function findUsersLeaves($id);
    public function getLeavesForUser(User $user, int $pagination);
}
