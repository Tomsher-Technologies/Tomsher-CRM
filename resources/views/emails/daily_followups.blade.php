<div style="font-family: 'Segoe UI', Arial, sans-serif; background:#f5f7fb; padding:20px;">
    <div style="max-width:800px; margin:auto; background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
        
        <!-- Header -->
        <div style="background:#2c7be5; color:#fff; padding:20px; text-align:center;">
            <h2 style="margin:0;">Follow-up Summary</h2>
        </div>

        <!-- Body -->
        <div style="padding:20px; color:#333;">
            <p>Hello <strong>{{ $staff->name }}</strong>,</p>

            <!-- Today's Followups -->
            @if($todayFollowups->count())
                <h3 style="color:#2c7be5; margin-top:20px;">📅 Today’s Follow-ups</h3>
                <table width="100%" style="border-collapse:collapse; table-layout:fixed; margin-top:10px;">
                    <thead>
                        <tr style="background:#eef4ff;">
                            <th style="width:5%; padding:10px; border:1px solid #ddd;">#</th>
                            <th style="width:20%; padding:10px; border:1px solid #ddd;">Enquiry</th>
                            <th style="width:20%; padding:10px; border:1px solid #ddd;">Type</th>
                            <th style="width:20%; padding:10px; border:1px solid #ddd;">Time</th>
                            <th style="width:35%; padding:10px; border:1px solid #ddd;">Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayFollowups as $fkey => $f)
                            <tr>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center;">{{ $fkey + 1 }}</td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    {{ $f->enquiry->enquiry_code ?? '' }}<br>
                                    <small>{{ $f->enquiry->customer->company_name ?? '' }}</small>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    {{ ucfirst($f->followup_type) }} ({{ ucfirst($f->sub_type) }})
                                    @if ($f->followup_type == "meeting" && $f->location != NULL)
                                        <div><strong>Location:</strong> {{ $f->location ?? '-' }}</div>
                                    @endif
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    @if ($f->followup_type === 'meeting')
                                        <div><strong>From:</strong> {{ \Carbon\Carbon::parse($f->followup_from)->format('d M Y, h:i A') }}</div>
                                        <div><strong>To:</strong> {{ \Carbon\Carbon::parse($f->followup_to)->format('d M Y, h:i A') }}</div>
                                    @else
                                        {{ \Carbon\Carbon::parse($f->followup_time)->format('d M Y, h:i A') }}
                                    @endif
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    {{ $f->subject }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Past Followups -->
            @if($pastFollowups->count())
                <h3 style="color:#dc3545; margin-top:30px;">⏰ Overdue Follow-ups</h3>
                <table width="100%" style="border-collapse:collapse; table-layout:fixed; margin-top:10px;">
                    <thead>
                        <tr style="background:#fdecec;">
                            <th style="width:5%; padding:10px; border:1px solid #ddd;">#</th>
                            <th style="width:20%; padding:10px; border:1px solid #ddd;">Enquiry</th>
                            <th style="width:20%; padding:10px; border:1px solid #ddd;">Type</th>
                            <th style="width:20%; padding:10px; border:1px solid #ddd;">Date</th>
                            <th style="width:35%; padding:10px; border:1px solid #ddd;">Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastFollowups as $pkey => $f)
                            <tr>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center;">{{ $pkey + 1 }}</td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    {{ $f->enquiry->enquiry_code ?? '' }}<br>
                                    <small>{{ $f->enquiry->customer->company_name ?? '' }}</small>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    {{ ucfirst($f->followup_type) }} ({{ ucfirst($f->sub_type) }})
                                    @if ($f->followup_type == "meeting" && $f->location != NULL)
                                        <div><strong>Location:</strong> {{ $f->location ?? '-' }}</div>
                                    @endif
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    @if ($f->followup_type === 'meeting')
                                        <div><strong>From:</strong> {{ \Carbon\Carbon::parse($f->followup_from)->format('d M Y, h:i A') }}</div>
                                        <div><strong>To:</strong> {{ \Carbon\Carbon::parse($f->followup_to)->format('d M Y, h:i A') }}</div>
                                    @else
                                        {{ \Carbon\Carbon::parse($f->followup_time)->format('d M Y, h:i A') }}
                                    @endif
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; text-align:center; word-break:break-word;">
                                    {{ $f->subject }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- No Data -->
            @if(!$todayFollowups->count() && !$pastFollowups->count())
                <div style="margin-top:20px; padding:15px; background:#e6f7ee; border-radius:6px; text-align:center;">
                    🎉 No pending follow-ups for today
                </div>
            @else
                <p style="margin-top:20px; font-weight:500;">Please follow up accordingly.</p>
            @endif

            <!-- Footer -->
            <div style="margin-top:30px; border-top:1px solid #eee; padding-top:15px; font-size:14px; color:#777;">
                Regards,<br>
                <strong>{{ env('APP_NAME', 'Tomsher-CRM') }}</strong> Team
            </div>
        </div>
    </div>
</div>