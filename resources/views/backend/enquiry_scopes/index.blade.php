@extends('backend.layouts.app', ['title' => 'All Enquiry Scope Of Works'])

@section('content')

<div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Enquiry Scopes</h5>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3">

                <div class="col-md-4 mb-1">
                    <input type="text" name="keyword" id="keyword" value="{{ request()->keyword }}" class="form-control form-control-sm" placeholder="Search with Enquiry code, title, customer.">
                </div>

                <div class="col-md-3 mb-1">
                    <select name="added_by" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All Enquiry Owners</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('added_by') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-1">
                    <select name="project_type_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All Project Categories</option>
                        @foreach ($projectTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ request('project_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-control form-control-sm aiz-selectpicker">
                        <option value="">Select Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="responded" {{ request('status') === 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
               
                <div class="col-md-2 d-flex gap-2 mb-1">
                    <button type="submit" class="btn btn-primary w-100 btn-sm">Filter</button>
                    <a href="{{ route('enquiry-scopes.index') }}" class="btn btn-secondary w-100  ml-1 btn-sm">Reset</a>
                </div>
            </form>
            
            <table class="table aiz-table table-bordered mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Enquiry Code</th>
                        <th>Customer</th>
                        <th>Scope Title</th>
                        
                        <th class="text-center">Submitted Date</th>
                        <th class="text-center">Enquiry Owner</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @can('view_enquiry_scope_work')
                        @forelse($scopes as $key => $scope)
                            <tr>
                                <td class="text-center">{{ $key + $scopes->firstItem() }}</td>

                                <td class="text-center">
                                    {{ $scope->enquiry->enquiry_code ?? '-' }}
                                </td>

                                <td>
                                    {{ $scope->enquiry->customer->company_name ?? '-' }}
                                    @if($scope->enquiry->projectTypes->count())
                                        <ul style="font-size: 12px;" class="pl-3 mb-0 mt-1 text-muted">
                                            @foreach($scope->enquiry->projectTypes as $type)
                                                <li > {{ $type->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>

                                 <td>
                                    {{ $scope->title ?? '' }}
                                </td>

                                <td class="text-center">
                                    {{ date('d, M Y', strtotime($scope->created_at)) }}
                                </td>

                                <td class="text-center">
                                    {{ $scope->enquiry->owner->name ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-{{ $scope->status == 'open' ? 'warning' : ($scope->status == 'responded' ? 'info' : 'success') }}" style="min-width: 70px; text-align:center;">
                                        {{ ucfirst($scope->status) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('enquiry-scopes.show', $scope->id) }}" class="btn btn-soft-warning btn-sm btn-icon btn-circle m-auto" title="View enquiry scope details">
                                        <i class="las la-eye" style="margin-top: 3px;"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            
                        @endforelse
                    @endcan
                </tbody>
            </table>
            <div class="aiz-pagination mt-2">
                @can('view_enquiry_scope_work')
                    {{ $scopes->appends(request()->input())->links('pagination::bootstrap-5') }}
                @endcan
            </div>
        </div>
    </div>
@endsection