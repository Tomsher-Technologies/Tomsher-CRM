@extends('backend.layouts.app',['title' => 'Create Staff'])

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h4">{{trans('messages.staff_info')}}</h5>
            </div>

            <form class="form-horizontal" autocomplete="off" action="{{ route('staffs.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{trans('messages.name')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{trans('messages.name')}}" id="name" name="name" class="form-control form-control-sm" value="{{ old('name') }}">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{trans('messages.email')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{trans('messages.email')}}" id="email" name="email" class="form-control form-control-sm"  value="{{ old('email') }}">
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="mobile">{{trans('messages.phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{trans('messages.phone')}}" id="mobile" name="mobile" class="form-control form-control-sm"  value="{{ old('mobile') }}">
                            @error('mobile')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">{{trans('messages.password')}} <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="{{trans('messages.password')}} " autocomplete="new-password" id="password" name="password" class="form-control form-control-sm"  value="{{ old('password') }}">
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
                            <select name="role"  class="form-control form-control-sm aiz-selectpicker">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <h6>Follow-up Mail Setup
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="followup_mail_status">Daily Follow-up Mail <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="followup_mail_status"  class="form-control form-control-sm">
                                <option value="1" @if(old('followup_mail_status') == 1) selected @endif>Enable</option>
                                <option value="0" @if(old('followup_mail_status') == 0) selected @endif>Disable</option>
                            </select>
                            @error('followup_mail_status')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                   <div class="form-group row">
                        <label class="col-sm-3 col-from-label">CC Emails</label>
                        <div class="col-sm-9">
                            <div id="cc-emails-container">
                                <div class="cc-email-row mb-1 d-flex align-items-center">
                                    <input type="email" name="cc_emails[]" class="form-control form-control-sm mr-2" placeholder="Enter CC email">
                                    <button type="button" class="btn btn-sm btn-success add-cc">+</button>
                                </div>
                            </div>
                            @error('cc_emails.*')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{trans('messages.Save')}}</button>
                        <a href="{{ route('staffs.index') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('cc-emails-container');

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-cc')) {
                const newRow = document.createElement('div');
                newRow.classList.add('cc-email-row', 'mb-1', 'd-flex', 'align-items-center');
                newRow.innerHTML = `
                    <input type="email" name="cc_emails[]" class="form-control form-control-sm mr-2" placeholder="Enter CC email">
                    <button type="button" class="btn btn-sm btn-danger remove-cc">-</button>
                `;
                container.appendChild(newRow);
            } else if (e.target.classList.contains('remove-cc')) {
                e.target.closest('.cc-email-row').remove();
            }
        });
    });
</script>
@endsection