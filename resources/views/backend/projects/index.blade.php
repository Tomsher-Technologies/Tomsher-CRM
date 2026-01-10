@extends('backend.layouts.app', ['title' => 'All Projects'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Projects</h5>
            @can('add_project')
                <a href="{{ route('projects.create') }}" class="btn btn-success">Add Project</a>
            @endcan
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3">
                <div class="col-md-4 mb-1">
                    <select name="customer_id" class="form-control form-control-sm  aiz-selectpicker" data-live-search="true">
                        <option value="">All Customers</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->customer_code }} - {{ $customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

              
                <div class="col-md-3 mb-1">
                    @php
                        $statuses = [
                            'pending' => ['label' => 'Pending', 'bg' => '#4b515352', 'color' => '#000'],
                            'project_assigned' => ['label' => 'Project Assigned', 'bg' => '#abe4fb', 'color' => '#000'],
                            'kickoff_completed' => ['label' => 'Kickoff Completed', 'bg' => '#4db2ff', 'color' => '#000'],
                            'design_stage' => ['label' => 'Design Stage', 'bg' => '#FFEB3B', 'color' => '#000'],
                            'static' => ['label' => 'Static', 'bg' => '#81f98f8a', 'color' => '#000'],
                            'beta' => ['label' => 'Beta', 'bg' => '#05e31fb3', 'color' => '#000'],
                            'on_hold' => ['label' => 'On Hold', 'bg' => '#e85858ab', 'color' => '#000'],
                            'ongoing' => ['label' => 'Ongoing', 'bg' => '#FF9800', 'color' => '#000'],
                            'completed' => ['label' => 'Completed', 'bg' => '#06a118', 'color' => '#000'],
                            'canceled' => ['label' => 'Canceled', 'bg' => '#F44336', 'color' => '#000'],
                        ];
                    @endphp
                
                    <select name="status" id="status" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All Status</option>
                        @foreach ($statuses as $key => $data)
                            <option value="{{ $key }}"
                                data-content="
                                    <div style='display: flex; align-items: center;'>
                                        <div style='width: 16px; height: 16px; background: {{ $data['bg'] }}; border-radius: 4px; margin-right: 8px;'></div>
                                        <span style='color: {{ $data['color'] }}; font-weight: 600;'>{{ $data['label'] }}</span>
                                    </div>
                                "
                                {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $data['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

               
                
                <div class="col-md-4 mb-1">
                    <input type="text" class="form-control form-control-sm" value="{{ request('keyword') }}" name="keyword" placeholder="Search by Project name/Enquiry code" >
                </div>

                <div class="col-md-3 mb-1">
                    <select name="payment_status" class="form-control form-control-sm  aiz-selectpicker" data-live-search="true">
                        <option value="">All Payment Status</option>
                        <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="not_completed" {{ request('payment_status') == 'not_completed' ? 'selected' : '' }}>Not Completed</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 mb-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary w-100  ml-1">Reset</a>
                </div>
            </form>
           
            <table class="table table-bordered aiz-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th style="width:20%;">Project Name</th>
                        <th>Customer</th>
                        <th class="text-center">Enquiry</th>
                        <th class="text-center">Project Status</th>
                        <th class="text-center">Internal Deadline</th>
                        <th class="text-center">Client Deadline</th>
                        <th class="text-center">Payment Status</th>
                        <th  style="width:10%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @can('view_projects')
                        @foreach ($projects as $key => $pro)
                            <tr>
                                <td class="text-center">{{ $key + 1 + ($projects->currentPage() - 1) * $projects->perPage() }}
                                </td>
                                <td>{{ $pro->project_name ?? '' }}</td>
                                <td>{{ $pro->customer->company_name . ' [' . $pro->customer->customer_code . ']' }}</td>
                                <td class="text-center">
                                    @if ($pro->enquiry != NULL)
                                        <a href="{{ route('enquiries.show', $pro->enquiry) }}" target="_blank">{{ $pro->enquiry->enquiry_code ?? '' }}</a>
                                    @else
                                        N/A
                                    @endif
                                    
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge  badge-inline {{$pro->status}}">
                                        {{ ucfirst(str_replace('_', ' ', $pro->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $pro->internal_deadline ?? 'N/A' }}</td>
                                <td class="text-center">{{ $pro->client_deadline ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if ($pro->pending_amount == 0)
                                        <span class="badge  badge-inline badge-success">Completed</span>
                                    @else
                                        <span class="badge  badge-inline badge-danger">Not Completed</span><br>
                                        @can('view_project_amounts')
                                            Pending : <span class="text-danger fw-600">AED {{ $pro->pending_amount }}</span>
                                        @endcan
                                    @endif
                                    
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('projects.show', $pro) }}"
                                        class="btn btn-soft-warning btn-sm btn-icon btn-circle" title="View project details">
                                        <i class="las la-eye"></i>
                                    </a>

                                    @can('edit_project')
                                        <a href="{{ route('projects.edit', $pro) }}"
                                            class="btn btn-soft-success btn-sm btn-icon btn-circle" title="Edit project details">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endcan

                                    {{-- <form action="{{ route('enquiries.destroy', $enquiry) }}" method="POST" style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    @endcan
                </tbody>
            </table>
            <div class="aiz-pagination mt-1">
                @can('view_projects')
                    {{ $projects->appends(request()->input())->links('pagination::bootstrap-5') }}
                @endcan
            </div>
        </div>
    </div>

@endsection

@section('style')
<style>
   
    .btn i {
        font-size: 14px;
    }

   
    </style>
    
@endsection

@section('script')
<script>
   $(document).ready(function () {
        
    });

   
</script>
@endsection
