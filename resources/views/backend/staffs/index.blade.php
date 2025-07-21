@extends('backend.layouts.app',['title' => 'All Staffs'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{trans('messages.all_staffs')}}</h5>
        @can('add_staff')
            <a href="{{ route('staffs.create') }}" class="btn btn-circle btn-success">
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
                <button class="btn btn-info " type="submit">Filter</button>
                <a href="{{ route('staffs.index') }}" class="btn btn-cancel">Reset</a>
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
                    <th class="text-center">{{trans('messages.status')}}</th>
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
    <script type="text/javascript">
       
        function update_status(el) {
            if (el.checked) {
                var status = 0;
            } else {
                var status = 1;
            }
            $.post('{{ route('staff.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', 'Staff status updated successfully');
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