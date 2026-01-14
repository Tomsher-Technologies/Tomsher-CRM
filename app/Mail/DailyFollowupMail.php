<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
        return $this->subject('Daily Follow-up Reminder')
                    ->view('emails.daily_followups');
    }
}
