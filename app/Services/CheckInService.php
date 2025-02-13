<?php

namespace App\Services;

use App\Repositories\Implementations\CheckInRepository;
use App\Repositories\Implementations\UserRepository;
use Exception;

class CheckInService
{
    public function __construct(private readonly CheckInRepository $checkInRepo, private readonly UserRepository $userRepo) {}

    public function storeCheckInPin($pin, $action)
    {
        $user = $this->userRepo->findByPin($pin);

        if (!$user) {
            throw new Exception('User not found or incorrect PIN');
        }

        return $this->createCheckIn($user, $action);
    }

    public function createCheckIn($user, $action)
    {
        if ($action === 'check-in') {
            $user->is_active = true;
        } elseif ($action === 'check-out') {
            $user->is_active = false;
        }

        $this->userRepo->save($user);

        $checkInData = [
            'user_id' => $user->id,
            'action' => $action,
            'date' => now()
        ];

        return $this->checkInRepo->create($checkInData);
    }

    public function getUsersCheckInsToday()
    {
        return $this->checkInRepo->getUsersCheckInsToday();
    }

//    public function getUsersCheckIns($date)
//    {
//        return $this->checkInRepo->getUsersCheckIns($date);
//    }
    public function getUsersWithLastCheckIns()
    {
        return $this->userRepo->getUsersWithLastCheckin();
    }

    public function getCheckInsForUser($userId, $year, $month)
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new \Exception('User not found.', 404);
        }

        $user->check_ins = $this->checkInRepo->getCheckInsForUser($userId, $year, $month);

        return $user;
    }

    public function getCheckInsByDateRange($userId, $startDate, $endDate)
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new \Exception('User not found.', 404);
        }

        $user->check_ins = $this->checkInRepo->getCheckInsByDateRange($userId, $startDate, $endDate);

        return $user;
    }
}
