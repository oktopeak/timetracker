<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnniversaryMail extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;
    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $title = "Employee 6-Month Anniversary Reminder";
        return $this->from(config('app.company_mail'), config('app.company_name'))
            ->subject($title)
            ->markdown('mail.leaves-mail')
            ->with([
                'title' => $title,
                'message' => $this->generateMessage(),
            ]);
    }

    private function generateMessage(): string
    {
        return "Your employee {$this->user->fullName} will reach their 6-month anniversary since joining the team on " . now()->addDays(5)->format('Y-m-d') . ".";
    }
}
