@extends('backend.layouts.app',['title' => 'Edit Staff'])

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h4">{{trans('messages.staff_info')}}</h5>
            </div>

            <form action="{{ route('staffs.update', $staff->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{trans('messages.name')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{trans('messages.name')}}" id="name" name="name" value="{{ old('name', $staff->name) }}" class="form-control form-control-sm">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{trans('messages.email')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{trans('messages.email')}}" id="email" name="email" value="{{ old('email', $staff->email) }}" class="form-control form-control-sm">
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="mobile">{{trans('messages.phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{trans('messages.phone')}}" id="mobile" name="mobile" value="{{ old('mobile', $staff->phone) }}" class="form-control form-control-sm" >
                            @error('mobile')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">{{trans('messages.password')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="{{trans('messages.password')}}" autocomplete="new-password"  id="password" name="password" class="form-control form-control-sm"  value="{{ old('password') }}">
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">Confirm Password <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation" class="form-control form-control-sm"  value="{{ old('password_confirmation') }}">
                            @error('password_confirmation')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{trans('messages.role')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="role_id" required class="form-control  form-control-sm aiz-selectpicker">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $staff->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{trans('messages.Save')}}</button>
                        <a href="{{ route('staffs.index') }}" class="btn btn-cancel">{{trans('messages.cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
