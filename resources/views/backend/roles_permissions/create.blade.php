@extends('backend.layouts.app',['title' => 'Create Role'])

@section('content')

<div class="col-lg-10 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ trans('messages.Role_Information')}}</h5>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{ trans('messages.Role_Name')}} <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{ trans('messages.Role_Name')}}" value="{{ old('name') }}" id="name" name="name" class="form-control form-control-sm">
                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
               
                <div class="form-group row">
                    <label  class="col-md-3 col-from-label">Permissions <span class="text-danger">*</span></label>
                    <div class="col-md-9">

                        @foreach($permission as $parent)
                            <div class="col-sm-12 d-flex mt-2" >
                                <div class="permission-group">
                                    <label class="parent-label checkbox-label">
                                        <input type="checkbox" name="permissions[]" value="{{ $parent->name }}"
                                            class="parent-checkbox  demo-sw" data-parent="{{ $parent->name }}">
                                        <span class="mleft5">{{ $parent->title }}</span>
                                    </label>
                                
                                    <div class="child-container mt-3" style="margin-left: 20px;">
                                        @foreach($parent->children as $child)
                                            <label class="child-label checkbox-label">
                                                <input type="checkbox" name="permissions[]" value="{{ $child->name }}"
                                                    class="child-checkbox  demo-sw" data-parent="{{ $parent->name }}">
                                                <span class="mleft5">{{ $child->title }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach


                        {{-- @foreach($permission as $value)
                            @php 
                                $selected = '';
                                
                                if(old('permissions')){
                                    if(in_array($value->name, old('permissions'))){
                                        $selected = 'checked';
                                    }
                                }
                                // else{
                                //     $selected = 'checked';
                                // }
                                
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="col-from-label">{{  $value->title }}</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="permissions[]" id="permissions" class="form-control form-control-sm demo-sw" value="{{$value->name}}" {{ $selected }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        @endforeach --}}
                        @error('permissions')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary btn-sm">{{ trans('messages.Save')}}</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-cancel btn-sm">Cancel</a>
                </div>
            </div>
        </from>
    </div>
</div>

@endsection

@section('header')
<style>
    /* Style the checkbox container */
    .permission-group {
        /* margin-bottom: 15px; */
    }

    /* Style for parent checkboxes */
    .parent-label {
        font-weight: bold;
        font-size: 12px;
        display: flex;
        align-items: center;
        /* margin-bottom: 5px; */
    }

    .mleft5{
        margin-left: 5px;
    }
    .child-label{
        display: flex;
        align-items: center;
    }

    /* Child checkboxes section */
    .child-container {
        margin-left: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* Custom styled checkboxes */
    input[type="checkbox"] {
        width: 15px;
        height: 15px;
        margin-right: 2px;
        cursor: pointer;
    }

    .checkbox-label:hover {
        color: #0958a3;
        cursor: pointer;
    }
</style>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // When a child is checked/unchecked, update parent
            $('.child-checkbox').on('change', function () {
                let parentCheckbox = $('input[value="' + $(this).data('parent') + '"]');
                let allChildren = $('.child-checkbox[data-parent="' + $(this).data('parent') + '"]');
                let anyChecked = allChildren.is(':checked');

                parentCheckbox.prop('checked', anyChecked); // ✅ Check parent if any child is checked
            });

            // When a parent is checked/unchecked, update all children
            $('.parent-checkbox').on('change', function () {
                let allChildren = $('.child-checkbox[data-parent="' + $(this).data('parent') + '"]');
                allChildren.prop('checked', $(this).is(':checked')); // ✅ Check/uncheck all children
            });
        });
    </script>

@endsection