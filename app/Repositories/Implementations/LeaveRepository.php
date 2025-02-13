<?php

namespace App\Repositories\Implementations;

use App\Models\Leave;
use App\Models\User;
use App\Repositories\Interfaces\LeaveRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeaveRepository extends Repository implements LeaveRepositoryInterface
{
    public function __construct(Leave $model)
    {
        parent::__construct($model);
    }

    public function getPendingLeaveRequests()
    {
        return Leave::where('status', 'pending')
            ->with('user','leave_type')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })->get();
    }

    public function getApprovedRequests($year = null, $month = null)
    {
        $query = Leave::query();
        if($year){
            $query->whereYear('leave_start', $year);
            if($month){
                $query->whereMonth('leave_start', $month);
            }
        }

       return $query->where('status', 'approved')
            ->with('user', 'leave_type')
            ->get()
            ->groupBy('user_id');
    }

    public function updateStatus($user, $leave, $status)
    {
        $leave->status = $status;
        $leave->reviewed_by = $user->id;
        $leave->reviewed_at = now();
        $leave->save();
        return $leave->load('leave_type');
    }

    public function findUsersLeaves($id)
    {
        if(!User::find($id)){
            return null;
        }
        return Leave::where('user_id', $id)
            ->with('leave_type')
            ->get()
            ->groupBy(function ($leave) {
                return $leave->leave_type->name ?? 'Unknown';
            });
    }

    public function getLeavesForUser(User $user, int $pagination)
    {
        return Leave::where('user_id', $user->id)->orderBy('id', 'desc')->paginate($pagination);
    }
}
