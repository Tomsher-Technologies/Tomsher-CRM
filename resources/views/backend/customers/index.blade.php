@extends('backend.layouts.app',['title' => 'All Customers'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Customers</h5>
        @can('add_customer')
            <a href="{{ route('customers.create') }}" class="btn btn-success" >Add Customer</a>
        @endcan
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Search by Code, Name, Email, Country, Emirate" value="{{ request('keyword') }}">
            </div>

            <div class="col-md-3">
                <select name="industry" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                    <option value="">-- Select Industry --</option>
                    @foreach ($industries as $industry)
                        <option value="{{ $industry->id }}" {{ request('industry') == $industry->id ? 'selected' : '' }}>{{ $industry->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="is_active" class="form-control form-control-sm aiz-selectpicker">
                    <option value="">Active Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="user_id" id="user_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name  }} 
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
        <table class="table table-bordered aiz-table mb-0">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Customer Code</th>
                    <th style="word-break: break-all;width:20%;">Company Name</th>
                    <th style="width:20%;">Primary Contact</th>
                    <th class="text-center">Total Projects</th>
                    <th class="text-center">Total Enquiries</th>
                    <th class="text-center">Salesperson</th>
                    <th class="text-center">{{trans('messages.status')}}</th>
                    <th class="text-center">{{trans('messages.options')}}</th>
                </tr>
            </thead>
            <tbody>
                @can('view_customers')
                    @foreach($customers as $key => $cust)
                        <tr @if($cust->is_active != 1) style="background:#f7d3d369;" @endif>
                            <td class="text-center">{{ ($key+1) + ($customers->currentPage() - 1)*$customers->perPage() }}</td>
                            <td>{{ $cust->customer_code }}</td>
                            
                            <td style="word-break: break-word;position: relative;">
                                    
                                <div>
                                    {{ $cust->company_name }}
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
                                        @if ($cust->company_name)
                                            <div><strong>Company Name :</strong>
                                                {{ $cust->company_name }}</div>
                                        @endif
                                        @if ($cust->company_email)
                                            <div> <strong>Email :</strong>
                                                {{ $cust->company_email }}</div>
                                        @endif
                                        @if ($cust->company_address)
                                            <div><strong>Address :</strong>
                                                {{ $cust->company_address }}</div>
                                        @endif
                                        @if ($cust->company_country)
                                            <div><strong>Country :</strong>
                                                {{ $cust->country?->name }}</div>
                                        @endif

                                        @if ($cust->emirate)
                                            <div> <strong>Emirate :</strong>
                                                {{ $cust->uae_emirate?->name }}</div>
                                        @endif
                                        @if ($cust->website_link)
                                            <div> <strong>Website :</strong>
                                                {{ $cust->website_link }}</div>
                                        @endif
                                    </div>
                                </div>

                            </td>

                            <td style="position: relative;">
                                @php $primary = $cust->main_contact; @endphp
                            
                                @if($primary)
                                    <div>
                                        {{ $primary->name }}
                                        <a href="javascript:void(0)" class="show-popup" data-id="{{ $cust->id }}">
                                            <i class="las la-info-circle fs-16 text-primary" style="cursor: pointer;"></i>
                                        </a>
                                    </div>
                            
                                    <!-- Stylish Popup -->
                                    <div class="popup-card" id="popup-{{ $cust->id }}">
                                        <div class="popup-card-header">
                                            <span><i class="las la-id-card"></i> Contact Info</span>
                                            <i class="las la-times close-popup" data-id="{{ $cust->id }}"></i>
                                        </div>
                                        <div class="popup-card-body">
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
                                @else
                                    <em>No Primary Contact</em>
                                @endif
                            </td>
                            
                            
                            <td class="text-center">
                                <a href="{{ route('projects.index',['customer_id' => $cust->id]) }}">
                                    {{ $cust->projects->count() ?? 0 }}
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('enquiries.index',['customer_id' => $cust->id]) }}">
                                    {{ $cust->enquiries->count() ?? 0 }}
                                </a>
                            </td>

                            <td class="text-center">
                                {{ $cust->sale_person->name ?? '' }}
                            </td>

                            <td class="text-center">
                                @can('change_customer_status')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox"
                                            onchange="update_status(this)"
                                            value="{{ $cust->id }}"
                                            {{ $cust->is_active ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                @else
                                    @if ($cust->is_active)
                                        <span class="badge badge-success px-4 py-1" style="line-height: 1.2;">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-4 py-1" style="line-height: 1.2;">
                                            Inactive
                                        </span>
                                    @endif
                                @endcan

                            </td>
                            <td class="text-center">
                                @can('edit_customer')
                                    <a href="{{ route('customers.edit',$cust->id) }}" class="btn btn-soft-success btn-sm btn-icon btn-circle" title="Edit customer details">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endcan

                                <a href="{{ route('customers.show',$cust->id) }}" class="btn btn-soft-warning btn-sm btn-icon btn-circle" title="View customer details">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination mt-2">
            @can('view_customers')
                {{ $customers->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
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

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }


</style>    
@endsection

@section('script')
<script>

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


    function update_status(el) {
        if (el.checked) {
            var status = 1;
        } else {
            var status = 0;
        }
        $.post('{{ route('customer.status') }}', {
            _token: '{{ csrf_token() }}',
            id: el.value,
            status: status
        }, function(data) {
            if (data == 1) {
                AIZ.plugins.notify('success', 'Customer status updated successfully');
                setTimeout(function() {
                    window.location.reload();
                }, 3000);

            } else {
                AIZ.plugins.notify('danger', 'Something went wrong');
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            }
        });
    }
</script>
@endsection