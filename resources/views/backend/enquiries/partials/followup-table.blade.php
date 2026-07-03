<div class="mt-3">
    <h6 class="mb-1">Follow-ups</h6>
    <table class="table table-bordered aiz-table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Follow-up</th>
                <th class="text-center">Type</th>
                <th class="text-center">Time</th>
                <th class="text-start" style="width:20%;">Pre Comment</th>
                <th class="text-start" style="width:20%;">Post Comment</th>
                <th class="text-center">Created By</th>
                {{-- <th>Location</th> --}}
                <th class="text-center">Followup Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($followups as $key => $followup)
                @php
                    $icon = '';
                    switch ($followup->followup_type) {
                        case 'call': $icon = '<i class="las fs-18 la-phone text-primary me-1"></i>'; break;
                        case 'email': $icon = '<i class="las fs-18 la-envelope text-danger me-1"></i>'; break;
                        case 'whatsapp': $icon = '<i class="lab fs-18 la-whatsapp text-success me-1"></i>'; break;
                        case 'meeting': $icon = '<i class="las fs-18 la-handshake text-warning me-1"></i>'; break;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td class="text-center">{!! $icon !!} {{ ucfirst($followup->followup_type) }}</td>
                    <td class="text-center">{{ ucfirst($followup->sub_type) }}</td>
                    <td class="text-center">
                        @if ($followup->followup_type === 'meeting')
                            <div><strong>From:</strong> {{ \Carbon\Carbon::parse($followup->followup_from)->format('d, M Y h:i A') }}</div>
                            <div><strong>To:</strong> {{ \Carbon\Carbon::parse($followup->followup_to)->format('d, M Y h:i A') }}</div>
                        @else
                            <div>{{ \Carbon\Carbon::parse($followup->followup_time)->format('d, M Y h:i A') }}</div>
                        @endif
                    </td>
                    <td class="text-start">{{ $followup->subject ?? '-' }}</td>
                    <td class="text-start">{{ $followup->post_comment ?? '-' }}</td>
                    <td class="text-center">{{ $followup->added_by->name ?? 'System' }}</td>
                    {{-- <td>{{ $followup->location ?? '-' }}</td> --}}
                    <td class="text-center">
                        @php
                            $statusClass = '';
                        @endphp
                        @if ($followup->status === 'pending')
                            @if ($followup->followup_type === 'meeting')
                                @php
                                    $followupTime = \Carbon\Carbon::parse($followup->followup_from);
                                @endphp
                                @if($followupTime->isFuture())
                                    @php
                                        $statusClass = 'pending-upcoming';
                                    @endphp
                                    <span class="badge badge-inline pending-upcoming">{{ ucfirst($followup->status) }}</span>
                                @else
                                    @php
                                        $statusClass = 'pending-due';
                                    @endphp
                                    <span class="badge badge-inline pending-due">{{ ucfirst($followup->status) }}</span>
                                @endif
                            @else
                                @php
                                    $followupTime = \Carbon\Carbon::parse($followup->followup_time);
                                @endphp
                                @if($followupTime->isFuture())
                                    @php
                                        $statusClass = 'pending-upcoming';
                                    @endphp
                                    <span class="badge badge-inline pending-upcoming">{{ ucfirst($followup->status) }}</span>
                                @else
                                    @php
                                        $statusClass = 'pending-due';
                                    @endphp
                                    <span class="badge badge-inline pending-due">{{ ucfirst($followup->status) }}</span>
                                @endif
                            @endif
                        @elseif($followup->status == 'completed')
                            @php
                                $statusClass = 'completed';
                            @endphp
                            <span class="badge badge-inline completed">
                                {{ ucfirst($followup->status) }}
                            </span>
                        @elseif($followup->status == 'canceled')
                            @php
                                $statusClass = 'badge-secondary';
                            @endphp
                            <span class="badge badge-inline badge-secondary">
                                {{ ucfirst($followup->status) }}
                            </span>
                        @elseif($followup->status == 'rescheduled')
                            @php
                                $statusClass = 'badge-warning';
                            @endphp
                            <span class="badge badge-inline badge-warning">
                                {{ ucfirst($followup->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $participantNames = $followup->participants
                                ->where('pivot.is_main', false)
                                ->pluck('name')
                                ->join(', ');
                        @endphp
                        @php
                            $participantIds = $followup->participants
                                ->where('pivot.is_main', false)
                                ->pluck('id')
                                ->join(',');
                        @endphp
                        <button type="button" class="btn btn-soft-info btn-sm btn-icon btn-circle view-followup"
                            data-toggle="modal" data-target="#followupModal"
                            data-enquiry="{{ $followup->enquiry->enquiry_code ?? '' }} - {{ $followup->enquiry->customer->company_name ?? '' }}"
                            data-type="{{ ucfirst($followup->followup_type) }}"
                            data-subtype="{{ ucfirst($followup->sub_type) }}" 
                            data-followup-status="{{ $followup->status }}"
                            @if ($followup->followup_type === 'meeting')
                                data-time-from="{{ \Carbon\Carbon::parse($followup->followup_from)->format('d, M Y h:i A') }}"
                                data-time-to="{{ \Carbon\Carbon::parse($followup->followup_to)->format('d, M Y h:i A') }}"
                            @else
                                data-time="{{ \Carbon\Carbon::parse($followup->followup_time)->format('d, M Y h:i A') }}"
                            @endif
                            data-raw-time="{{ $followup->followup_time ? \Carbon\Carbon::parse($followup->followup_time)->format('Y-m-d H:i:s') : '' }}"
                            data-raw-from="{{ $followup->followup_from ? \Carbon\Carbon::parse($followup->followup_from)->format('Y-m-d H:i:s') : '' }}"
                            data-raw-to="{{ $followup->followup_to ? \Carbon\Carbon::parse($followup->followup_to)->format('Y-m-d H:i:s') : '' }}"
                            data-subject="{{ $followup->subject }}"
                            data-post-comment="{{ $followup->post_comment }}"
                            data-location="{{ $followup->location }}"
                            data-status="{{ $followup->status }}"
                            data-statusclass="{{$statusClass}}"
                            data-createdby="{{ $followup->added_by->name ?? '' }}"
                            data-participants="{{ $participantNames }}"
                            data-participant-ids="{{ $participantIds }}"
                            data-followup-id="{{ $followup->id }}"
                        >   
                            <i class="las la-edit"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
