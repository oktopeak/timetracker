<?php

namespace App\Jobs;

use App\Mail\AnniversaryMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AnniversaryNotificationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;

    protected User $user;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('AnniversaryNotificationJob started');
        Mail::to($this->fetchRecipients())->send(new AnniversaryMail($this->user));
    }

    private function fetchRecipients(): Collection
    {
        return User::select(['email', 'name'])
            ->where('role', '=', User::ROLE_ADMIN)
            ->get();
    }
}
