<?php

namespace App\Jobs;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LeaveSlackNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Leave $leave;
    protected string $timeframe;
    /**
     * Create a new job instance.
     */
    public function __construct(Leave $leave, string $timeframe)
    {
        $this->leave = $leave;
        $this->timeframe = $timeframe;
    }

    public function handle(): void
    {
        $message = $this->generateSlackMessage($this->leave);

        Log::info('Slack Notification Message:', ['message' => $message]);
        //SlackAlert::message($message);
    }
    private function generateSlackMessage($leave): string
    {
        $message = "Daily Leave Notification\n\n";
        $message .= "*Leaves for {$this->timeframe}:* \n";
        $message .= "- {$leave->user->full_name}: {$leave->leave_type->name} ({$leave->leave_start->format('Y-m-d')} to {$leave->leave_end->format('Y-m-d')})\n";
        return $message;
    }
}
