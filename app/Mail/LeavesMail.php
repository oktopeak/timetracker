<?php

namespace App\Mail;

use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeavesMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private readonly Leave $leave, private readonly string $timeframe = '')
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $title = $this->generateSubject();
        return $this->from(config('app.company_mail'), config('app.company_name'))
            ->subject($title)
            ->markdown('mail.leaves-mail')
            ->with([
                'title' => $title,
                'message' => $this->generateMessage(),
            ]);
    }

    private function generateSubject(): string
    {
        return match ($this->timeframe) {
            'five days' => 'Leave notification for five days from now (' . now()->addDays(5)->format('Y-m-d') . ')',
            default => 'Leave notification for tomorrow (' . now()->addDay()->format('Y-m-d') . ')',
        };
    }
    private function generateMessage(): string
    {
        return "Your employee {$this->leave->user->full_name} is going on " . $this->getLeaveTypeName($this->leave->leave_type) . " from {$this->leave->leave_start->format('Y-m-d')} to {$this->leave->leave_end->format('Y-m-d')}.";
    }
    private function getLeaveTypeName(LeaveType $leaveType): string
    {
        return match ($leaveType->name) {
            LeaveType::SICK_LEAVE => 'sick leave',
            LeaveType::PERSONAL_LEAVE => 'personal leave',
            default => 'annual leave',
        };
    }
}
