@extends('backend.layouts.app',['title' => 'All Roles'])

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{trans('messages.all_roles')}}</h5>
        @can('add_role')
            <a href="{{ route('roles.create') }}" class="btn btn-circle btn-success btn-sm">
                <span>{{trans('messages.add_new_role')}}</span>
            </a>
        @endcan
    </div>
    <div class="card-body">
        <table class="table aiz-table table-bordered">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{trans('messages.Role_Name')}}</th>
                    <th class="text-center" width="">{{trans('messages.options')}}</th>
                </tr>
            </thead>
            <tbody>
                @can('view_role')
                    @foreach($roles as $key => $role)
                        <tr>
                            <td>{{ ($key+1) + ($roles->currentPage() - 1)*$roles->perPage() }}</td>
                            <td>{{ $role->name}}</td>
                            <td class="text-center">
                                @can('edit_role')
                                    <a class="btn btn-soft-success btn-icon btn-sm btn-circle" href="{{route('roles.edit', ['id'=>$role->id] )}}" title="{{ trans('messages.edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endcan
                              
                                @can('delete_role')
                                    <a href="#" class="btn btn-soft-danger btn-sm btn-icon btn-circle confirm-delete" data-href="{{route('roles.destroy', $role->id)}}" title="{{ trans('messages.delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endcan
                               
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination">
            @can('view_role')
                {{ $roles->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
