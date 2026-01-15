<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class DailyFollowupMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $staff;
    public $todayFollowups;
    public $pastFollowups;

    public function __construct($staff, $todayFollowups, $pastFollowups)
    {
        $this->staff = $staff;
        $this->todayFollowups = $todayFollowups;
        $this->pastFollowups = $pastFollowups;
    }

    public function build()
    {
        $today = Carbon::today()->format('d M Y');
        $staffName = $this->staff->name ?? 'Staff';
        
        return $this->subject("Daily Follow-up Reminder for {$staffName} - {$today}")
                    ->view('emails.daily_followups');
    }
}
