@extends('backend.layouts.app', ['title' => 'Customer Information'])

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
        font-size: 15px;
        color: #333;
        margin-bottom: 16px;
    }
    .section-title {
        font-size: 20px;
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
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3><i class="fas fa-building me-2"></i>Customer Details</h3>
        <div>
            <a href="{{ Session::has('customers_last_url') ? Session::get('customers_last_url') : route('customers.index') }}" class="btn btn-outline-primary me-2 btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <!-- Company Details -->
    <div class="info-box">
        <div class="section-title"><i class="fas fa-building me-1"></i> Company Information</div>
        <div class="row gy-4">
            <div class="col-md-4">
                <div class="info-label">Customer Code</div>
                <div class="info-value">{{ $customer->customer_code }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Company Name</div>
                <div class="info-value">{{ $customer->company_name }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Company Email</div>
                <div class="info-value">{{ $customer->company_email ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Industry</div>
                <div class="info-value">{{ $customer->industry->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Website</div>
                <div class="info-value">
                    @if($customer->website_link)
                        <a href="{{ $customer->website_link }}" target="_blank">{{ $customer->website_link }}</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Registered Country</div>
                <div class="info-value">{{ $customer->country->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Emirate</div>
                <div class="info-value">{{ $customer->uae_emirate->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="info-label">New to Company</div>
                <div class="info-value">
                    @if($customer->ntc)
                        <span class="badge bg-success badge-md">Yes</span>
                    @else
                        <span class="badge bg-warning badge-md">No</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Google Map</div>
                <div class="info-value">
                    @if($customer->google_location)
                        <a href="{{ $customer->google_location }}" target="_blank" class="text-primary">View Location</a>
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-label">Address</div>
                <div class="info-value">{{ $customer->company_address ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    <!-- Contact Persons -->
    <div class="info-box">
        <div class="section-title"><i class="fas fa-users me-1"></i> Contact Persons</div>
        <div class="row g-2">
            @forelse($customer->contacts as $contact)
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

    @if ($customer->assignmentHistory->isNotEmpty())
        <div class="card mt-2">
            <div class="card-header">
                <h6 class="mt-1">Salesperson Assignment History</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered aiz-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Old Salesperson</th>
                            <th>New Salesperson</th>
                            <th>Changed By</th>
                            <th>Source</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->assignmentHistory as $key => $assignment)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $assignment->oldSalesPerson->name ?? '-' }}</td>
                                <td>{{ $assignment->newSalesPerson->name ?? '-' }}</td>
                                <td>{{ $assignment->changedBy->name ?? 'System' }}</td>
                                <td>
                                    @if ($assignment->enquiry_id)
                                        From Enquiry 
                                        <a href="{{ route('enquiries.show', $assignment->enquiry) }}"> 
                                            [{{ $assignment->enquiry->enquiry_code }}]
                                        </a>
                                    @else
                                        From Customer
                                    @endif
                                </td>
                                <td>{{ $assignment->created_at->format('d, M Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
