<?php

namespace App\Jobs;

use App\Mail\LeavesMail;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class LeaveNotificationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected Leave $leave;
    protected string $timeframe;
    /**
     * Create a new job instance.
     */
    public function __construct(Leave  $leave, string $timeframe = '')
    {
        $this->leave = $leave;
        $this->timeframe = $timeframe;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->fetchRecipients())->send(new LeavesMail($this->leave, $this->timeframe));
    }

    private function fetchRecipients(): Collection
    {
        return User::select(['email', 'name'])
            ->where('role', '=', User::ROLE_ADMIN)
            ->get();
    }
}
