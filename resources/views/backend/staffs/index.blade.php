@extends('backend.layouts.app',['title' => 'All Staffs'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{trans('messages.all_staffs')}}</h5>
        @can('add_staff')
            <a href="{{ route('staffs.create') }}" class="btn btn-circle btn-success btn-sm">
                <span>{{trans('messages.add_new_staffs')}}</span>
            </a>
        @endcan
    </div>
    <div class="card-body">
        <form class="row g-3" id="sort_brands" action="" method="GET">
            <div class="col-md-4 input-group  mb-1">
                <input type="text" class="form-control form-control-sm" id="search"
                    name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                    placeholder="{{ trans('messages.type_name_enter') }}">
            </div>

            <div class="col-md-4 input-group  mb-1">
                <select name="role_id"  class="form-control form-control-sm aiz-selectpicker">
                    <option value="">All Roles</option>
                    @foreach(App\Models\Role::where('is_active',1)->get() as $role)
                        <option value="{{ $role->name }}" {{ $role_id == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-4 mb-1">
                <button class="btn btn-info btn-sm" type="submit">Filter</button>
                <a href="{{ route('staffs.index') }}" class="btn btn-cancel btn-sm">Reset</a>
            </div>
        </form>
        <table class="table table-bordered aiz-table mb-0">
            <thead>
                <tr>
                    <th  class="text-center" width="10%">#</th>
                    <th>{{trans('messages.name')}}</th>
                    <th >{{trans('messages.email')}}</th>
                    
                    <th >{{trans('messages.phone')}}</th>
                    <th>{{trans('messages.role')}}</th>
                    <th class="text-center">Follow-up Mail Status</th>
                    <th class="text-center">Active Status</th>
                    <th class="text-center">{{trans('messages.options')}}</th>
                </tr>
            </thead>
            <tbody>
                @can('view_staff')
                    @foreach($users as $key => $staff)
                        <tr>
                            <td class="text-center">{{ ($key+1) + ($users->currentPage() - 1)*$users->perPage() }}</td>
                            <td>{{$staff->name}}</td>
                            <td>{{$staff->email}}</td>
                            
                            <td>{{$staff->phone}}</td>
                            <td>
                                {{ $staff->roles->pluck('name')->join(', ') }}
                            </td>
                            <td class="text-center">
                                @can('edit_staff')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_mail_status(this)" value="{{ $staff->id }}"
                                            <?php if ($staff->followup_mail_status == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <span></span>
                                    </label>
                                @endcan  
                            </td>

                            <td class="text-center">
                                @can('edit_staff')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_status(this)" value="{{ $staff->id }}"
                                            <?php if ($staff->banned == 0) {
                                                echo 'checked';
                                            } ?>>
                                        <span></span>
                                    </label>
                                @endcan  
                            </td>
                            <td class="text-center">
                                @can('edit_staff')
                                    <a class="btn btn-soft-success btn-sm btn-icon btn-circle" href="{{route('staffs.edit', encrypt($staff->id))}}" title="{{ trans('messages.edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endcan
                                    {{-- <a href="#" class="btn btn-danger btn-sm btn-icon btn-circle confirm-delete" data-href="{{route('staffs.destroy', $staff->id)}}" title="{{ trans('messages.delete') }}">
                                        <i class="las la-trash"></i>
                                    </a> --}}
                                </td>
                        </tr>
                    @endforeach
                @endcan
               
            </tbody>
        </table>
        @can('view_staff')
            <div class="aiz-pagination">
                {{ $users->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        @endcan
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
       
        function update_status(el) {
            // Determine the new status based on checkbox state
            var status = el.checked ? 0 : 1;
            var actionText = !status ? "activate" : "deactivate";

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to " + actionText + " this staff?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('staff.status') }}', {
                        _token: '{{ csrf_token() }}',
                        id: el.value,
                        status: status
                    }, function(data) {
                        if (data == 1) {
                            AIZ.plugins.notify('success', 'Staff status updated successfully');
                        } else {
                            AIZ.plugins.notify('danger', 'Something went wrong');
                        }
                        // Reload after 2 seconds
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    });
                } else {
                    // Revert checkbox if canceled
                    el.checked = !el.checked;
                }
            });
        }


        function update_mail_status(el) {
            var status = el.checked ? 1 : 0;
            var message = status ? "enable" : "disable";

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to " + message + " follow-up mail for this staff?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('staff.mail-status') }}', {
                        _token: '{{ csrf_token() }}',
                        id: el.value,
                        status: status
                    }, function(data) {
                        if (data == 1) {
                            AIZ.plugins.notify('success', 'Staff follow-up mail status updated successfully');
                        } else {
                            AIZ.plugins.notify('danger', 'Something went wrong');
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    });
                } else {
                    el.checked = !el.checked;
                }
            });
        }

    </script>
@endsection