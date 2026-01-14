<p>Hello {{ $staff->name }},</p>

@if($todayFollowups->count())
    <h3 style="color:#2c7be5;">📅 Today’s Follow-ups</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align: center;">#</th>
                <th style="text-align: center;">Enquiry</th>
                <th style="text-align: center;">Type</th>
                <th style="text-align: center;">Time</th>
                <th style="text-align: center;">Subject</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todayFollowups as $fkey => $f)
                <tr>
                    <td style="text-align: center;">{{ $fkey + 1 }}</td>
                    <td style="text-align: center;">{{ $f->enquiry->enquiry_code ?? '' }} - {{ $f->enquiry->customer->company_name ?? '' }}</td>
                    <td style="text-align: center;">
                        {{ ucfirst($f->followup_type) }} ({{ ucfirst($f->sub_type) }})
                        @if ($f->followup_type == "meeting" && $f->location != NULL)
                            <br>(<strong>Location :</strong> {{ $f->location ?? '-' }})
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if ($f->followup_type === 'meeting')
                            <div><strong>From:</strong> {{ \Carbon\Carbon::parse($f->followup_from)->format('d, M Y h:i A') }}</div>
                            <div><strong>To:</strong> {{ \Carbon\Carbon::parse($f->followup_to)->format('d, M Y h:i A') }}</div>
                        @else
                            <div>{{ \Carbon\Carbon::parse($f->followup_time)->format('d, M Y h:i A') }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $f->subject }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif


@if($pastFollowups->count())
    <h3 style="color:#dc3545; margin-top:20px;">⏰ Past Pending Follow-ups (Overdue)</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align: center;">#</th>
                <th style="text-align: center;">Enquiry</th>
                <th style="text-align: center;">Type</th>
                <th style="text-align: center;">Date</th>
                <th style="text-align: center;">Subject</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pastFollowups as $pkey => $f)
                <tr>
                    <td style="text-align: center;">{{ $pkey + 1 }}</td>
                    <td style="text-align: center;">{{ $f->enquiry->enquiry_code ?? '' }} - {{ $f->enquiry->customer->company_name ?? '' }}</td>
                    <td style="text-align: center;">
                        {{ ucfirst($f->followup_type) }} ({{ ucfirst($f->sub_type) }})
                        @if ($f->followup_type == "meeting" && $f->location != NULL)
                            <br>(<strong>Location :</strong> {{ $f->location ?? '-' }})
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if ($f->followup_type === 'meeting')
                            <div><strong>From:</strong> {{ \Carbon\Carbon::parse($f->followup_from)->format('d, M Y h:i A') }}</div>
                            <div><strong>To:</strong> {{ \Carbon\Carbon::parse($f->followup_to)->format('d, M Y h:i A') }}</div>
                        @else
                            <div>{{ \Carbon\Carbon::parse($f->followup_time)->format('d, M Y h:i A') }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $f->subject }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(!$todayFollowups->count() && !$pastFollowups->count())
    <p style="margin-top:20px;">No pending follow-ups for today 🎉</p>
@else
    <p style="margin-top:20px;">Please follow up accordingly.</p>
@endif

<p style="margin-top:20px;">
    Regards,<br>
    {{ env('APP_NAME', 'Tomsher-CRM') }} Team
</p>
