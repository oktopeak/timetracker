<?php

namespace App\Services;

use App\Repositories\Implementations\PersonalHolidayRepository;

class PersonalHolidayService
{
    public function __construct(private readonly PersonalHolidayRepository $personalHolidayRepo) {}

    public function createHoliday($data)
    {
        $holiday = $this->personalHolidayRepo->create($data);
        return $holiday;
    }

    public function getUsersHolidays($id)
    {
        $holidays = $this->personalHolidayRepo->findByUserId($id);
        if (!$holidays) {
            throw new \Exception('User not found.', 404);
        }
        return $holidays;
    }

    public function updateHolidayDate($id, $data)
    {
        $holiday = $this->personalHolidayRepo->findById($id);

        if (!$holiday || !$holiday->user) {
            throw new \Exception('Personal holiday not found.', 404);
        }
        return $this->personalHolidayRepo->update($holiday, $data);
    }

    public function destroy($id)
    {
        $holiday = $this->personalHolidayRepo->findById($id);

        if (!$holiday || !$holiday->user) {
            throw new \Exception('Personal holiday not found', 404);
        }

        return $this->personalHolidayRepo->destroy($holiday);
    }
}
