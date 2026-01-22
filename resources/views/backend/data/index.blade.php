@extends('backend.layouts.app', ['title' => 'All Data'])

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Data</h5>
            <div>
                @can('add_data')
                    <a href="{{ route('data.create') }}" class="btn btn-success">Add Data</a>
                @endcan

                @can('import_data')
                    <a href="{{ route('data-import.index') }}" class="btn btn-warning">Import Data</a>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-sm-3  ">
                    <input type="text" name="keyword" class="form-control form-control-sm"
                        placeholder="Search by Code, Name, Email" value="{{ request('keyword') }}">
                </div>

                <div class="col-sm-3">
                    <select name="source" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">-- Select Source --</option>
                        @foreach ($sources as $sour)
                            <option value="{{ $sour->id }}" {{ request('source') == $sour->id ? 'selected' : '' }}>
                                {{ $sour->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3">
                    @php
                        $statuses = getDataStatuses();

                    @endphp

                    <select name="status" id="status" class="form-control form-control-sm aiz-selectpicker"
                        data-live-search="true">
                        <option value="">All Status</option>
                        @foreach ($statuses as $key => $st)
                            <option value="{{ $key }}"
                                data-content="
                                <div style='display: flex; align-items: center;'>
                                    <div style='width: 16px; height: 16px; background: {{ $st['bg'] }}; border-radius: 4px; margin-right: 8px;'></div>
                                    <span style='font-weight: 600;'>{{ $st['label'] }}</span>
                                </div>
                            "
                                {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $st['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="col-sm-3">
                    <select name="user_id" id="user_id" class="form-control form-control-sm aiz-selectpicker"
                        data-live-search="true">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3 mt-1">
                    <input type="text" class="aiz-date-range form-control form-control-sm"
                        value="{{ request('entry_date') }}" name="entry_date" placeholder="Filter by entry date"
                        data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>

                <div class="col-sm-3 mt-1">
                    <input type="text" class="aiz-date-range form-control form-control-sm"
                        value="{{ request('last_date') }}" name="last_date" placeholder="Filter by last updated date"
                        data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>

                <div class="col-sm-3 mt-1">
                    <input type="text" class="aiz-date-range form-control form-control-sm"
                        value="{{ request('follow_date') }}" name="follow_date" placeholder="Filter by next follow-up date"
                        data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>

                <div class="col-sm-2 d-flex mt-1 ">
                    <input type="hidden" value="{{ request('sort_by') }}" name="sort_by" id="sort_by_form">
                    <button type="submit" class="btn btn-xs btn-primary ">Filter</button>
                    <button class="btn btn-xs ml-1 btn-secondary"><a href="{{ route('data.index') }}"
                            class="text-white">Reset</a></button>
                </div>
            </form>

            <div class="row" style="text-align: -webkit-right;">
                <div class="col-md-10 d-flex flex-wrap mt-4">
                    <div class="d-flex align-items-center me-4 mb-1 ml-1">
                        <span class="d-inline-block"
                            style="width:40px; height:20px; background-color: #ffe0de; border:1px solid #ccc;"></span>
                        <span class="ms-2" style="margin-left: 5px;">Pending Followups</span>
                    </div>
                    <div class="d-flex align-items-center me-4 mb-1 ml-1">
                        <span class="d-inline-block"
                            style="width:40px; height:20px; background-color: #e9efff; border:1px solid #ccc;"></span>
                        <span class="ms-2" style="margin-left: 5px;">Today's Followup</span>
                    </div>
                    <div class="d-flex align-items-center me-4 mb-1 ml-1">
                        <span class="d-inline-block"
                            style="width:40px; height:20px; background-color: #90ee905e; border:1px solid #ccc;"></span>
                        <span class="ms-2" style="margin-left: 5px;">Converted To Enquiry</span>
                    </div>
                </div>
                <div class="col-md-2 m-auto">
                    <form action="{{ route('data.index') }}" method="GET">
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
                            <option value="next_followup_asc"
                                {{ request('sort_by') == 'next_followup_asc' ? 'selected' : '' }}>
                                Next Follow-up ASC
                            </option>
                            <option value="next_followup_desc"
                                {{ request('sort_by') == 'next_followup_desc' ? 'selected' : '' }}>
                                Next Follow-up DESC
                            </option>
                            <option value="last_updated_asc"
                                {{ request('sort_by') == 'last_updated_asc' ? 'selected' : '' }}>
                                Last Updated ASC
                            </option>
                            <option value="last_updated_desc"
                                {{ request('sort_by') == 'last_updated_desc' ? 'selected' : '' }}>
                                Last Updated DESC
                            </option>
                        </select>
                    </form>
                </div>
            </div>
            <table class="table table-bordered aiz-table mb-0">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Data Code</th>
                        <th style="word-break: break-word;width:15%;">Company Name</th>
                        <th style="width:15%; word-break: break-word;">Primary Contact</th>
                        <th class="text-center">Salesperson</th>
                        <th class="text-center">Source</th>
                        <th class="text-center">Entry Date</th>
                        <th class="text-center">Last Updated</th>
                        <th class="text-center">Next Follow-up</th>
                        <th class="text-center">{{ trans('messages.status') }}</th>
                        <th class="text-center">{{ trans('messages.options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @can('view_data')
                        @foreach ($data as $key => $dat)
                            @php
                                $bgcolor = '#fff';
                                if ($dat->status === 'convert_to_enquiry') {
                                    $bgcolor = '#90ee905e';
                                }

                                if (($dat->status == 'to_be_contacted' || $dat->status == 'contacted' || $dat->status =='ongoing_discussion' || $dat->status == 'not_responding') && $dat->next_followup != null) {
                                    if ($dat->next_followup === date('Y-m-d')) {
                                        $bgcolor = '#e9efff';
                                    }

                                    if ($dat->next_followup < date('Y-m-d')) {
                                        $bgcolor = '#ffe0de';
                                    }
                                } 
                            @endphp 
                            <tr style="background:{{ $bgcolor }};">
                                <td class="text-center">{{ $key + 1 + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td>{{ $dat->data_code }}</td>
                                <td style="word-break: break-word;position: relative;">
                                    
                                    <div>
                                        {{ $dat->company_name }}
                                        <a href="javascript:void(0)" class="show-popup-company" data-id="{{ $key }}">
                                            <i class="las la-info-circle fs-16 text-primary" style="cursor: pointer;"></i>
                                        </a>
                                    </div>

                                    <div class="popup-card-company" id="popup-company-{{ $key }}">
                                        <div class="popup-card-header">
                                            <span><i class="las la-id-card"></i> Company Info</span>
                                            <i class="las la-times close-popup-company" data-id="{{ $key }}"></i>
                                        </div>
                                        <div class="popup-card-body">
                                            @if ($dat->company_name)
                                                <div><strong>Company Name :</strong>
                                                    {{ $dat->company_name }}</div>
                                            @endif
                                            @if ($dat->company_email)
                                                <div> <strong>Email :</strong>
                                                    {{ $dat->company_email }}</div>
                                            @endif
                                            @if ($dat->company_address)
                                                <div><strong>Address :</strong>
                                                    {{ $dat->company_address }}</div>
                                            @endif
                                            @if ($dat->company_country)
                                                <div><strong>Country :</strong>
                                                    {{ $dat->country?->name }}</div>
                                            @endif

                                            @if ($dat->emirate)
                                                <div> <strong>Emirate :</strong>
                                                    {{ $dat->uae_emirate?->name }}</div>
                                            @endif
                                            @if ($dat->website_link)
                                                <div> <strong>Website :</strong>
                                                    {{ $dat->website_link }}</div>
                                            @endif
                                        </div>
                                    </div>

                                </td>
                                <td style="position: relative;word-break: break-word;">
                                    @php $primary = $dat->main_contact; @endphp

                                    @if ($primary)
                                        <div>
                                            {{ $primary->name }}
                                            <a href="javascript:void(0)" class="show-popup" data-id="{{ $dat->id }}">
                                                <i class="las la-info-circle fs-16 text-primary" style="cursor: pointer;"></i>
                                            </a>
                                        </div>

                                        <!-- Stylish Popup -->
                                        <div class="popup-card" id="popup-{{ $dat->id }}">
                                            <div class="popup-card-header">
                                                <span><i class="las la-id-card"></i> Contact Info</span>
                                                <i class="las la-times close-popup" data-id="{{ $dat->id }}"></i>
                                            </div>
                                            <div class="popup-card-body">
                                                @if ($primary->designation)
                                                    <div><i class="las la-user-tie"></i> <strong>Designation :</strong>
                                                        {{ $primary->designation }}</div>
                                                @endif
                                                @if ($primary->email)
                                                    <div><i class="las la-envelope"></i> <strong>Email :</strong>
                                                        {{ $primary->email }}</div>
                                                @endif
                                                @if ($primary->landline_number)
                                                    <div><i class="las la-phone"></i> <strong>Landline :</strong>
                                                        {{ $primary->landline_number }}</div>
                                                @endif
                                                @if ($primary->mobile_number)
                                                    <div><i class="las la-mobile"></i> <strong>Mobile :</strong>
                                                        {{ $primary->mobile_number }}</div>
                                                @endif
                                                @if ($primary->whatsapp_number)
                                                    <div><i class="lab la-whatsapp"></i> <strong>WhatsApp :</strong>
                                                        {{ $primary->whatsapp_number }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <em>No Primary Contact</em>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $dat->sale_person->name ?? '' }}
                                </td>

                                <td class="text-center">
                                    {{ $dat->source->name ?? '' }}
                                </td>

                                <td class="text-center">
                                    {{ $dat->entry_date ?? '' }}
                                </td>

                                <td class="text-center">
                                    {{ $dat->last_updated ?? '' }}
                                </td>

                                <td class="text-center">
                                    {{ $dat->next_followup ?? '' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge  badge-inline " style="background: {{$statuses[$dat->status]['bg'] ?? '' }}; color:{{$statuses[$dat->status]['filter_color'] ?? '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $dat->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($dat->status != 'convert_to_enquiry' && ((auth()->user()->id === $dat->sales_person) || auth()->user()->can('edit_all_data')))
                                        @can('edit_data')
                                            <a href="{{ route('data.edit', $dat->id) }}"
                                                class="btn btn-soft-success btn-sm btn-icon btn-circle" title="Edit data details">
                                                <i class="las la-edit"></i>
                                            </a>
                                        @endcan
                                    @endif

                                    <a href="{{ route('data.show', $dat->id) }}"
                                        class="btn btn-soft-warning btn-sm btn-icon btn-circle" title="View data details">
                                        <i class="las la-eye"></i>
                                    </a>

                                    @if ($dat->status != 'convert_to_enquiry' && ((auth()->user()->id === $dat->sales_person) || auth()->user()->can('edit_all_data')))
                                        @can('edit_data')
                                            <a href="javascript:void(0)"
                                                class="btn-change-status-icon btn btn-sm btn-icon btn-circle change-status-btn m-auto"
                                                data-id="{{ $dat->id }}" data-status="{{ $dat->status }}"
                                                title="Change Status" data-status-date="{{ $dat->last_updated }}"
                                                data-followup-date="{{ $dat->next_followup }}"
                                                data-comment="{{ $dat->last_comment ?? '' }}">
                                                <i class="las la-exchange-alt" style="margin-top: 2px;"></i>
                                            </a>
                                        @endcan
                                    @endif

                                    <a href="javascript:void(0)"
                                        class="btn btn-secondary btn-sm btn-icon btn-circle view-timeline m-auto"
                                        data-id="{{ $dat->id }}">
                                        <i class="las la-history" style="margin-top: 2px;"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endcan
                </tbody>
            </table>
            <div class="aiz-pagination mt-2">
                @can('view_data')
                    {{ $data->appends(request()->input())->links('pagination::bootstrap-5') }}
                @endcan
            </div>
        </div>
    </div>


    <div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="status-change-form" action="{{ route('data.changeStatus') }}" method="POST">
                @csrf
                <input type="hidden" name="data_id" id="status-data-id">
                <input type="hidden" id="original-status">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Status</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body row">
                        <div id="status-modal-errors" class="alert alert-danger" style="display:none;"></div>

                        <div class="form-group col-sm-12">
                            <label><b>Status</b></label>
                            <select name="status" id="data-status-select"
                                class="form-control form-control-sm aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select</option>
                                @foreach ($statuses as $key => $data)
                                    <option value="{{ $key }}"
                                        data-content="
                                        <div style='display: flex; align-items: center;'>
                                            <div style='width: 16px; height: 16px; background: {{ $data['bg'] }}; border-radius: 4px; margin-right: 8px;'></div>
                                            <span style='font-weight: 600;'>{{ $data['label'] }}</span>
                                        </div>
                                    "
                                        {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $data['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-6">
                            <label><b> Updated Date</b></label>
                            <input type="date" name="status_date" id="status_date"
                                class="form-control form-control-sm status_date" required>
                        </div>

                        <div class="form-group col-sm-6" id="next_followup_date">
                            <label><b> Next Follow-up Date</b></label>
                            <input type="date" name="followup_date" id="followup_date"
                                class="form-control form-control-sm followup_date" required>
                        </div>

                        <div class="form-group col-sm-12">
                            <label><b>Comment</b></label>
                            <textarea name="comment" id="statusComment" class="form-control form-control-sm"></textarea>
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

    <div class="modal fade" id="timelineModal" tabindex="-1" role="dialog" aria-labelledby="timelineModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="timelineModalLabel">Status Timeline</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="timelineModalContent">
                    <!-- Timeline HTML will be loaded here -->
                    <div class="text-center">
                        <span class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('style')
    <style>
        .popup-card-company {
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

        /* .popup-card-header {
            background: linear-gradient(135deg, #0058a2, #43a1ef);
            color: #fff;
            padding: 5px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 14px;
        } */

        .popup-card-header i.close-popup-company {
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            transition: 0.3s;
        }

        .popup-card-header i.close-popup-company:hover {
            color: #ffc107;
        }

        /* .popup-card-body {
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
        } */




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
            margin-bottom: 5px;
            /* display: flex; */
            align-items: center;
            gap: 6px;
        }

        .popup-card-body i {
            color: #007bff;
            font-size: 16px;
        }

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
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('click', '.view-timeline', function(e) {
            let dataId = $(this).data('id');
            $('#timelineModalContent').html(
                '<div class="text-center"><span class="spinner-border text-primary" role="status"></span></div>'
                );
            $('#timelineModal').modal('show');

            $.ajax({
                url: '/data/' + dataId + '/timeline',
                type: 'GET',
                success: function(res) {
                    $('#timelineModalContent').html(res);
                },
                error: function() {
                    $('#timelineModalContent').html(
                        '<p class="text-danger">Failed to load timeline.</p>');
                }
            });
        });

        document.getElementById('sort_by').addEventListener('change', function() {
            $('#sort_by_form').val(this.value);
            this.form.submit();
        });

        $(document).on('click', '.show-popup', function(e) {
            e.stopPropagation();
            $('.popup-card').hide(); // hide others
            const id = $(this).data('id');
            $('#popup-' + id).fadeIn(200);
        });

        $(document).on('click', '.close-popup', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');
            $('#popup-' + id).fadeOut(200);
        });

        $(document).on('click', function() {
            $('.popup-card').fadeOut(200);
        });

        $(document).on('click', '.popup-card', function(e) {
            e.stopPropagation(); // prevent outside click from closing if clicking inside
        });

        $(document).on('click', '.show-popup-company', function(e) {
            e.stopPropagation();
            $('.popup-card-company').hide(); // hide others
            const idd = $(this).data('id');
            $('#popup-company-' + idd).fadeIn(200);
        });

        $(document).on('click', '.close-popup-company', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');
            $('#popup-company-' + id).fadeOut(200);
        });


        $(document).on('click', '.change-status-btn', function() {
            const dataId = $(this).data('id');
            const currentStatus = $(this).data('status');
            const statusDate = $(this).data('status-date') || new Date().toISOString().split('T')[0];
            const followupDate = $(this).data('followup-date') || new Date().toISOString().split('T')[0];
            const comment = $(this).data('comment') || '';

            $('#status-data-id').val(dataId);
            $('#data-status-select').val(currentStatus).trigger('change');
            $('.status_date').val(statusDate);
            $('.followup_date').val(followupDate);

            $('textarea[name="comment"]').val(comment);
            $('#status-modal-errors').hide().html('');
            $('#original-status').val(currentStatus);

            $('#statusChangeModal').modal('show');
        });

        $('#data-status-select').on('change', function() {
            const selectedStatus = $(this).val();

            const dataId = $('#status-data-id').val();

            if (selectedStatus === 'convert_to_enquiry') {
                $('#next_followup_date').addClass('d-none');
            } else {
                $('#next_followup_date').removeClass('d-none');
            }

            // Load saved items
            $.get('/data-details/' + dataId + '/' + selectedStatus, function(response) {
                const comment = response.comment;
                const status_date = response.status_date;
                const followup_date = response.followup_date;

                $('#statusComment').val(comment);
                $('#status_date').val(status_date);
                $('#followup_date').val(followup_date);
            });
        });

        let pendingSubmit = false;
        $('#status-change-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();
            var flag = true;

            const originalStatus = $('#original-status').val();
            var selectedStatus = $('#data-status-select').val();

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

            if (selectedStatus === 'convert_to_enquiry' && !pendingSubmit) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'The data will be converted into an Enquiry. Do you want to continue?',
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

            if (flag == true) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(res) {
                        $('#statusChangeModal').modal('hide');
                        window.location.href=res.route;
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
    </script>
@endsection
