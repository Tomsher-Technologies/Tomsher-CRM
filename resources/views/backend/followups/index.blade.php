@extends('backend.layouts.app',['title' => 'All Follow-ups'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Follow-ups</h5>
        @can('add_followups')
            <a href="{{ route('followups.create') }}" class="btn btn-success">Add Followups</a>
        @endcan
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('followups.index') }}" class="row g-3">
            <!-- Enquiry Filter -->
            <div class="col-md-4 mb-1">
                <label>Enquiry</label>
                <select name="enquiry_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                    <option value="">All Enquiries</option>
                    @foreach($enquiries as $enquiry)
                        <option value="{{ $enquiry->id }}" {{ request('enquiry_id') == $enquiry->id ? 'selected' : '' }}>
                            {{ $enquiry->enquiry_code }} - {{ $enquiry->customer->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @can('view_all_users_followups')
                <div class="col-md-3  mb-1">
                    <label>Users</label>
                    <select name="created_by" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endcan
        
            
            <!-- Time Range -->
            <div class="col-md-4 mb-1">
                <label>Date</label>
                <input type="text" class="form-control form-control-sm aiz-date-range" name="date_range" placeholder="Select Date" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off"  value="{{ $date_range }}">
            </div>

            <!-- Type Filter -->
            <div class="col-md-2 mb-1">
                <label>Follow-up Type</label>
                <select name="followup_type" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="call" {{ request('followup_type') == 'call' ? 'selected' : '' }}>Call</option>
                    <option value="email" {{ request('followup_type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="whatsapp" {{ request('followup_type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="meeting" {{ request('followup_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                </select>
            </div>
        
            <!-- Sub-Type Filter -->
            <div class="col-md-2 mb-1">
                <label>Sub-Type</label>
                <select name="sub_type" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="incoming" {{ request('sub_type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                    <option value="outgoing" {{ request('sub_type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                    <option value="online" {{ request('sub_type') == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="in-person" {{ request('sub_type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                </select>
            </div>

        
            <!-- Status Filter -->
            <div class="col-md-2 mb-1">
                <label>Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                </select>
            </div>
        
            <!-- Submit -->
            <div class="col-md-2 d-flex align-items-end mb-1">
                <button class="btn btn-primary w-50">Filter</button>
                <a href="{{ route('followups.index') }}" class="btn btn-secondary w-5  ml-1">Reset</a>
            </div>
        </form>

        <table class="table table-bordered aiz-table">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Follow-up Type</th>
                    <th style="width:20%">Enquiry Info</th>
                    
                    <th class="text-center">Sub-Type</th>
                    <th class="text-center">Time</th>
                   
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @can('view_followups')
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
                            <td class="text-center">{{ $key + 1 + ($followups->currentPage() - 1) * $followups->perPage() }}
                            
                            <td class="text-center">{!! $icon !!} {{ ucfirst($followup->followup_type) }}</td>
                            <td style="position: relative;">
                                <span> <strong class="text-muted fs-12">Enquiry : </strong>
                                    <a href="{{ route('enquiries.show', $followup->enquiry) }}" target="_blank" >
                                        {{ $followup->enquiry->enquiry_code ?? '' }} - {{ $followup->enquiry->customer->company_name ?? '' }}
                                    </a>
                                    <a href="javascript:void(0)" class="show-popup" data-id="{{ $followup->id }}">
                                        <i class="las la-info-circle fs-16 text-primary" style="cursor: pointer;"></i>
                                    </a>
                                </span>
                                <br>
                                @if ($followup->enquiry_status != NULL)
                                    <span> 
                                        <strong class="text-muted fs-12">Enquiry Status: </strong>
                                        <span class="text-success">({{ ucfirst(str_replace('_', ' ', $followup->enquiry_status)) }})</span>
                                    </span>
                                    <br>
                                @endif
                                
                                <span> <strong class="text-muted fs-12">Added By :</strong> {{ $followup->added_by->name ?? '' }}</span>

                                @php $primary = $followup->enquiry->customer->main_contact; @endphp
                                    <!-- Stylish Popup -->
                                    <div class="popup-card" id="popup-{{ $followup->id }}">
                                        <div class="popup-card-header">
                                            <span><i class="las la-id-card"></i> Contact Info</span>
                                            <i class="las la-times close-popup" data-id="{{ $followup->id }}"></i>
                                        </div>
                                        <div class="popup-card-body">
                                            @if ($primary->name)
                                                <div><i class="las la-user"></i> <strong>Name:</strong> {{ $primary->name }}</div>
                                            @endif
                                            @if ($primary->designation)
                                                <div><i class="las la-user-tie"></i> <strong>Designation:</strong> {{ $primary->designation }}</div>
                                            @endif
                                            @if ($primary->email)
                                                <div><i class="las la-envelope"></i> <strong>Email:</strong> {{ $primary->email }}</div>
                                            @endif
                                            @if ($primary->landline_number)
                                                <div><i class="las la-phone"></i> <strong>Landline:</strong> {{ $primary->landline_number }}</div>
                                            @endif
                                            @if ($primary->mobile_number)
                                                <div><i class="las la-mobile"></i> <strong>Mobile:</strong> {{ $primary->mobile_number }}</div>
                                            @endif
                                            @if ($primary->whatsapp_number)
                                                <div><i class="lab la-whatsapp"></i> <strong>WhatsApp:</strong> {{ $primary->whatsapp_number }}</div>
                                            @endif
                                        </div>
                                    </div>

                            </td>
                            <td class="text-center">{{ ucfirst($followup->sub_type) }}</td>
                            <td class="text-center">
                                @if ($followup->followup_type === 'meeting')
                                    <div><strong>From:</strong> {{ \Carbon\Carbon::parse($followup->followup_from)->format('d, M Y h:i A') }}</div>
                                    <div><strong>To:</strong> {{ \Carbon\Carbon::parse($followup->followup_to)->format('d, M Y h:i A') }}</div>
                                @else
                                    <div>{{ \Carbon\Carbon::parse($followup->followup_time)->format('d, M Y h:i A') }}</div>
                                @endif
                            </td>
                           
                            <td class="text-center">
                                @php
                                    $statusClass = '';
                                @endphp
                                @if ($followup->status === 'pending')
                                    @if ($followup->followup_type === 'meeting')
                                        @php
                                            $followupTime = \Carbon\Carbon::parse($followup->followup_from);
                                        @endphp
                                        {{-- $followupTime->isToday() ||  --}}
                                        @if($followupTime->isFuture())
                                            @php
                                                $statusClass = 'pending-upcoming';
                                            @endphp
                                            <span class="badge  badge-inline pending-upcoming">{{ ucfirst($followup->status) }}</span>
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
                                        {{-- $followupTime->isToday() ||  --}}
                                        @if($followupTime->isFuture())
                                            @php
                                                $statusClass = 'pending-upcoming';
                                            @endphp
                                            <span class="badge  badge-inline pending-upcoming">{{ ucfirst($followup->status) }}</span>
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
                                    <span class="badge  badge-inline completed">
                                        {{ ucfirst($followup->status) }}
                                    </span>
                                @elseif($followup->status == 'canceled')
                                    @php
                                        $statusClass = 'badge-secondary';
                                    @endphp
                                    <span class="badge  badge-inline badge-secondary">
                                        {{ ucfirst($followup->status) }}
                                    </span>
                                @elseif($followup->status == 'rescheduled')
                                    @php
                                        $statusClass = 'badge-warning';
                                    @endphp
                                    <span class="badge  badge-inline badge-warning">
                                        {{ ucfirst($followup->status) }}
                                    </span>
                                @endif
                            
                            </td>
                            <td class="text-center">
                                @can('view_followups')
                                    @php
                                        $participantNames = $followup->participants
                                            ->where('pivot.is_main', false) // exclude main participant if needed
                                            ->pluck('name')
                                            ->join(', ');
                                    @endphp
                                    <button type="button" class="btn btn-soft-warning btn-sm btn-icon btn-circle view-followup"
                                        data-toggle="modal" data-target="#followupModal"
                                        data-enquiry="{{ $followup->enquiry->enquiry_code ?? '' }} - {{ $followup->enquiry->customer->company_name ?? '' }}"
                                        data-type="{{ ucfirst($followup->followup_type) }}"
                                        data-subtype="{{ ucfirst($followup->sub_type) }}"
                                        @if ($followup->followup_type === 'meeting')
                                            data-time-from="{{ \Carbon\Carbon::parse($followup->followup_from)->format('d, M Y h:i A') }}"
                                            data-time-to="{{ \Carbon\Carbon::parse($followup->followup_to)->format('d, M Y h:i A') }}"
                                        @else
                                            data-time="{{ \Carbon\Carbon::parse($followup->followup_time)->format('d, M Y h:i A') }}"
                                        @endif
                                        data-subject="{{ $followup->subject }}" 
                                        data-post-comment="{{ $followup->post_comment }}" 
                                        data-location="{{ $followup->location }}"
                                        data-status="{{ $followup->status }}"
                                        data-statusclass="{{$statusClass}}" 
                                        data-createdby="{{ $followup->added_by->name ?? '' }}" 
                                        data-participants="{{ $participantNames }}">
                                        <i class="las la-eye"></i>
                                    </button>
                                @endcan
                                
                                @if(auth()->user()->can('edit_all_user_followups') || (auth()->user()->can('edit_followups') && $followup->created_by == auth()->id()))
                                    <a href="{{ route('followups.edit', $followup->id) }}"
                                        class="btn btn-soft-success btn-sm btn-icon btn-circle" title="Edit follow-up details">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination">
            @can('view_followups')
                {{ $followups->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
        </div>
    </div>
</div>

<div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Follow-up Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered aiz-table">
                    <tbody>
                        <tr>
                            <td style="width:25%;"><strong>Enquiry</strong></td>
                            <td><span id="modal-enquiry"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Follow-up Type</strong></td>
                            <td><span id="modal-type" ></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Sub-Type</strong></td>
                            <td><span id="modal-subtype"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Time</strong></td>
                            <td><span id="modal-time"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Pre-Follow-up Comment</strong></td>
                            <td><span id="modal-subject"></span></td>
                        </tr>
                        <tr id="modal-location-wrapper" style="display: none;">
                            <td style="width:25%;"><strong>Location</strong></td>
                            <td><span id="modal-location"></span></td>
                        </tr>

                        <tr id="modal-participants-wrapper" style="display: none;">
                            <td style="width:25%;"><strong>Participants</strong></td>
                            <td><span id="modal-participants"></span></td>
                        </tr>

                        <tr>
                            <td style="width:25%;"><strong>Status</strong></td>
                            <td><span id="modal-status" class="badge badge-inline"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Created By</strong></td>
                            <td><span id="modal-created_by"></span></td>
                        </tr>

                        <tr>
                            <td style="width:25%;"><strong>Post-Follow-up Comment</strong></td>
                            <td><span id="modal-comment"></span></td>
                        </tr>
                        
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

@endsection


@section('style')
<style>
    .popup-card {
        display: none;
        position: absolute;
        top: 40px;
        right: 0;
        width: 280px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        overflow: hidden;
        animation: fadeIn 0.3s ease;
        font-family: 'Segoe UI', sans-serif;
    }

    .popup-card-header {
        background: linear-gradient(135deg, #0058a2, #43a1ef);
        color: #fff;
        padding: 5px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        font-size: 14px;
    }

    .popup-card-header i.close-popup {
        cursor: pointer;
        font-size: 16px;
        color: #fff;
        transition: 0.3s;
    }

    .popup-card-header i.close-popup:hover {
        color: #ffc107;
    }

    .popup-card-body {
        padding: 15px 16px;
        font-size: 12px;
        color: #444;
    }

    .popup-card-body div {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .popup-card-body i {
        color: #007bff;
        font-size: 16px;
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
@endsection

@section('script')
<script>
    $(document).ready(function () {

        $(document).on('click', '.view-followup', function () {
            
            $('#modal-enquiry').text($(this).data('enquiry'));
            $('#modal-type').text($(this).data('type'));
            $('#modal-subtype').text($(this).data('subtype'));
            
            $('#modal-subject').text($(this).data('subject'));
            $('#modal-location').text($(this).data('location'));
            $('#modal-created_by').text($(this).data('createdby'));
            $('#modal-comment').text($(this).data('post-comment'));
            $('#modal-participants').text($(this).data('participants'));
            
            let status = $(this).data('status');
            let statusclass = $(this).data('statusclass');
            let badge = $('#modal-status');

            badge.text(status.charAt(0).toUpperCase() + status.slice(1));
            badge.removeClass().addClass('badge badge-inline');
            badge.addClass(statusclass);

            var timeDisplay = '';
            if ($(this).data('type').toLowerCase() === 'meeting') {
                var from = $(this).data('time-from');
                var to = $(this).data('time-to');
                timeDisplay = `<div><strong>From:</strong> ${from}</div><div><strong>To:</strong> ${to}</div>`;
                $('#modal-location-wrapper').show();
                $('#modal-participants-wrapper').show();
            } else {
                timeDisplay = $(this).data('time');
                $('#modal-location-wrapper').hide();
                $('#modal-participants-wrapper').hide();
            }
            $('#modal-time').html(timeDisplay);
        });

        $(document).on('click', '.show-popup', function (e) {
            e.stopPropagation();
            $('.popup-card').hide(); // hide others
            const id = $(this).data('id');
            $('#popup-' + id).fadeIn(200);
        });

        $(document).on('click', '.close-popup', function (e) {
            e.stopPropagation();
            const id = $(this).data('id');
            $('#popup-' + id).fadeOut(200);
        });

        $(document).on('click', function () {
            $('.popup-card').fadeOut(200);
        });

        $(document).on('click', '.popup-card', function (e) {
            e.stopPropagation(); // prevent outside click from closing if clicking inside
        });
    });
</script>

@endsection
