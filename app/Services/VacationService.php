<?php

namespace App\Services;

use App\Http\Resources\VacationResource;
use App\Repositories\Implementations\UserRepository;
use App\Repositories\Implementations\VacationRepository;

class VacationService
{
    public function __construct(private readonly VacationRepository $vacationRepo, private readonly UserRepository $userRepo) {}

    public function createVacation($data)
    {
        $data['days_left'] = $data['days_left'] ?? $data['number_of_days'];
        return $this->vacationRepo->create($data);
    }

    public function createVacationFromUser($user, $data)
    {
        $data['user_id'] = $user->id;
        $data['year'] = now()->year;
        $data['days_left'] = $data['number_of_days'];

        return $this->vacationRepo->create($data);
    }


    public function updateVacation($userId, $data)
    {
        $vacation = $this->vacationRepo->findByUserId($userId);

        if (!$vacation) {
            throw new \Exception('Vacation not found.', 404);
        }

        $newNumberOfDays = $data['number_of_days'];
        $currentNumberOfDays = $vacation->number_of_days;
        $daysToAdd = $newNumberOfDays - $currentNumberOfDays;

        if ($daysToAdd > 0) {
            $vacation->number_of_days += $daysToAdd;
            $vacation->days_left += $daysToAdd;
        } elseif ($daysToAdd < 0) {
            $vacation->number_of_days += $daysToAdd;
            $vacation->days_left += $daysToAdd;
        }

        $this->vacationRepo->save($vacation);

        return $vacation;
    }

    public function getUserCurrentVacation($userId)
    {
        return $this->vacationRepo->findByUserId($userId);
    }

    public function getUsersVacations($userId)
    {
        $vacations = $this->vacationRepo->getUsersVacationHistory($userId);
        return VacationResource::collection($vacations);
    }
}
