<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vacation;
use Illuminate\Console\Command;

class VacationRefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacation:refresh';

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
        $users = User::all();
        $thisYear = now()->year;
        $lastYear = now()->subYear()->year;
        $vacations = Vacation::whereIn('user_id', $users->pluck('id')->toArray())->where('year', $lastYear)->get();
        $insertData = [];
        foreach ($vacations as $vacation) {
            $insertData[] = [
                'user_id' => $vacation->user_id,
                'number_of_days' => $vacation->number_of_days,
                'year' => $thisYear,
                'days_left' => $vacation->number_of_days,
                'carried_days' => $vacation->days_left,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Vacation::insert($insertData);
        $this->info('Refreshed ' . count($insertData) . ' vacations for year ' . $thisYear);
    }
}
