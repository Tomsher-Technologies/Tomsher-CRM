<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EnquiryFollowup;
use App\Mail\DailyFollowupMail;

use Illuminate\Support\Facades\Mail;

class SendDailyFollowupMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-followup-mails';

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
        $today = Carbon::today();
        $staffs = User::where('followup_mail_status', 1)->where('banned',0)->get();

        foreach ($staffs as $staff) {
            $items = EnquiryFollowup::where('status', 'pending')
                ->whereHas('enquiry', function ($q) use ($staff) {
                    $q->where('owner_id', $staff->id);
                })
                ->where(function ($q) use ($today) {
                    $q->whereDate('followup_time', '<=', $today)
                    ->orWhereDate('followup_from', '<=', $today);
                })
                ->with('enquiry')
                ->get();

            if (!$items->isEmpty()) {
                $grouped = $items->groupBy(function ($followup) use ($today) {

                    $date = $followup->followup_type === 'meeting'
                        ? Carbon::parse($followup->followup_from)
                        : Carbon::parse($followup->followup_time);

                    return $date->isToday() ? 'today' : 'past';
                });

                $todayFollowups = $grouped->get('today', collect());
                $pastFollowups  = $grouped->get('past', collect());
            }else{
                $todayFollowups = collect();
                $pastFollowups  = collect();
            }

            $ccEmails = $staff->followup_cc ? json_decode($staff->followup_cc) : [];
            Mail::to($staff->email)
                ->cc($ccEmails)
                ->queue(new DailyFollowupMail(
                    $staff,
                    $todayFollowups,
                    $pastFollowups
                ));
            
        }
    }
}
