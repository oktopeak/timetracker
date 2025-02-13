<?php

namespace App\Repositories\Implementations;

use App\Models\CheckIn;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Repositories\Interfaces\CheckInRepositoryInterface;
use Carbon\Carbon;

class CheckInRepository extends Repository implements CheckInRepositoryInterface
{
    public function __construct(CheckIn $model)
    {
        parent::__construct($model);
    }

    public function getUsersCheckInsToday()
    {
        return CheckIn::with('user')
            ->whereDate('date', now()->toDateString())
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('user_id');
    }
//    public function getUsersCheckIns($searchDate = null)
//    {
//        $date = !empty($searchDate) ? $searchDate : now()->toDateString();
//
//        return CheckIn::with('user')
//        ->whereDate('date', $date)
//        ->orderBy('id', 'desc')
//        ->get()
//        ->groupBy('user_id');
//    }

    public function getCheckInsForUser($id, $year, $month)
    {
        return CheckIn::with('user')
            ->where('user_id', $id)
            ->whereRaw('YEAR(date) = ? AND MONTH(date) = ?', [$year, $month])
            ->get();
    }

    public function getCheckInsByDateRange($id, $startDate, $endDate)
    {
        return CheckIn::with('user')
            ->where('user_id', $id)
            ->whereRaw('date BETWEEN ? AND ?', [$startDate, $endDate])
            ->get();
    }

    public function addLeaveCheckIns($userId, $start_date, $leave_end, $days, $leave_type)
    {
        $date = Carbon::parse($start_date);
        $addedDays = 0;

        $holidays = Holiday::pluck('date')->toArray();

        while($addedDays < $days){
            if(!$date->isWeekend()  && !in_array($date->format('Y-m-d'), $holidays)){
                CheckIn::create([
                    'user_id' => $userId,
                    'action' => $leave_type . ' (' . $start_date->format('d.m.Y') . '-' . $leave_end->format('d.m.Y') . ')',
                    'date' => $date,
                ]);
                $addedDays++;
            }
            $date->addDay();
        }

        return true;
    }

    public function getUsersWithLastCheckIns()
    {
        return CheckIn::with('user')
            ->select('id', 'user_id', 'action', 'date')
//            ->whereDate('date', now()->toDateString())
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('check_ins')
                    ->groupBy('user_id');
            })
            ->orderBy('date', 'desc')
            ->get();
    }
}
