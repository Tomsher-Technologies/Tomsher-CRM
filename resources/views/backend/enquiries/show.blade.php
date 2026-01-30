@extends('backend.layouts.app',['title' => 'Show Enquiry'])
@section('content')
<style>
    .primary-contact{
        border: 2px solid #0abb75 !important;
    }
    .info-label {
        font-weight: 600;
        color: #000;
        font-size: 12px;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 12px;
        color: #333;
        margin-bottom: 16px;
    }
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        border-left: 5px solid #0d6efd;
        padding-left: 12px;
        margin-bottom: 15px;
    }
    .info-box {
        background-color: #fff;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }

     .proposal-select-card {
        border: 2px solid #ccc;
        transition: 0.2s;
    }
    .proposal-select-card.selected {
        border-color: #007bff;
        background-color: #e9f3ff;
    }

    /* Icon-only Change Status button */
    .btn-change-status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #94afff;
        color: #000000;
        border-radius: 50%;
        /* width: calc(2.02rem + 2px);
        height: calc(2.02rem + 2px); */
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn i {
        font-size: 14px;
    }

    .btn-change-status-icon i {
        font-size: 13px;
    }
    
    .btn-change-status-icon:hover {
        background: #0542b3;
        color: #fff;
        /* transform: scale(1.1); */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

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

<div class="container">
    <div class="d-flex align-items-center mb-4">
        <div class="row">
            <div class="col">
                @can('edit_enquiries')
                    <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-success btn-sm"><i class="las la-edit fs-16" style="margin-top: 3px;"></i> Edit Enquiry</a>
                @endcan

                @can('add_followups')
                    <a href="{{ route('followups.create', $enquiry->id) }}" class="btn btn-secondary btn-sm"><i class="las la-calendar-plus fs-16" style="margin-top: 2px;"></i> Add Followup</a>
                @endcan

                <a href="javascript:void(0)" class="btn btn-dark btn-sm change-status-btn"  data-id="{{ $enquiry->id }}" data-status="{{ $enquiry->status }}">Change Status</a>

                <a href="{{ Session::has('enquiries_last_url') ? Session::get('enquiries_last_url') : route('enquiries.index') }}" class="btn btn-primary btn-sm"><i class="las la-arrow-left fs-16" style="margin-top: 2px;"></i> Back to List</a>
            </div>
        </div>
        
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enquiry Details</h5>
           

        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="text-muted">Customer</div>
                    <div class="mb-1"><strong>{{ $enquiry->customer->company_name.' ['.$enquiry->customer->customer_code.']' }}</strong></div>

                    <div class="text-muted">Enquiry Date</div>
                    <div class="mb-1">{{ $enquiry->enquiry_date ? \Carbon\Carbon::parse($enquiry->enquiry_date)->format('d M Y') : '-' }}</div>

                    <div class="text-muted">Enquiry Source</div>
                    <div class="mb-1">{{ $enquiry->source->name ?? '-' }}</div>

                    <div class="text-muted">Enquiry Owner</div>
                    <div class="mb-1">{{ $enquiry->owner->name ?? '-' }}</div>

                    <div class="text-muted">Project Title</div>
                    <div class="mb-1">{{ $enquiry->project_title ?? '' }}</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted">Added By</div>
                    <div class="mb-1">{{ $enquiry->addedBy->name ?? '-' }}</div>

                    <div class="text-muted">Current Status</div>
                    <div class="mb-1">
                        @php
                            $statuses = getEnquiryStatuses();
                        @endphp
                        <span class="badge  badge-inline " style="background: {{$statuses[$enquiry->status]['bg'] ?? '' }}; color:{{$statuses[$enquiry->status]['list_color'] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                        </span>
                    
                    </div>

                    <div class="text-muted">Project Categories</div>
                    @if($enquiry->projectTypes->count())
                        <ul class="list-unstyled">
                            @foreach($enquiry->projectTypes as $type)
                                <li><i class="las la-square mr-1"></i> {{ $type->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">-</div>
                    @endif
                </div>
            </div>

            <hr>
           
            <h6 class="mb-3">Project Details</h6>
            <p>{!! nl2br(e($enquiry->project_details)) !!}</p>

            @if($enquiry->comments)
                <div class="mt-4 mb-3">Internal Comments</div>
                <div class="text-muted">{!! nl2br(e($enquiry->comments)) !!}</div>
            @endif
            <hr>
            <p class="text-right text-muted">
                <small>Last updated on {{ $enquiry->updated_at->format('d M Y h:i A') }}</small>
            </p>

            <div class="info-box">
                <div class="section-title"><i class="fas fa-building me-1"></i> Company Information</div>
                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="text-muted">Customer Code</div>
                        <div class="mb-1">{{ $enquiry->customer->customer_code }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Company Name</div>
                        <div class="mb-1">{{ $enquiry->customer->company_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Company Email</div>
                        <div class="mb-1">{{ $enquiry->customer->company_email ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Industry</div>
                        <div class="mb-1">{{ $enquiry->customer->industry->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Website</div>
                        <div class="mb-1">
                            @if($enquiry->customer->website_link)
                                <a href="{{ $enquiry->customer->website_link }}" target="_blank">{{ $enquiry->customer->website_link }}</a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Registered Country</div>
                        <div class="mb-1">{{ $enquiry->customer->country->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Emirate</div>
                        <div class="mb-1">{{ $enquiry->customer->uae_emirate->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">New to Company</div>
                        <div class="mb-1">
                            @if($enquiry->customer->ntc)
                                <span class="badge bg-success badge-md">Yes</span>
                            @else
                                <span class="badge bg-warning badge-md">No</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Google Map</div>
                        <div class="mb-1">
                            @if($enquiry->customer->google_location)
                                <a href="{{ $enquiry->customer->google_location }}" target="_blank" class="text-primary">View Location</a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Address</div>
                        <div class="mb-1">{{ $enquiry->customer->company_address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <!-- Contact Persons -->
            <div class="info-box">
                <div class="section-title"><i class="fas fa-users me-1"></i> Contact Persons</div>
                <div class="row g-2">
                    @forelse($enquiry->customer->contacts as $contact)
                        <div class="col-md-6 mt-2">
                            <div class="bg-white border rounded px-4 py-2 shadow-sm h-100 {{ ($contact->is_primary == 1) ? 'primary-contact' : ''}}">
                                <div class="row gy-2">
                                    <div class="col-md-6">
                                        <div class="text-muted">Name 
                                            @if($contact->is_primary)
                                                <span class="badge bg-success badge-inline badge-sm ml-1 text-white">Primary</span>
                                            @endif
                                        </div>
                                        <div class="mb-1">{{ $contact->name }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted">Email</div>
                                        <div class="mb-1">{{ $contact->email ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted">Landline</div>
                                        <div class="mb-1">{{ $contact->landline_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted">Mobile</div>
                                        <div class="mb-1">{{ $contact->mobile_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted">WhatsApp</div>
                                        <div class="mb-1">{{ $contact->whatsapp_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted">Designation</div>
                                        <div class="mb-1">{{ $contact->designation ?? 'N/A' }}</div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No contact persons added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    

    <div class="card">
        <div class="card-header">
            <h6 class="mt-1">Milestone Timeline</h6>
        </div>
        <div class="card-body">
            <ul class="timeline">
                @php
                    $timeline = $enquiry->statusHistories()->orderBy('status_date','asc')->get();
                @endphp
                @foreach($timeline as $history)
                    <li class="mt-3">
                        <h6>
                            <strong >{{ ucwords(str_replace('_',' ', $history->status)) }}</strong>
                            @if($history->status === 'preparing_scope')
                                <a href="{{ route('enquiry-scopes.show', $enquiry->scopeOfWork->id) }}" 
                                            class="btn btn-info btn-sm btn-icon btn-circle ml-1" 
                                            title="View Scope of Work">
                                                <i class="las la-file-alt" style="margin-top: 2px;"></i>
                                            </a>
                            @endif
                        </h6>

                        

                        <small class="text-muted">on {{ \Carbon\Carbon::parse($history->status_date)->format('d M Y') }}</small>
                        <small class="text-muted">&nbsp; By: {{ $history->changedBy->name ?? 'System' }}</small>

                        @if($history->status === 'proposal_submitted')
                            @php
                                $proposalItems = $history->proposalItems()->get();
                            @endphp
                            @if(!empty($proposalItems) && $proposalItems->count() > 0)
                                <br>
                                <table class="table table-bordered aiz-table w-75 mt-2">
                                    <thead>
                                        <tr>
                                            <th class="text-center w-10">#</th>
                                            <th class="text-start w-50">Title</th>
                                            <th class="text-center w-20">Cost</th>
                                            <th class="text-center w-10">Internal Days</th>
                                            <th class="text-center w-10">Client Days</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($proposalItems as $it => $items)
                                            @php
                                                $selected = ($items->selected == 1) ? 'background: lightgreen;' : '';
                                            @endphp
                                            <tr style="{{ $selected }}">
                                                <td class="text-center">{{ $it+1 }}</td>
                                                <td class="text-start">{{ $items->title }}</td>
                                                <td class="text-center">{{ $items->cost }}</td>
                                                <td class="text-center">{{ $items->internal_days }}</td>
                                                <td class="text-center">{{ $items->client_days }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No proposal items found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            @endif
                            
                           
                        @endif

                        @if(!empty($history->approved_cost) && $history->approved_cost != 0.00)
                            <br><span class="text-success mt-1">Approved Cost: AED {{ $history->approved_cost }}</span>
                        @endif
                        @if($history->comment)
                            <p class="mt-1">{{ $history->comment }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
           
        </div>
    </div>

    @if ($enquiry->transferHistories->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h6 class="mt-1">Transfer History</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered aiz-table">
                    <thead>
                        <tr>
                            <th>Owner Name</th>
                            <th>Transferred By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enquiry->transferHistories as $history)
                            <tr>
                                <td>{{ $history->toUser->name ?? 'N/A' }}</td>
                                <td>{{ $history->fromUser->name ?? '-' }}</td>
                                <td>{{ $history->created_at->format('d, M Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    
    @if ($enquiry->followups->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h6 class="mt-1">Follow-ups</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered aiz-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Follow-up</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Time</th>
                            <th>Location</th>
                            <th class="text-center">Followup Status</th>
                            <th class="text-center">Enquiry Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enquiry->followups as $key => $followup)
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
                                <td>{{ $followup->location ?? '-' }}</td>
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
                                    @if ($followup->enquiry_status != NULL)
                                        <span class="text-success">({{ ucfirst(str_replace('_', ' ', $followup->enquiry_status)) }})</span>
                                    @endif
                                </td>
                                <td class="text-center">
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
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

    <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="status-change-form" action="{{ route('enquiries.changeStatus') }}" method="POST">
                @csrf
                <input type="hidden" name="enquiry_id" id="status-enquiry-id">
                <input type="hidden" id="original-status">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Enquiry Status</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    
                    <div class="modal-body">
                        <div id="status-modal-errors" class="alert alert-danger" style="display:none;"></div>

                        <div class="form-group">
                            <label ><b>Status</b></label>
                            <select name="status" id="enquiry-status-select" class="form-control form-control-sm aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select</option>
                                @foreach ($statuses as $key => $data)
                                    <option value="{{ $key }}"
                                        data-content="
                                            <div style='display: flex; align-items: center;'>
                                                <div style='width: 16px; height: 16px; background: {{ $data['bg'] }}; border-radius: 4px; margin-right: 8px;'></div>
                                                <span style='color: {{ $data['filter_color'] }}; font-weight: 600;'>{{ $data['label'] }}</span>
                                            </div>
                                        "
                                        {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $data['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label><b> Date</b></label>
                            <input type="date" name="status_date" id="status_date" class="form-control form-control-sm status_date" required>
                        </div>

                        <div class="form-group d-none" id="preparing-scope-section">
                            <label class="mt-2"><b>Scope Title</b></label>
                            <input type="text" name="scope_title"  id="scope_title" class="form-control form-control-sm">

                            <label class="mt-2"><b>Scope of Work</b></label>
                            <textarea name="scope_content" id="scope_content" class="aiz-text-editor form-control form-control-sm" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label><b>Comment</b></label>
                            <textarea name="comment" id="statusComment" class="form-control form-control-sm"></textarea>
                        </div>

                        <div class="form-group d-none" id="proposal-items-section">
                            <h6><b>Proposal Items</b></h6>
                            <div id="proposal-items-wrapper" class="px-3">
                                {{-- <div class="proposal-item row mb-2">
                                    <div class="col-md-12 mt-1">
                                        <label class="">Title</label>
                                        <input type="text" name="proposal_items[0][title]" class="form-control" placeholder="Title" required>
                                    </div>
                                    <div class="col-md-4 mt-1">
                                        <label class="">Cost</label>
                                        <input type="number" step="0.01" name="proposal_items[0][cost]" class="form-control" placeholder="Cost">
                                    </div>
                                    <div class="col-md-3 mt-1">
                                        <label class="">Internal Days</label>
                                        <input type="number" name="proposal_items[0][internal_days]" class="form-control" placeholder="Internal Days">
                                    </div>
                                    <div class="col-md-3 mt-1">
                                        <label class="">Client Days</label>
                                        <input type="number" name="proposal_items[0][client_days]" class="form-control" placeholder="Client Days">
                                    </div>
                                    
                                    <div class="col-md-2 mt-1">
                                        <button type="button" class="btn btn-danger remove-item mt-4">×</button>
                                    </div> 
                                </div> --}}
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="add-proposal-item">Add More</button>
                        </div>

                        <div class="form-group d-none" id="proposal-items-section-approved">
                            <h6><b>Submitted Proposal Items</b></h6>
                            <div id="proposal-items-approved-wrapper" class="px-3">

                            </div>
                        </div>

                        <div class="form-group d-none" id="approved-cost-field">
                            <label><b>Approved Cost</b></label>
                            <input type="number" step="0.01" name="approved_cost" id="approved_cost" class="form-control form-control-sm">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update Status</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        $(document).on('click', '.change-status-btn', function () {
            const enquiryId = $(this).data('id');
            const currentStatus = $(this).data('status');
            const statusDate = $(this).data('status-date') || new Date().toISOString().split('T')[0];
            const comment = $(this).data('comment') || '';

            $('#status-enquiry-id').val(enquiryId);
            $('#enquiry-status-select').val(currentStatus).trigger('change');
            $('.status_date').val(statusDate);
            $('textarea[name="comment"]').val(comment);
            $('#status-modal-errors').hide().html('');
            $('#original-status').val(currentStatus);

            $('#statusChangeModal').modal('show');
        });

        let proposalItemIndex = 1; // Initial index for proposal items

        // Show/Hide conditional fields
        $('#enquiry-status-select').on('change', function () {
            const selectedStatus = $(this).val();

            $('#approved-cost-field').addClass('d-none');
            $('#preparing-scope-section').addClass('d-none');

            if (selectedStatus === 'proposal_submitted') {
                // Show the proposal section
                $('#proposal-items-section').removeClass('d-none');
                // Clear existing items
                $('#proposal-items-wrapper').html('');
                $('#proposal-items-section-approved').addClass('d-none');
            } else {
                $('#proposal-items-section').addClass('d-none');
                $('#proposal-items-section-approved').addClass('d-none');
            }

            if (selectedStatus === 'preparing_scope') {
                $('#preparing-scope-section').removeClass('d-none');
            }

            const enquiryId = $('#status-enquiry-id').val();
        
            // Load saved items
            $.get('/enquiries/' + enquiryId + '/proposal-items/'+selectedStatus, function (response) {
                const items = response.proposal_items;
                const comment = response.comment;
                const status_date = response.status_date;
                const approvedCost = response.approved_cost;
                const scope_content = response.scope_content;
                const scope_title = response.scope_title;

                $('#statusComment').val(comment);
                $('#status_date').val(status_date);
                $('#approved_cost').val(approvedCost);
                $('#scope_title').val(scope_title);
                $('#scope_content').summernote('code', scope_content);

                if (selectedStatus === 'proposal_submitted') {
                    if (items.length === 0) {
                        addProposalItem(); // add one empty by default
                    } else {
                        items.forEach(function (item, index) {
                            $('#proposal-items-wrapper').append(getProposalItemHtml(index, item));
                            proposalItemIndex = index + 1;
                        });
                    }
                }else{
                    $('#proposal-items-wrapper').empty();
                }

                if(selectedStatus == 'project_approved'){
                    $('#approved-cost-field').removeClass('d-none');
                    $('#proposal-items-approved-wrapper').html('');
                    $('#proposal-items-section-approved').removeClass('d-none');
                    if(items.length != 0){
                        items.forEach(function (item, index) {
                            var selected = checked = '';
                            if(item.selected == 1){
                                selected = 'selected';
                                checked = 'checked';
                            }
                            $('#proposal-items-approved-wrapper').append(`<div class="col-md-12">
                                <label class="card p-2 mb-1 proposal-select-card ${selected}" style="cursor: pointer;">
                                    <input type="radio" name="selected_proposal_item_id" ${checked} data-cost="${item.cost || ''}" value="${item.id || ''}" class="d-none">
                                    <span class="mb-1 fs-14">${item.title || ''}</span>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Cost:</strong> ${item.cost || ''}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Internal Days:</strong> ${item.internal_days || ''}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Client Days:</strong> ${item.client_days || ''}
                                        </div>
                                    </div>
                                </label>
                            </div>`);

                            
                        });
                    }
                }
            });
        });

        $(document).on('change', 'input[name="selected_proposal_item_id"]', function () {
            $('.proposal-select-card').removeClass('selected');
            $(this).closest('.proposal-select-card').addClass('selected');
            let selectedProposalItemId = $('input[name="selected_proposal_item_id"]:checked').attr('data-cost');
            console.log(selectedProposalItemId);
            $('#approved_cost').val(selectedProposalItemId);
        });

        function addProposalItem() {
            let index = $('#proposal-items-wrapper .proposal-item').length;

            $('#proposal-items-wrapper').append(getProposalItemHtml(index));
            
        }

        function getProposalItemHtml(index, item = {}) {
            return `
                <div class="proposal-item row mb-1 p-2" style="border: 1px solid #c7c7c7;">
                    <div class="col-md-12 mt-1">
                        <label class="">Title</label>
                        <input type="text" name="proposal_items[${index}][title]" class="form-control form-control-sm" placeholder="Title" value="${item.title || ''}" required>
                    </div>
                    <div class="col-md-4 mt-1">
                        <label class="">Cost</label>
                        <input type="number" step="0.01" name="proposal_items[${index}][cost]" class="form-control form-control-sm" placeholder="Cost" value="${item.cost || ''}">
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="">Internal Days</label>
                        <input type="number" name="proposal_items[${index}][internal_days]" class="form-control form-control-sm" placeholder="Internal Days" value="${item.internal_days || ''}">
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="">Client Days</label>
                        <input type="number" name="proposal_items[${index}][client_days]" class="form-control form-control-sm" placeholder="Client Days" value="${item.client_days || ''}">
                    </div>
                    <div class="col-md-2 mt-1">
                        <button type="button" class="btn btn-danger remove-item mt-4">×</button>
                    </div>
                </div>
            `;
        }
        // Submit via AJAX
        let pendingSubmit = false;
        $('#status-change-form').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();
            var flag = true;

            const originalStatus = $('#original-status').val();
            var selectedStatus = $('#enquiry-status-select').val();

            if (originalStatus === selectedStatus && !pendingSubmit) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'The selected status is the same as the current status. Do you want to continue?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, continue',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        pendingSubmit = true;
                        $('#status-change-form').submit();
                    }
                });
                return;
            }

            pendingSubmit = false;

            if(selectedStatus == 'project_approved'){
                let proposalItemsExist = $('input[name="selected_proposal_item_id"]').length > 0;

                if (proposalItemsExist) {
                    let selectedId = $('input[name="selected_proposal_item_id"]:checked').val();

                    if (!selectedId) {
                        e.preventDefault();
                        AIZ.plugins.notify('danger', "Please select a proposal item.");
                        flag = false;
                    }
                }
            }
            if (selectedStatus === 'preparing_scope') {
                if (!$('input[name="scope_title"]').val()) {
                    AIZ.plugins.notify('danger', 'Scope title is required');
                    flag = false;
                }
                if (!$('textarea[name="scope_content"]').val()) {
                    AIZ.plugins.notify('danger', 'Scope content is required');
                    flag = false;
                }
            }

            if(flag == true){
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function (res) {
                        $('#statusChangeModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function (key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        $('#status-modal-errors').html(errorHtml).show();
                    }
                });
            }
        });
        
        $('#add-proposal-item').on('click', function () {
            let newItem = `
                <div class="proposal-item row mb-1 p-2" style="border: 1px solid #c7c7c7;">
                    <div class="col-md-12 mt-1">
                        <label class="">Title</label>
                        <input type="text" name="proposal_items[${proposalItemIndex}][title]" class="form-control form-control-sm" placeholder="Title" required>
                    </div>
                    <div class="col-md-4 mt-1">
                        <label class="">Cost</label>
                        <input type="number" step="0.01" name="proposal_items[${proposalItemIndex}][cost]" class="form-control form-control-sm" placeholder="Cost">
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="">Internal Days</label>
                        <input type="number" name="proposal_items[${proposalItemIndex}][internal_days]" class="form-control form-control-sm" placeholder="Internal Days">
                    </div>
                    <div class="col-md-3 mt-1">
                        <label class="">Client days</label>
                        <input type="number" name="proposal_items[${proposalItemIndex}][client_days]" class="form-control form-control-sm" placeholder="Client Days">
                    </div>
                    <div class="col-md-2 mt-1">
                        <button type="button" class="btn btn-danger remove-item mt-4">×</button>
                    </div>
                </div>
            `;

            // Append the new item to the proposal items wrapper
            $('#proposal-items-wrapper').append(newItem);

            // Increment the proposal item index
            proposalItemIndex++;
        });

        // Event delegation for removing items
        $(document).on('click', '.remove-item', function () {
            $(this).closest('.proposal-item').remove();
        });

        // Clear fields when modal is closed without submission
        $('#statusChangeModal').on('hidden.bs.modal', function () {
            // Reset proposal item wrapper (remove all fields)
            $('#proposal-items-wrapper').empty();
            $('#proposal-items-approved-wrapper').empty();
            // Reset proposalItemIndex for the next modal open
            proposalItemIndex = 1;

            // Optionally, clear other fields like status, comment, etc.
            $('#status-enquiry-id').val('');
            $('#enquiry-status-select').val('').trigger('change');
            $('#status-modal-errors').hide().html('');
            $('.status_date').val('');
            $('textarea[name="comment"]').val('');
            $('#submitted-cost-field').addClass('d-none');
            $('#approved-cost-field').addClass('d-none');
        });
    });
</script>
@endsection
