<?php

namespace App\Mail;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    protected $leaveStatus;
    protected Leave $leave;
    /**
     * Create a new message instance.
     */
    public function __construct(Leave $leave, $leaveStatus)
    {
        $this->leave = $leave;
        $this->leaveStatus = $leaveStatus;
    }

    public function build()
    {
        $title = "Leave request status";
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
        return "Dear {$this->leave->user->full_name}, your leave request for {$this->leave->leave_type->name} from {$this->leave->leave_start->format('Y-m-d')} to {$this->leave->leave_end->format('Y-m-d')} has been {$this->leaveStatus}.";
    }
}
