<?php

namespace App\Repositories\Interfaces;

interface CheckInRepositoryInterface
{
    //public function getUsersCheckIns($date);
    public function getUsersWithLastCheckIns();
    public function getCheckInsForUser($id, $year, $month);
    public function getCheckInsByDateRange($id, $startDate, $endDate);
    public function addLeaveCheckIns($userId, $start_date, $leave_end, $days, $leave_type);
    public function getUsersCheckInsToday();
}
