<?php

namespace App\Services;

use App\Repositories\Implementations\HolidayRepository;

class HolidayService
{
    public function __construct(private readonly HolidayRepository $holidayRepo) {}

    public function getAll()
    {
        return $this->holidayRepo->getAll();
    }

    public function createHoliday($data)
    {
        $holiday = $this->holidayRepo->create($data);
        return $holiday;
    }

    public function update($id, $data)
    {
        $holiday = $this->holidayRepo->findById($id);

        if (!$holiday) {
            throw new \Exception('Holiday not found.', 404);
        }

        return $this->holidayRepo->update($holiday, $data);
    }

    public function destroy($id)
    {
        $holiday = $this->holidayRepo->findById($id);

        if (!$holiday) {
            throw new \Exception('Holiday not found.', 404);
        }

        return $this->holidayRepo->destroy($holiday);
    }
}
