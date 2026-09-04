@extends('backend.layouts.app', ['title' => 'Show Enquiry'])
@section('content')
    <style>
        .primary-contact {
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
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
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="container">
        <div class="d-flex align-items-center mb-4">
            <div class="row">
                <div class="col">
                    @can('edit_enquiries')
                        <a href="{{ route('enquiries.edit', $enquiry) }}" class="btn btn-success btn-sm"><i
                                class="las la-edit fs-16" style="margin-top: 3px;"></i> Edit Enquiry</a>
                    @endcan

                    @can('add_followups')
                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addFollowupModal"><i
                                class="las la-calendar-plus fs-16" style="margin-top: 2px;"></i> Add Followup</a>
                    @endcan

                    @can('edit_enquiries')
                        <a href="javascript:void(0)" class="btn btn-dark btn-sm change-status-btn" data-id="{{ $enquiry->id }}"
                            data-status="{{ $enquiry->status }}">Change Status</a>
                    @endcan

                    <a href="{{ Session::has('enquiries_last_url') ? Session::get('enquiries_last_url') : route('enquiries.index') }}"
                        class="btn btn-primary btn-sm"><i class="las la-arrow-left fs-16" style="margin-top: 2px;"></i> Back
                        to List</a>
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
                        <div class="mb-1">
                            <strong>{{ $enquiry->customer->company_name . ' [' . $enquiry->customer->customer_code . ']' }}</strong>
                        </div>

                        <div class="text-muted">Enquiry Date</div>
                        <div class="mb-1">
                            {{ $enquiry->enquiry_date ? \Carbon\Carbon::parse($enquiry->enquiry_date)->format('d M Y') : '-' }}
                        </div>

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
                            <span class="badge  badge-inline "
                                style="background: {{ $statuses[$enquiry->status]['bg'] ?? '' }}; color:{{ $statuses[$enquiry->status]['list_color'] ?? '' }}">
                                {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                            </span>

                        </div>

                        <div class="text-muted">Source Mode</div>
                        <div class="mb-1">
                            @if ($enquiry->source_mode != null)
                                {{ $enquiry->source_mode == 'cross_up_sell' ? 'Cross/Up Sell' : ucfirst($enquiry->source_mode) . ' Lead' }}
                            @endif
                        </div>
                        <div class="text-muted">Project Categories</div>
                        @if ($enquiry->projectTypes->count())
                            <ul class="list-unstyled">
                                @foreach ($enquiry->projectTypes as $type)
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

                @if ($enquiry->comments)
                    <hr>
                    <h6 class="mb-3">Internal Comments</h6>
                    <p>{!! nl2br(e($enquiry->comments)) !!}</p>
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
                                @if ($enquiry->customer->website_link)
                                    <a href="{{ $enquiry->customer->website_link }}"
                                        target="_blank">{{ $enquiry->customer->website_link }}</a>
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
                                @if ($enquiry->customer->ntc)
                                    <span class="badge bg-success badge-md">Yes</span>
                                @else
                                    <span class="badge bg-warning badge-md">No</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Google Map</div>
                            <div class="mb-1">
                                @if ($enquiry->customer->google_location)
                                    <a href="{{ $enquiry->customer->google_location }}" target="_blank"
                                        class="text-primary">View Location</a>
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
                                <div
                                    class="bg-white border rounded px-4 py-2 shadow-sm h-100 {{ $contact->is_primary == 1 ? 'primary-contact' : '' }}">
                                    <div class="row gy-2">
                                        <div class="col-md-6">
                                            <div class="text-muted">Name
                                                @if ($contact->is_primary)
                                                    <span
                                                        class="badge bg-success badge-inline badge-sm ml-1 text-white">Primary</span>
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
                <h5 class="mt-1">Milestone Timeline</h5>
            </div>
            <div class="card-body">
                <ul class="timeline">
                    @php
                        $timeline = $enquiry->statusHistories()->orderBy('status_date', 'asc')->orderBy('id', 'asc')->get()->values();
                        $followupsByStatus = $enquiry->followups->groupBy('enquiry_status');
                    @endphp
                    @foreach ($timeline as $index => $history)
                        <li class="mt-3 mb-2">
                            <h6 class="fs-17">
                                <strong class="text-info ">{{ ucwords(str_replace('_', ' ', $history->status)) }}</strong>
                                @if ($history->status === 'preparing_scope')
                                    <a href="{{ route('enquiry-scopes.show', $enquiry->scopeOfWork->id) }}"
                                        class="btn btn-info btn-sm btn-icon btn-circle ml-1" title="View Scope of Work">
                                        <i class="las la-file-alt" style="margin-top: 2px;"></i>
                                    </a>
                                @endif
                            </h6>



                            <small class="text-muted">on
                                {{ \Carbon\Carbon::parse($history->status_date)->format('d M Y') }}</small>
                            <small class="text-muted">&nbsp; By: {{ $history->changedBy->name ?? 'System' }}</small>

                            @can('view_enquiries_project_cost')
                                @if ($history->status === 'proposal_submitted')
                                    @php
                                        $proposalItems = $history->proposalItems()->get();
                                    @endphp
                                    @if (!empty($proposalItems) && $proposalItems->count() > 0)
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
                                                        $selected =
                                                            $items->selected == 1 ? 'background: lightgreen;' : '';
                                                    @endphp
                                                    <tr style="{{ $selected }}">
                                                        <td class="text-center">{{ $it + 1 }}</td>
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


                                @if (!empty($history->approved_cost) && $history->approved_cost != 0.0)
                                    <br><span class="text-success mt-1">Approved Cost: AED
                                        {{ $history->approved_cost }}</span>
                                @endif
                            @endcan
                            @if ($history->comment)
                                <p class="mt-1">{{ $history->comment }}</p>
                            @endif

                            @php
                                $historyCreatedAt = $history->created_at;
                                $nextHistoryOfSameStatus = $timeline->slice($index + 1)->first(function($item) use ($history) {
                                    return $item->status === $history->status;
                                });
                                $nextCreatedAt = $nextHistoryOfSameStatus ? $nextHistoryOfSameStatus->created_at : null;

                                $milestoneFollowups = $followupsByStatus->get($history->status, collect())->filter(function($followup) use ($historyCreatedAt, $nextCreatedAt) {
                                    $followupCreatedAt = $followup->created_at;
                                    
                                    // Check if the followup was added on or after the milestone status creation time
                                    if ($followupCreatedAt->lt($historyCreatedAt)) {
                                        return false;
                                    }
                                    
                                    // If there is a next milestone status of the same type, check if the followup was added before that next milestone status creation time
                                    if ($nextCreatedAt && $followupCreatedAt->gte($nextCreatedAt)) {
                                        return false;
                                    }
                                    
                                    return true;
                                });
                            @endphp
                            @if ($milestoneFollowups->isNotEmpty())
                                @include('backend.enquiries.partials.followup-table', [
                                    'followups' => $milestoneFollowups,
                                ])
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
                            @foreach ($enquiry->transferHistories as $history)
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

        <div class="modal fade" id="addFollowupModal" tabindex="-1" aria-labelledby="addFollowupModalLabel" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('followups.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">

                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalTitle"><i class="las la-calendar-plus mr-1 text-primary"></i> Add Follow-up</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-left">
                            <div class="row">
                                <!-- Follow-up Type -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="font-weight-700">Follow-up Type <span class="text-danger">*</span></label>
                                    <select name="followup_type" id="add_followup_type" class="form-control form-control-sm" onchange="handleAddTypeChange()" required>
                                        <option value="">Select</option>
                                        <option value="call">Call</option>
                                        <option value="email">Email</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="meeting">Meeting</option>
                                    </select>
                                </div>

                                <!-- Sub-Type -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="font-weight-700">Sub-Type <span class="text-danger">*</span></label>
                                    <select name="sub_type" id="add_sub_type" class="form-control form-control-sm" required>
                                    </select>
                                </div>

                                <!-- Time -->
                                <div class="form-group col-12 mb-3" id="add-followup-time-group">
                                    <label class="font-weight-700">Time <span class="text-danger">*</span></label>
                                    <input type="text" name="followup_time" id="add_followup_time" class="form-control form-control-sm">
                                </div>

                                <!-- Meeting From -->
                                <div class="form-group col-md-6 mb-3" id="add-meeting-from-group" style="display: none;">
                                    <label class="font-weight-700">Meeting From <span class="text-danger">*</span></label>
                                    <input type="text" name="followup_from" id="add_followup_from" class="form-control form-control-sm">
                                </div>

                                <!-- Meeting To -->
                                <div class="form-group col-md-6 mb-3" id="add-meeting-to-group" style="display: none;">
                                    <label class="font-weight-700">Meeting To <span class="text-danger">*</span></label>
                                    <input type="text" name="followup_to" id="add_followup_to" class="form-control form-control-sm">
                                </div>

                                <!-- Pre-Follow-up Comment -->
                                <div class="form-group col-12 mb-3">
                                    <label class="font-weight-700">Pre-Follow-up Comment <span class="text-danger">*</span></label>
                                    <textarea name="comment" class="form-control form-control-sm" rows="3" required></textarea>
                                </div>

                                <!-- Location -->
                                <div class="form-group col-12 mb-3" id="add-location-group" style="display: none;">
                                    <label class="font-weight-700">Location <span class="text-danger">*</span></label>
                                    <input type="text" name="location" id="add_location" class="form-control form-control-sm">
                                </div>

                                <!-- Meeting Participants -->
                                <div class="form-group col-12 mb-3" id="add-participants-group" style="display: none;">
                                    <label class="font-weight-700" for="add_participants">Meeting Participants (excluding yourself)</label>
                                    @php
                                        $users = \App\Models\User::where('banned', 0)->where('id', '!=', auth()->id())->orderBy('name', 'asc')->get();
                                    @endphp
                                    <select name="participants[]" id="add_participants" class="form-control form-control-sm aiz-selectpicker" multiple data-live-search="true">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="form-group col-12 mb-3">
                                    <label class="font-weight-700">Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="canceled">Canceled</option>
                                        <option value="rescheduled">Rescheduled</option>
                                    </select>
                                </div>

                                <!-- Post-Follow-up Comment -->
                                <div class="form-group col-12 mb-3">
                                    <label class="font-weight-700">Post-Follow-up Comment</label>
                                    <textarea name="post_comment" class="form-control form-control-sm" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">💾 Save Follow-up</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form id="followup-edit-form" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle"><i class="las la-edit mr-1 text-primary"></i> Edit Follow-up</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- Follow-up Type -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="font-weight-700">Follow-up Type <span class="text-danger">*</span></label>
                                    <select name="followup_type" id="edit_followup_type" class="form-control form-control-sm" onchange="handleEditTypeChange()" required>
                                        <option value="call">Call</option>
                                        <option value="email">Email</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="meeting">Meeting</option>
                                    </select>
                                </div>

                                <!-- Sub-Type -->
                                <div class="form-group col-md-6 mb-3">
                                    <label class="font-weight-700">Sub-Type <span class="text-danger">*</span></label>
                                    <select name="sub_type" id="edit_sub_type" class="form-control form-control-sm" required>
                                    </select>
                                </div>

                                <!-- Time -->
                                <div class="form-group col-12 mb-3" id="edit-followup-time-group">
                                    <label class="font-weight-700">Time <span class="text-danger">*</span></label>
                                    <input type="text" name="followup_time" id="edit_followup_time" class="form-control form-control-sm">
                                </div>

                                <!-- Meeting From -->
                                <div class="form-group col-md-6 mb-3" id="edit-meeting-from-group" style="display: none;">
                                    <label class="font-weight-700">Meeting From <span class="text-danger">*</span></label>
                                    <input type="text" name="followup_from" id="edit_followup_from" class="form-control form-control-sm">
                                </div>

                                <!-- Meeting To -->
                                <div class="form-group col-md-6 mb-3" id="edit-meeting-to-group" style="display: none;">
                                    <label class="font-weight-700">Meeting To <span class="text-danger">*</span></label>
                                    <input type="text" name="followup_to" id="edit_followup_to" class="form-control form-control-sm">
                                </div>

                                <!-- Pre-Follow-up Comment -->
                                <div class="form-group col-12 mb-3">
                                    <label class="font-weight-700">Pre-Follow-up Comment <span class="text-danger">*</span></label>
                                    <textarea name="comment" id="edit_comment" class="form-control form-control-sm" rows="3" required></textarea>
                                </div>

                                <!-- Location -->
                                <div class="form-group col-12 mb-3" id="edit-location-group" style="display: none;">
                                    <label class="font-weight-700">Location <span class="text-danger">*</span></label>
                                    <input type="text" name="location" id="edit_location" class="form-control form-control-sm">
                                </div>

                                <!-- Meeting Participants -->
                                <div class="form-group col-12 mb-3" id="edit-participants-group" style="display: none;">
                                    <label class="font-weight-700" for="edit_participants">Meeting Participants (excluding yourself)</label>
                                    @php
                                        $users = \App\Models\User::where('banned', 0)->where('id', '!=', auth()->id())->orderBy('name', 'asc')->get();
                                    @endphp
                                    <select name="participants[]" id="edit_participants" class="form-control form-control-sm aiz-selectpicker" multiple data-live-search="true">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="form-group col-12 mb-3">
                                    <label class="font-weight-700">Status</label>
                                    <select name="status" id="edit_status" class="form-control form-control-sm">
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="canceled">Canceled</option>
                                        <option value="rescheduled">Rescheduled</option>
                                    </select>
                                </div>

                                <!-- Post-Follow-up Comment -->
                                <div class="form-group col-12 mb-3">
                                    <label class="font-weight-700">Post-Follow-up Comment</label>
                                    <textarea name="post_comment" id="edit_post_comment" class="form-control form-control-sm" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success"><i class="las la-check mr-1"></i> Update</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
            aria-hidden="true">
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
                                <label><b>Status</b></label>
                                <select name="status" id="enquiry-status-select"
                                    class="form-control form-control-sm aiz-selectpicker" data-live-search="true"
                                    required>
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
                                <input type="date" name="status_date" id="status_date"
                                    class="form-control form-control-sm status_date" required>
                            </div>

                            <div class="form-group d-none" id="preparing-scope-section">
                                <label class="mt-2"><b>Scope Title</b></label>
                                <input type="text" name="scope_title" id="scope_title"
                                    class="form-control form-control-sm">

                                <label class="mt-2"><b>Scope of Work</b></label>
                                <textarea name="scope_content" id="scope_content" class="aiz-text-editor form-control form-control-sm"
                                    rows="4"></textarea>
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
                                <button type="button" class="btn btn-sm btn-primary" id="add-proposal-item">Add
                                    More</button>
                            </div>

                            <div class="form-group d-none" id="proposal-items-section-approved">
                                <h6><b>Submitted Proposal Items</b></h6>
                                <div id="proposal-items-approved-wrapper" class="px-3">

                                </div>
                            </div>

                            <div class="form-group d-none" id="approved-cost-field">
                                <label><b>Approved Cost</b></label>
                                <input type="number" step="0.01" name="approved_cost" id="approved_cost"
                                    class="form-control form-control-sm">
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
        function handleEditTypeChange() {
            const type = $('#edit_followup_type').val();
            const subType = $('#edit_sub_type');
            const locationGroup = $('#edit-location-group');
            const participantsGroup = $('#edit-participants-group');
            const oldSubType = $('#edit_sub_type').data('val') || '';

            subType.empty();
            participantsGroup.hide();
            $('#edit-followup-time-group').show();
            $('#edit-meeting-from-group, #edit-meeting-to-group').hide();

            if (type === 'call' || type === 'email' || type === 'whatsapp') {
                subType.append(`
                    <option value="incoming" ${oldSubType === 'incoming' ? 'selected' : ''}>Incoming</option>
                    <option value="outgoing" ${oldSubType === 'outgoing' ? 'selected' : ''}>Outgoing</option>
                `);
                locationGroup.hide();
            } else if (type === 'meeting') {
                subType.append(`
                    <option value="online" ${oldSubType === 'online' ? 'selected' : ''}>Online</option>
                    <option value="in-person" ${oldSubType === 'in-person' ? 'selected' : ''}>In-Person</option>
                `);
                locationGroup.show();
                participantsGroup.show();
                $('#edit-followup-time-group').hide();
                $('#edit-meeting-from-group, #edit-meeting-to-group').show();
            } else {
                locationGroup.hide();
            }
        }

        function handleAddTypeChange() {
            const type = $('#add_followup_type').val();
            const subType = $('#add_sub_type');
            const locationGroup = $('#add-location-group');
            const participantsGroup = $('#add-participants-group');

            subType.empty();
            participantsGroup.hide();
            $('#add-followup-time-group').show();
            $('#add-meeting-from-group, #add-meeting-to-group').hide();

            if (type === 'call' || type === 'email' || type === 'whatsapp') {
                subType.append(`
                    <option value="incoming">Incoming</option>
                    <option value="outgoing">Outgoing</option>
                `);
                locationGroup.hide();
            } else if (type === 'meeting') {
                subType.append(`
                    <option value="online">Online</option>
                    <option value="in-person">In-Person</option>
                `);
                locationGroup.show();
                participantsGroup.show();
                $('#add-followup-time-group').hide();
                $('#add-meeting-from-group, #add-meeting-to-group').show();
            } else {
                locationGroup.hide();
            }
        }

        $(document).ready(function() {
            // Initialize flatpickr on edit modal inputs
            flatpickr("#edit_followup_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: false
            });
            flatpickr("#edit_followup_from", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: false
            });
            flatpickr("#edit_followup_to", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: false
            });

            $('#followup-edit-form').on('submit', function(e) {
                const status = $('#edit_status').val();
                if (status === 'rescheduled') {
                    e.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');
                    const data = form.serialize();

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(res) {
                            // Close the edit modal
                            $('#followupModal').modal('hide');
                            // Open the add modal
                            $('#addFollowupModal').modal('show');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                            let errorHtml = 'An error occurred while updating the follow-up.';
                            if (errors) {
                                errorHtml = '<ul>';
                                $.each(errors, function(key, value) {
                                    errorHtml += '<li>' + value[0] + '</li>';
                                });
                                errorHtml += '</ul>';
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorHtml
                            });
                        }
                    });
                }
            });

            $(document).on('click', '.view-followup', function() {
                const id = $(this).data('followup-id');
                const type = ($(this).data('type') || '').toLowerCase();
                const subType = ($(this).data('subtype') || '').toLowerCase();
                
                const rawTime = $(this).data('raw-time') || '';
                const rawFrom = $(this).data('raw-from') || '';
                const rawTo = $(this).data('raw-to') || '';
                
                const comment = $(this).data('subject') || '';
                const location = $(this).data('location') || '';
                const status = $(this).data('followup-status') || '';
                const postComment = $(this).data('post-comment') || '';
                const participantIds = ($(this).data('participant-ids') || '').toString().split(',').filter(Boolean);

                // Set form action
                $('#followup-edit-form').attr('action', '/followups/' + id);

                // Set fields
                $('#edit_followup_type').val(type);
                
                // Store subType value temporarily to restore it after type change triggers
                $('#edit_sub_type').data('val', subType);
                handleEditTypeChange();

                // Set time inputs
                $('#edit_followup_time').val(rawTime);
                $('#edit_followup_from').val(rawFrom);
                $('#edit_followup_to').val(rawTo);

                $('#edit_comment').val(comment);
                $('#edit_location').val(location);
                $('#edit_status').val(status);
                $('#edit_post_comment').val(postComment);

                // Selectpicker for participants
                $('#edit_participants').val(participantIds);
                if (window.selectpicker || $.fn.selectpicker) {
                    $('#edit_participants').selectpicker('refresh');
                }
            });

            $(document).on('click', '.change-status-btn', function() {
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
            $('#enquiry-status-select').on('change', function() {
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
                $.get('/enquiries/' + enquiryId + '/proposal-items/' + selectedStatus, function(response) {
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
                            items.forEach(function(item, index) {
                                $('#proposal-items-wrapper').append(getProposalItemHtml(
                                    index, item));
                                proposalItemIndex = index + 1;
                            });
                        }
                    } else {
                        $('#proposal-items-wrapper').empty();
                    }

                    if (selectedStatus == 'project_approved') {
                        $('#approved-cost-field').removeClass('d-none');
                        $('#proposal-items-approved-wrapper').html('');
                        $('#proposal-items-section-approved').removeClass('d-none');
                        if (items.length != 0) {
                            items.forEach(function(item, index) {
                                var selected = checked = '';
                                if (item.selected == 1) {
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

            $(document).on('change', 'input[name="selected_proposal_item_id"]', function() {
                $('.proposal-select-card').removeClass('selected');
                $(this).closest('.proposal-select-card').addClass('selected');
                let selectedProposalItemId = $('input[name="selected_proposal_item_id"]:checked').attr(
                    'data-cost');
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
            $('#status-change-form').on('submit', function(e) {
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

                if (selectedStatus == 'project_approved') {
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

                if (flag == true) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        success: function(res) {
                            $('#statusChangeModal').modal('hide');
                            location.reload();
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul>';
                            $('#status-modal-errors').html(errorHtml).show();
                        }
                    });
                }
            });

            $('#add-proposal-item').on('click', function() {
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
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.proposal-item').remove();
            });

            // Clear fields when modal is closed without submission
            $('#statusChangeModal').on('hidden.bs.modal', function() {
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

            // Initialize flatpickr on add modal inputs
            flatpickr("#add_followup_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: false
            });
            flatpickr("#add_followup_from", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: false
            });
            flatpickr("#add_followup_to", {
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: false
            });

            // Reset add modal on close
            $('#addFollowupModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('#add_sub_type').empty();
                $('#add-location-group').hide();
                $('#add-participants-group').hide();
                $('#add-followup-time-group').show();
                $('#add-meeting-from-group, #add-meeting-to-group').hide();
                if (window.selectpicker || $.fn.selectpicker) {
                    $('#add_participants').val([]).selectpicker('refresh');
                }
            });

            @if (session('open_add_followup'))
                $('#addFollowupModal').modal('show');
            @endif

        });
    </script>
@endsection
