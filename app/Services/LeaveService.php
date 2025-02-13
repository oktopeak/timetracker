<?php

namespace App\Services;

use App\Mail\LeaveRequestStatusChanged;
use App\Repositories\Implementations\CheckInRepository;
use App\Repositories\Implementations\HolidayRepository;
use App\Repositories\Implementations\LeaveRepository;
use App\Repositories\Implementations\PersonalHolidayRepository;
use App\Repositories\Implementations\VacationRepository;
use Illuminate\Support\Facades\Mail;

class LeaveService
{
    public function __construct(
        private readonly LeaveRepository $leaveRepo,
        private readonly CheckInRepository $checkinRepo,
        private readonly VacationRepository $vacationRepo,
        private readonly PersonalHolidayRepository $personalHolidayRepo,
        private readonly HolidayRepository $publicHolidayRepo
    ) {}

    public function createLeaveRequest($user, $data)
    {
        $personalHolidays = $this->personalHolidayRepo->findByUserId($user->id) ?? collect();
        $publicHolidays = $this->publicHolidayRepo->getAll() ?? collect();

        $data['number_of_days'] = $this->calculateNumberOfDays($data['leave_start'], $data['leave_end'], $publicHolidays, $personalHolidays);

        $data['user_id'] = $user->id;
        $data['status'] = 'pending';
        $data['notes'] = $data['notes'] ?? '';

        return $this->leaveRepo->create($data);
    }

    public function getPendingLeaveRequests()
    {
        return $this->leaveRepo->getPendingLeaveRequests();
    }

    public function getApprovedRequests($year, $month)
    {
        return $this->leaveRepo->getApprovedRequests($year, $month);
    }

    public function updateStatus($user, $id, $status)
    {
        $leave = $this->leaveRepo->findWith($id, ['leave_type']);

        if (!$leave || !$leave->user) {
            throw new \Exception('Leave request not found.', 404);
        }

        $updated = $this->leaveRepo->updateStatus($user, $leave, $status);

        Mail::to($leave->user->email)->send(new LeaveRequestStatusChanged($leave, $status));
        return $updated;
    }

    public function getUsersLeaveRequests($id)
    {
        $leaves = $this->leaveRepo->findUsersLeaves($id);

        if (!$leaves) {
            throw new \Exception('Leave requests not found.', 404);
        }

        return $leaves;
    }

    public function processApproval($id)
    {
        $leave = $this->leaveRepo->findWith($id, ['leave_type']);

        if(!$leave || !$leave->user){
            throw new \Exception('Leave request not found.', 404);
        }

        if ($leave->status === 'approved') {
            throw new \Exception('Leave request already approved.');
        }

        if ($leave->leave_type->id !== 1) {
            $this->processVacationDays($leave);
        }

        return $this->checkinRepo->addLeaveCheckIns($leave->user->id, $leave->leave_start, $leave->leave_end, $leave->number_of_days, $leave->leave_type->name);
    }

    private function calculateNumberOfDays(string $start, string $end, $publicHolidays, $personalHolidays): int
    {
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);
        $days = 0;

        while ($startDate <= $endDate) {
            if (!$startDate->isWeekend()) {
                if (!$publicHolidays->contains('date',  $startDate->toDateString())) {
                    if (!$personalHolidays->contains('date',  $startDate->toDateString())) {
                        $days++;
                    }
                }
            }
            $startDate->addDay();
        }

        return $days;
    }

    private function processVacationDays($leave)
    {
        $user = $leave->user;
        $days = $leave->number_of_days;
        $vacation = $this->vacationRepo->findByUserId($user->id);

        if (!$vacation || ($vacation->carried_days + $vacation->days_left) < $days) {
            throw new \Exception('Not enough vacation days.');
        }

        $remainingDays = $days;
        if ($vacation->carried_days >= $remainingDays) {
            $vacation->carried_days -= $remainingDays;
            $remainingDays = 0;
        } else {
            $remainingDays -= $vacation->carried_days;
            $vacation->carried_days = 0;
        }

        if ($remainingDays > 0) {
            $vacation->days_left -= $remainingDays;
        }

        $this->vacationRepo->save($vacation);
    }
}
