<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use Illuminate\Console\Command;

class CreateHolidaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holidays:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentYear = now()->year;

        $holidays = config('holidays.holidays');

        foreach ($holidays as $holiday) {
            $holidayDate = "{$currentYear}-{$holiday['date']}";

            $existingHoliday = Holiday::where('date', $holidayDate)->first();
            if (!$existingHoliday) {
                Holiday::create([
                    'name' => $holiday['name'],
                    'date' => $holidayDate,
                ]);
            }
        }
        $this->info('Holiday creation process completed.');
    }
}
