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

                    <div class="form-group">
                        <h6>Follow-up Mail Setup
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="followup_mail_status">Daily Follow-up Mail <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="followup_mail_status"  class="form-control form-control-sm">
                                <option value="1" @if(old('followup_mail_status', $staff->followup_mail_status) == 1) selected @endif>Enable</option>
                                <option value="0" @if(old('followup_mail_status', $staff->followup_mail_status) == 0) selected @endif>Disable</option>
                            </select>
                            @error('followup_mail_status')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label">CC Emails</label>
                        <div class="col-sm-9">
                            <div id="cc-emails-wrapper">
                                @php
                                    $cc_emails = old('cc_emails', (isset($staff) && $staff->followup_cc != NULL) ? json_decode($staff->followup_cc, true) : []);
                                @endphp
                                @foreach($cc_emails as $email)
                                    <div class="input-group mb-1 cc-email-row">
                                        <input type="email" name="cc_emails[]" class="form-control form-control-sm" placeholder="CC Email" value="{{ $email }}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-cc-email">&times;</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-1" id="add-cc-email">Add New</button>
                            @error('cc_emails.*')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary btn-sm">{{trans('messages.Save')}}</button>
                        <a href="{{ route('staffs.index') }}" class="btn btn-cancel btn-sm">{{trans('messages.cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('cc-emails-wrapper');
        const addBtn = document.getElementById('add-cc-email');

        addBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('input-group', 'mb-1', 'cc-email-row');
            newRow.innerHTML = `
                <input type="email" name="cc_emails[]" class="form-control form-control-sm" placeholder="CC Email">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-cc-email">&times;</button>
                </div>
            `;
            wrapper.appendChild(newRow);
        });

        // Remove row
        wrapper.addEventListener('click', function(e) {
            if(e.target.classList.contains('remove-cc-email')) {
                e.target.closest('.cc-email-row').remove();
            }
        });
    });
</script>
@endsection