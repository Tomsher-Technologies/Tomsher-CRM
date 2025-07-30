@extends('backend.layouts.app',['title' => 'Show Enquiry'])
@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Enquiry Details</h4>
            <a href="{{ Session::has('enquiries_last_url') ? Session::get('enquiries_last_url') : route('enquiries.index') }}" class="btn btn-light btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Customer</h6>
                    <p class="mb-2"><strong>{{ $enquiry->customer->company_name.' ['.$enquiry->customer->customer_code.']' }}</strong></p>

                    <h6 class="text-muted">Enquiry Date</h6>
                    <p class="mb-2">{{ $enquiry->enquiry_date ? \Carbon\Carbon::parse($enquiry->enquiry_date)->format('d M Y') : '-' }}</p>

                    <h6 class="text-muted">Enquiry Source</h6>
                    <p class="mb-2">{{ $enquiry->source->name ?? '-' }}</p>

                    <h6 class="text-muted">Enquiry Owner</h6>
                    <p class="mb-2">{{ $enquiry->owner->name ?? '-' }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Added By</h6>
                    <p class="mb-2">{{ $enquiry->addedBy->name ?? '-' }}</p>

                    <h6 class="text-muted">Status</h6>
                    <p class="mb-2">
                        @php
                            $statuses = getEnquiryStatuses();
                        @endphp
                        <span class="badge  badge-inline " style="background: {{$statuses[$enquiry->status]['bg'] ?? '' }}; color:{{$statuses[$enquiry->status]['list_color'] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                        </span>
                    
                    </p>

                    <h6 class="text-muted">Project Categories</h6>
                    @if($enquiry->projectTypes->count())
                        <ul class="list-unstyled">
                            @foreach($enquiry->projectTypes as $type)
                                <li><i class="las la-square mr-1"></i> {{ $type->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">-</p>
                    @endif

                   

                    
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Project Details</h6>
            <p>{!! nl2br(e($enquiry->project_details)) !!}</p>

            @if($enquiry->comments)
                <h6 class="mt-4 mb-3">Internal Comments</h6>
                <p class="text-muted">{!! nl2br(e($enquiry->comments)) !!}</p>
            @endif

            <hr>
            <p class="text-right text-muted">
                <small>Last updated on {{ $enquiry->updated_at->format('d M Y h:i A') }}</small>
            </p>
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
                                <td class="text-center">{{ \Carbon\Carbon::parse($followup->followup_time)->format('d M Y, h:i A') }}</td>
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
