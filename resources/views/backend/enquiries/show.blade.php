@extends('backend.layouts.app',['title' => 'Show Enquiry'])
@section('content')
<style>
    .primary-contact{
        border: 2px solid #0abb75 !important;
    }
    .info-label {
        font-weight: 600;
        color: #000;
        font-size: 14px;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 14px;
        color: #333;
        margin-bottom: 16px;
    }
    .section-title {
        font-size: 14px;
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
</style>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Enquiry Details</h5>
            <a href="{{ Session::has('enquiries_last_url') ? Session::get('enquiries_last_url') : route('enquiries.index') }}" class="btn btn-light btn-sm">Back to List</a>
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
                </div>

                <div class="col-md-6">
                    <div class="text-muted">Added By</div>
                    <div class="mb-1">{{ $enquiry->addedBy->name ?? '-' }}</div>

                    <div class="text-muted">Status</div>
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
                        <h6><strong >{{ ucwords(str_replace('_',' ', $history->status)) }}</strong></h6>
                        <small class="text-muted">on {{ \Carbon\Carbon::parse($history->status_date)->format('d M Y') }}</small>
                        <small class="text-muted">&nbsp; By: {{ $history->changedBy->name ?? 'System' }}</small>

                        @if($history->status === 'proposal_submitted')
                            @php
                                $proposalItems = $history->proposalItems()->get();
                            @endphp
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
                                    @foreach ($proposalItems as $it => $items)
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
                                    @endforeach
                                </tbody>
                            </table>
                           
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
                            <th style="width:20%">Subject</th>
                            <th>Location</th>
                            <th class="text-center">Followup Status</th>
                            <th class="text-center">Enquiry Status</th>
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
                                <td>{{ $followup->subject }}</td>
                                <td>{{ $followup->location ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($followup->status == 'pending')
                                        @php
                                            $followupTime = \Carbon\Carbon::parse($followup->followup_time);
                                        @endphp
                                        {{-- $followupTime->isToday() ||  --}}
                                        @if($followupTime->isFuture())
                                            <span class="badge  badge-inline pending-upcoming">{{ ucfirst($followup->status) }}</span>
                                        @else
                                            <span class="badge badge-inline pending-due">{{ ucfirst($followup->status) }}</span>
                                        @endif
                                    @else
                                        <span class="badge  badge-inline completed">
                                            {{ ucfirst($followup->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($followup->enquiry_status != NULL)
                                        <span class="text-success">({{ ucfirst(str_replace('_', ' ', $followup->enquiry_status)) }})</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

@section('style')

<style>

</style>
@endsection
