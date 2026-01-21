@extends('backend.layouts.app', ['title' => 'Data Information'])

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
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        border-left: 5px solid #0d6efd;
        padding-left: 12px;
        margin-bottom: 15px;
    }
    .info-box {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }

    .timeline {
        list-style: none;
        padding-left: 20px;
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 25px;
    }

    .timeline-dot {
        position: absolute;
        left: -1px;
        top: 5px;
        width: 14px;
        height: 14px;
        background: #0d6efd;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }

    .timeline-content {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 12px 15px;
    }

    .timeline-box {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        margin-top: 8px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-building me-2"></i>Data Details</h4>
        <div>
            <a href="{{ Session::has('data_last_url') ? Session::get('data_last_url') : route('data.index') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('data.edit', $data->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <!-- Company Details -->
    <div class="info-box">

        <div class="row">
            <div class="col-md-4">
                <div class="info-label">Entry Date</div>
                <div class="info-value">
                    {{ $data->entry_date ? date('d M, Y', strtotime($data->entry_date)) : '' }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @php
                        $statuses = getDataStatuses();
                    @endphp
                    <span class="badge  badge-inline " style="background: {{$statuses[$data->status]['bg'] ?? '' }}; color:{{$statuses[$data->status]['list_color'] ?? '' }}">
                        {{ ucfirst(str_replace('_', ' ', $data->status)) }}
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-label">Source</div>
                <div class="info-value">
                    {{ $data->source?->name }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="info-label">Requirement</div>
                <div class="info-value">
                    {{ $data->requirement ?? '' }}
                </div>
            </div>
        </div>
        <div class="section-title mt-3"><i class="fas fa-building me-1"></i> Company Information</div>
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="info-label">Data Code</div>
                <div class="info-value">{{ $data->data_code }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Company Name</div>
                <div class="info-value">{{ $data->company_name }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Company Email</div>
                <div class="info-value">{{ $data->company_email ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Industry</div>
                <div class="info-value">{{ $data->industry->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Website</div>
                <div class="info-value">
                    @if($data->website_link)
                        <a href="{{ $data->website_link }}" target="_blank">{{ $data->website_link }}</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Registered Country</div>
                <div class="info-value">{{ $data->country->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Emirate</div>
                <div class="info-value">{{ $data->uae_emirate->name ?? 'N/A' }}</div>
            </div>
            
            <div class="col-md-4">
                <div class="info-label">Google Map</div>
                <div class="info-value">
                    @if($data->google_location)
                        <a href="{{ $data->google_location }}" target="_blank" class="text-primary">View Location</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Address</div>
                <div class="info-value">{{ $data->company_address ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    <!-- Contact Persons -->
    <div class="info-box">
        <div class="section-title"><i class="fas fa-users me-1"></i> Contact Persons</div>
        <div class="row g-2">
            @forelse($data->contacts as $contact)
                <div class="col-md-6 mt-2">
                    <div class="bg-white border rounded px-4 py-2 shadow-sm h-100 {{ ($contact->is_primary == 1) ? 'primary-contact' : ''}}">
                        <div class="row gy-2">
                            <div class="col-md-6">
                                <div class="info-label">Name 
                                    @if($contact->is_primary)
                                        <span class="badge bg-success badge-inline badge-sm ml-1 text-white">Primary</span>
                                    @endif
                                </div>
                                <div class="info-value">{{ $contact->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $contact->email ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Landline</div>
                                <div class="info-value">{{ $contact->landline_number ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Mobile</div>
                                <div class="info-value">{{ $contact->mobile_number ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">WhatsApp</div>
                                <div class="info-value">{{ $contact->whatsapp_number ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Designation</div>
                                <div class="info-value">{{ $contact->designation ?? 'N/A' }}</div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">No contact persons added yet.</p>
            @endforelse
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h6 class="mt-1">Status Timeline</h6>
        </div>
        <div class="card-body">
            <ul class="timeline">
                @php
                    $timeline = $data->statusHistories()->orderBy('status_date','asc')->get();
                @endphp

                @foreach($timeline as $history)
                    <li class="timeline-item">
                        <span class="timeline-dot"></span>

                        <div class="timeline-content">
                            <h6 class="mb-1 fw-bold">
                                {{ ucwords(str_replace('_',' ', $history->status)) }}
                            </h6>

                            <div class="text-muted small">
                                {{ \Carbon\Carbon::parse($history->status_date)->format('d M Y') }}
                                · By {{ $history->changedBy->name ?? 'System' }}
                            </div>

                            @if($history->comment)
                                <div class="timeline-box  mt-1">
                                    <strong>Comment</strong>
                                    <p class="mb-0 mt-1">{{ $history->comment }}</p>
                                </div>
                            @endif

                            @if ($history->followup_date)
                                <div class="mt-2 text-info mt-1">
                                    <i class="fa fa-calendar"></i>
                                    <strong>Next Follow-up:</strong>
                                    {{ \Carbon\Carbon::parse($history->followup_date)->format('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
           
        </div>
    </div>

    
</div>
@endsection
