@extends('backend.layouts.app', ['title' => 'All Enquiries'])

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Enquiries</h5>
            @can('add_enquiries')
                <a href="{{ route('enquiries.create') }}" class="btn btn-success">Add Enquiry</a>
            @endcan
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3">

                <div class="col-md-3 mb-1">
                    <input type="text" class="form-control form-control-sm" value="{{ request('enquiry_code') }}" name="enquiry_code" placeholder="Search by enquiry code" >
                </div>

                <div class="col-md-3 mb-1">
                    <select name="customer_id" class="form-control  form-control-sm aiz-selectpicker" data-live-search="true">
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
                    <select name="enquiry_source_id" id="enquiry_source_id" class="form-control form-control-sm aiz-selectpicker"
                        data-live-search="true">
                        <option value="">All Enquiry Sources</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}"
                                {{ request('enquiry_source_id') == $source->id ? 'selected' : '' }}>
                                {{ $source->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-1">
                    <select name="source_mode" id="source_mode" class="form-control form-control-sm aiz-selectpicker"
                        data-live-search="true">
                        <option value="">All Source Modes</option>
                        <option value="inhouse" {{ request('source_mode') == "inhouse" ? 'selected' : '' }}>
                            Inhouse Lead
                        </option>
                        <option value="self" {{ request('source_mode') == "self" ? 'selected' : '' }}>
                            Self Lead
                        </option>
                    </select>
                </div>

                <div class="col-md-3 mb-1">
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

               
                <div class="col-md-3 mb-1">
                    @php
                        $statuses = getEnquiryStatuses();
                       
                    @endphp
                
                    <select name="status" id="status" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All Current Status</option>
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

                @can('view_all_users_enquiries')
                    <div class="col-md-3 mb-1">
                        <select name="added_by" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('added_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endcan
                
                <div class="col-md-3 mb-1">
                    <input type="text" class="aiz-date-range form-control form-control-sm" value="{{ request('enquiry_date') }}"
                        name="enquiry_date" placeholder="Filter by enquiry date" data-format="DD-MM-Y" data-separator=" to "
                        data-advanced-range="true" autocomplete="off">
                </div>

                

                <div class="col-md-3 ">
                    <input type="text" class="aiz-date-range form-control form-control-sm" value="{{ request('last_updated_date') }}"
                        name="last_updated_date" placeholder="Filter by Updated date" data-format="DD-MM-Y" data-separator=" to "
                        data-advanced-range="true" autocomplete="off">
                </div>


                <div class="col-md-3">
                    <select name="milestone_status" id="milestone_status" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All Milestone Status</option>
                        @foreach ($statuses as $key => $data)
                            <option value="{{ $key }}"
                                data-content="
                                    <div style='display: flex; align-items: center;'>
                                        <div style='width: 16px; height: 16px; background: {{ $data['bg'] }}; border-radius: 4px; margin-right: 8px;'></div>
                                        <span style='color: {{ $data['filter_color'] }}; font-weight: 600;'>{{ $data['label'] }}</span>
                                    </div>
                                "
                                {{ request('milestone_status') == $key ? 'selected' : '' }}>
                                {{ $data['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2 ">
                    <input type="hidden" value="{{ request('sort_by') }}" name="sort_by" id="sort_by_form" >
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('enquiries.index') }}" class="btn btn-secondary  btn-sm  ml-1">Reset</a>
                </div>
            </form>
            
            <div class="row" style="text-align: -webkit-right;">
                <div class="col-md-10 d-flex flex-wrap mt-4">
                    <div class="d-flex align-items-center me-4 mb-1 ml-1">
                        <span class="d-inline-block" style="width:40px; height:20px; background-color: #ffdc2812; border:1px solid #ccc;"></span>
                        <span class="ms-2" style="margin-left: 5px;">Pending Followups</span>
                    </div>
                    <div class="d-flex align-items-center me-4 mb-1 ml-1">
                        <span class="d-inline-block" style="width:40px; height:20px; background-color: #d3d3d36e; border:1px solid #ccc;"></span>
                        <span class="ms-2" style="margin-left: 5px;">Project Rejected / Not Interested / Not Responding / Invalid / Spam</span>
                    </div>
                    <div class="d-flex align-items-center me-4 mb-1 ml-1">
                        <span class="d-inline-block" style="width:40px; height:20px; background-color: #90ee903b; border:1px solid #ccc;"></span>
                        <span class="ms-2" style="margin-left: 5px;">Project Approved</span>
                    </div>
                </div>
                <div class="col-md-2 m-auto">
                    <form action="{{ route('enquiries.index') }}" method="GET">
                        @foreach (request()->except(['page']) as $k => $v)
                            @if (is_array($v))
                                @foreach ($v as $vv)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach

                        <select name="sort_by" id="sort_by" class="form-control form-control-sm "
                            data-live-search="true">
                            <option value="">Sort By</option>
                            <option value="updated_asc" {{ request('sort_by') == "updated_asc" ? 'selected' : '' }}>
                                Updated Date ASC
                            </option>
                            <option value="updated_desc" {{ request('sort_by') == "updated_desc" ? 'selected' : '' }}>
                                Updated Date DESC
                            </option>
                            <option value="enquiry_asc" {{ request('sort_by') == "enquiry_asc" ? 'selected' : '' }}>
                                Enquiry Date ASC
                            </option>
                            <option value="enquiry_desc" {{ request('sort_by') == "enquiry_desc" ? 'selected' : '' }}>
                                Enquiry Date DESC
                            </option>
                        </select>
                    </form>
                </div>
            </div>
            <table class="table aiz-table table-bordered mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Enquiry Code</th>
                        <th style="width:20%;">Customer</th>
                        <th class="text-center">Enquiry Source</th>
                        {{-- <th>Project Category</th> --}}
                        <th>Source Mode</th>
                        <th class="text-center">Current Status</th>
                        <th class="text-center">Enquiry Date</th>
                        <th class="text-center">Enquiry Owner</th>
                        <th class="text-center">Updated Date</th>
                        <th  style="width:15%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @can('view_enquiries')
                        @foreach ($enquiries as $key => $enquiry)
                            @php
                                $pendingFollowups = getDueFutureFollowups($enquiry->id);

                                $backGroundColor = '';
                                if($pendingFollowups != 0){
                                    $backGroundColor = '#ffdc2812';
                                }else{

                                    if(in_array($enquiry->status, ['project_rejected','not_interested','not_responding','invalid_spam']) ){
                                        $backGroundColor = '#d3d3d36e';
                                    }elseif ($enquiry->status === 'project_approved') {
                                        $backGroundColor = '#90ee903b';
                                    }
                                }
                            @endphp
                            <tr style="background-color:{{ $backGroundColor }}" data-id="{{ $enquiry->status }}">
                                <td class="text-center">{{ $key + 1 + ($enquiries->currentPage() - 1) * $enquiries->perPage() }}
                                </td>
                                <td>{{ $enquiry->enquiry_code ?? '' }}</td>
                                <td style="position: relative;">
                                    <div>
                                        {{ $enquiry->customer->company_name . ' [' . $enquiry->customer->customer_code . ']' }}
                                        <a href="javascript:void(0)" class="show-popup" data-id="{{ $enquiry->id }}">
                                            <i class="las la-info-circle fs-16 text-primary" style="cursor: pointer;"></i>
                                        </a>
                                    </div>
                                    @if($enquiry->projectTypes->count())
                                        <ul style="font-size: 10px;" class="pl-3 mb-0 mt-1 text-muted">
                                            @foreach($enquiry->projectTypes as $type)
                                                <li > {{ $type->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @php $primary = $enquiry->customer->main_contact; @endphp
                                    <!-- Stylish Popup -->
                                    <div class="popup-card" id="popup-{{ $enquiry->id }}">
                                        <div class="popup-card-header">
                                            <span><i class="las la-id-card"></i> Contact Info</span>
                                            <i class="las la-times close-popup" data-id="{{ $enquiry->id }}"></i>
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
                                <td class="text-center">{{ $enquiry->source->name ?? '' }}</td>
                                {{-- <td>
                                    @if ($enquiry->projectTypes->count())
                                        <ul class="mb-0 pl-3">
                                            @foreach ($enquiry->projectTypes as $type)
                                                <li>{{ $type->name }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em>N/A</em>
                                    @endif
                                </td> --}}
                                <td class="text-center">{{ ($enquiry->source_mode != NULL) ? ucfirst($enquiry->source_mode).' Lead' : '' }}</td>
                                <td class="text-center">
                                    
                                    <span class="badge  badge-inline " style="background: {{$statuses[$enquiry->status]['bg'] ?? '' }}; color:{{$statuses[$enquiry->status]['list_color'] ?? '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                                    </span>
                                
                                </td>
                                <td class="text-center">
                                    {{ date('d, M Y', strtotime($enquiry->enquiry_date)) }}
                                </td>

                                <td class="text-center">{{ $enquiry->owner->name ?? '' }}</td>

                                <td class="text-center">
                                    {{ date('d, M Y', strtotime($enquiry->updated_at)) }}
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('enquiries.show', $enquiry) }}"
                                        class="btn btn-soft-warning btn-sm btn-icon btn-circle m-auto" title="View enquiry details">
                                        <i class="las la-eye" style="margin-top: 3px;"></i>
                                    </a>

                                    @can('edit_enquiries')
                                        <a href="{{ route('enquiries.edit', $enquiry) }}"
                                            class="btn btn-soft-success btn-sm btn-icon btn-circle m-auto" title="Edit enquiry details">
                                            <i class="las la-edit" style="margin-top: 3px;"></i>
                                        </a>
                                    @endcan

                                    <a href="javascript:void(0)" class="btn-change-status-icon btn btn-sm btn-icon btn-circle change-status-btn m-auto" data-id="{{ $enquiry->id }}" data-status="{{ $enquiry->status }}" title="Change Status">
                                        <i class="las la-exchange-alt" style="margin-top: 2px;"></i>
                                    </a>

                                    @can('add_followups')
                                        <!-- Follow-up icon with tooltip -->
                                        <a href="{{ route('followups.create', $enquiry->id) }}" title="Add Follow-up" class="btn btn-soft-secondary btn-sm btn-icon btn-circle m-auto">
                                            <i class="las la-calendar-plus" style="margin-top: 2px;"></i>
                                        </a>
                                    @endcan

                                    @if($enquiry->scopeOfWork)
                                        <!-- Scope of Work button -->
                                        <a href="{{ route('enquiry-scopes.show', $enquiry->scopeOfWork->id) }}" 
                                        class="btn btn-soft-info btn-sm btn-icon btn-circle m-auto" 
                                        title="View Scope of Work">
                                            <i class="las la-file-alt" style="margin-top: 3px;"></i>
                                        </a>
                                    @endif

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
            <div class="aiz-pagination mt-2">
                @can('view_enquiries')
                    {{ $enquiries->appends(request()->input())->links('pagination::bootstrap-5') }}
                @endcan
            </div>

            <div class="d-flex flex-wrap mt-4">
                <div class="d-flex align-items-center me-4 mb-1 ml-1">
                    <span class="d-inline-block" style="width:40px; height:20px; background-color: #ffdc2812; border:1px solid #ccc;"></span>
                    <span class="ms-2" style="margin-left: 5px;">Pending Followups</span>
                </div>
                <div class="d-flex align-items-center me-4 mb-1 ml-1">
                    <span class="d-inline-block" style="width:40px; height:20px; background-color: #d3d3d36e; border:1px solid #ccc;"></span>
                    <span class="ms-2" style="margin-left: 5px;">Project Rejected / Not Interested / Not Responding / Invalid / Spam</span>
                </div>
                <div class="d-flex align-items-center me-4 mb-1 ml-1">
                    <span class="d-inline-block" style="width:40px; height:20px; background-color: #90ee903b; border:1px solid #ccc;"></span>
                    <span class="ms-2" style="margin-left: 5px;">Project Approved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
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

@endsection

@section('style')
    <style>

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
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        document.getElementById('sort_by').addEventListener('change', function () {
            $('#sort_by_form').val(this.value);
            this.form.submit();
        });

        // Trigger modal and prefill status
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

    flatpickr(".status_date", {
        dateFormat: "Y-m-d",
        maxDate: "today",
        defaultDate: "today"
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
</script>
@endsection
