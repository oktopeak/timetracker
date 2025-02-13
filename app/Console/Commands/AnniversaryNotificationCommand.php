<?php

namespace App\Console\Commands;

use App\Jobs\AnniversaryNotificationMailJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnniversaryNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anniversary:notification';

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
        try{
            $today = now();
            $fiveDaysFromNow = $today->addDays(5);

            $users = User::whereDate('joined_team', '=', $fiveDaysFromNow->subMonths(6)->format('Y-m-d'))
                ->get();

            foreach ($users as $user) {
                AnniversaryNotificationMailJob::dispatch($user);
            }
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
