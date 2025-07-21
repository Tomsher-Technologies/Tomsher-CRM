@extends('backend.layouts.app',['title' => 'Show Project Details'])
@section('content')
<div class="container">
    <div class="bg-white shadow-lg rounded-xl p-4">
        <h4 class="text-center mb-4">📄 Project Details</h4>
        <!-- Project Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <strong>Project Name:</strong> {{ $project->project_name }}
            </div>
            <div class="col-md-6">
                <strong>Customer:</strong> {{ $project->customer->customer_code }} - {{ $project->customer->company_name }}
            </div>
            <div class="col-md-6 mt-3">
                <strong>Enquiry:</strong> {{ $project->enquiry ? $project->enquiry->enquiry_code : 'N/A' }}
            </div>
            <div class="col-md-6 mt-3">
                <strong>Start Date:</strong> {{ $project->start_date ? date('d M, Y', strtotime($project->start_date)) : 'N/A' }}
            </div>
            <div class="col-md-6 mt-3">
                <strong>Internal Deadline:</strong> {{ $project->internal_deadline ? date('d M, Y', strtotime($project->internal_deadline)) : 'N/A' }}
            </div>
            <div class="col-md-6 mt-3">
                <strong>Client Deadline:</strong> {{ $project->client_deadline ? date('d M, Y', strtotime($project->client_deadline)) : 'N/A' }}
            </div>
            <div class="col-md-6 mt-3">
                <strong>Project Total Cost: AED {{ number_format($project->project_total_cost, 2) }}</strong>
            </div>
            <div class="col-md-6 mt-3">
                <strong>Paid Amount:</strong><span class="text-success fw-600"> AED {{ number_format($project->paid_amount, 2) }}</span>
            </div>
            <div class="col-md-6 mt-3">
                <strong>Pending Amount:</strong><span class="text-danger fw-600"> AED {{ number_format($project->pending_amount, 2) }}</span>
            </div>
            <div class="col-md-6 mt-3">
                <strong>Status:</strong> 
                <span class="badge  badge-inline {{$project->status}}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
            <div class="col-md-6 mt-3">
                <strong>Technologies:</strong> 
                @foreach($project->technologies as $technology)
                    <span class="badge bg-soft-danger badge-inline">{{ $technology->name }}</span>
                @endforeach
            </div>
            <div class="col-md-6 mt-3">
                <strong>Comment:</strong><br>
                {!! nl2br(e($project->comment)) !!}
            </div>
        </div>

        <hr>

        <!-- Stylish Status History Timeline -->
        <h5 class="fw-bold mb-4">🕑 Status Change History</h5>

        <div class="status-history-timeline position-relative">
            @foreach($project->statusHistories as $history)
                <div class="history-item d-flex position-relative mb-4">
                    <div class="icon flex-shrink-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="content ms-3">
                        <div class="fw-bold">
                            {{ ucfirst(str_replace('_', ' ', $history->old_status ?? 'N/A')) }}
                            ➔ 
                            {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}
                        </div>
                        <div class="small text-muted">
                            Changed on {{ \Carbon\Carbon::parse($history->changed_at)->format('d, M Y') }} 
                            by {{ $history->user->name ?? 'System' }}
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="timeline-line position-absolute top-0 start-0" style="left: 15px; height: 100%; width: 2px; background-color: #dee2e6;"></div>
        </div>


        <hr>

        <!-- Payment Details -->
        <h5 class="mb-3">💳 Payment Details</h5>
        @if($project->payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Payment Title</th>
                            <th>Amount</th>
                            <th>Percentage</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Expected Date</th>
                            <th>Received Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_title }}</td>
                                <td>AED {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->percentage }}%</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                                <td>
                                    @if($payment->status == 'received')
                                        <span class="badge bg-success badge-inline" style="color:#fff">Received</span>
                                    @else
                                        <span class="badge bg-warning text-dark badge-inline" style="color:#fff">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $payment->expected_date ? date('d, M Y', strtotime($payment->expected_date)) : 'N/A' }}</td>
                                <td>{{ $payment->received_date ? date('d, M Y', strtotime($payment->received_date)) : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">No Payment Details Found.</div>
        @endif

        <div class="text-end mt-4">
            <a href="{{ Session::has('projects_last_url') ? Session::get('projects_last_url') : route('enquiries.index') }}" class="btn btn-primary">Back to Projects</a>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
    .status-history-timeline {
        position: relative;
        padding-left: 40px;
    }
  
    .timeline-line {
        z-index: 0;
    }
    .history-item {
        z-index: 1;
    }
    </style>
@endsection
