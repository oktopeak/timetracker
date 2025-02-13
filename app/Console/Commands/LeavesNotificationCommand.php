<?php

namespace App\Console\Commands;

use App\Jobs\LeaveNotificationMailJob;
use App\Jobs\LeaveSlackNotificationJob;
use App\Models\Leave;
use App\Models\User;
use App\Models\Vacation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeavesNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:notification';

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
        try {
            $fiveDays = now()->addDays(5)->format('Y-m-d');
            $tomorrow = now()->addDay()->format('Y-m-d');
            $today = now()->format('Y-m-d');
            $leavesFiveDays = Leave::whereDate('leave_start', $fiveDays)->with(['leave_type', 'user'])->get();
            $leavesTomorrow = Leave::whereDate('leave_start', $tomorrow)->with(['leave_type', 'user'])->get();
            $leavesToday = Leave::whereDate('leave_start', $today)->with(['leave_type', 'user'])->get();

            foreach ($leavesFiveDays as $leave) {
                LeaveNotificationMailJob::dispatch($leave, 'five days');
            }
            foreach ($leavesTomorrow as $leave) {
                LeaveNotificationMailJob::dispatch($leave);
                LeaveSlackNotificationJob::dispatch($leave, 'tomorrow');
            }
            foreach ($leavesToday as $leave) {
                LeaveSlackNotificationJob::dispatch($leave, 'today');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
