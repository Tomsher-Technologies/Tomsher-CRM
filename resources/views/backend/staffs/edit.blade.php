@extends('backend.layouts.app',['title' => 'Edit Staff'])

@section('style')
<style>
    /* Card & Section layout */
    .crm-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        background: #fff;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }
    .crm-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }
    .crm-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .crm-card-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
    }
    .crm-card-header h5 i {
        font-size: 1.25rem;
        margin-right: 8px;
        color: #4f46e5;
    }
    .crm-card-body {
        padding: 24px;
    }
    
    /* Input custom styling */
    .crm-input-group {
        margin-bottom: 20px;
    }
    .crm-label {
        font-weight: 500;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 6px;
        display: block;
    }
    .crm-label span.text-danger {
        margin-left: 2px;
    }
    .crm-input {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 10px 14px !important;
        height: auto !important;
        font-size: 0.875rem !important;
        color: #334155 !important;
        background-color: #fff !important;
        transition: all 0.2s ease-in-out !important;
    }
    .crm-input:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
        outline: none !important;
    }
    
    /* Bootstrap Select Picker Custom Styles */
    .bootstrap-select > .btn {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #fff !important;
        padding: 10px 14px !important;
        font-size: 0.875rem !important;
        color: #334155 !important;
        height: auto !important;
        box-shadow: none !important;
        transition: all 0.2s ease-in-out !important;
    }
    .bootstrap-select > .btn:focus, .bootstrap-select.show > .btn {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
        outline: none !important;
    }
    .bootstrap-select .dropdown-toggle .filter-option {
        padding-left: 0 !important;
        font-weight: 400 !important;
    }

    /* Error and alert message styling */
    .crm-error-msg {
        font-size: 0.775rem;
        color: #ef4444;
        margin-top: 5px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }
    .crm-error-msg i {
        margin-right: 4px;
        font-size: 0.9rem;
    }

    /* Action bar styles */
    .crm-action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: #fff;
        padding: 16px 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    .crm-page-title h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }
    .crm-page-title p {
        margin: 4px 0 0 0;
        font-size: 0.825rem;
        color: #64748b;
    }
    
    /* Custom button styling */
    .crm-btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 10px 24px !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15) !important;
        transition: all 0.2s ease-in-out !important;
        cursor: pointer;
    }
    .crm-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25) !important;
        color: #fff !important;
    }
    .crm-btn-cancel {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 10px 24px !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        transition: all 0.2s ease-in-out !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .crm-btn-cancel:hover {
        background: #e2e8f0 !important;
        color: #334155 !important;
        text-decoration: none !important;
    }
    .crm-btn-cancel i {
        margin-right: 6px;
    }

    /* CC Emails dynamic rows styling */
    .cc-email-row {
        position: relative;
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        animation: slideIn 0.25s ease-out;
    }
    .cc-email-row .crm-input {
        flex-grow: 1;
        margin-right: 8px;
    }
    .btn-delete-cc {
        background: #fef2f2 !important;
        border: 1px solid #fecaca !important;
        color: #ef4444 !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 42px;
    }
    .btn-delete-cc:hover {
        background: #fee2e2 !important;
        color: #dc2626 !important;
    }
    .btn-add-cc {
        color: #4f46e5 !important;
        background: #eeebff !important;
        border: 1px dashed #c7bbfd !important;
        font-weight: 500;
        font-size: 0.825rem;
        border-radius: 8px;
        width: 100%;
        padding: 10px 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-add-cc:hover {
        background: #e0daff !important;
        border-color: #818cf8 !important;
        text-decoration: none !important;
    }
    .btn-add-cc i {
        font-size: 1.1rem;
        margin-right: 6px;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Action Bar -->
    <div class="crm-action-bar">
        <div class="crm-page-title">
            <h3>Edit Staff</h3>
            <p>Update user details, modify roles and hierarchy, and manage CC email configurations.</p>
        </div>
        <div>
            <a href="{{ route('staffs.index') }}" class="crm-btn-cancel">
                <i class="las la-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('staffs.update', $staff->id) }}" method="POST">
        <input name="_method" type="hidden" value="PATCH">
        @csrf
        
        <div class="row">
            <!-- Left Column: Profile & Workplace Details -->
            <div class="col-lg-7">
                <!-- Section 1: Staff Details -->
                <div class="crm-card">
                    <div class="crm-card-header">
                        <h5><i class="las la-user-circle"></i> Basic Information</h5>
                    </div>
                    <div class="crm-card-body">
                        <div class="crm-input-group">
                            <label class="crm-label" for="name">{{trans('messages.name')}} <span class="text-danger">*</span></label>
                            <input type="text" placeholder="{{trans('messages.name')}}" id="name" name="name" class="form-control crm-input" value="{{ old('name', $staff->name) }}">
                            @error('name')
                                <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="crm-input-group">
                                    <label class="crm-label" for="email">{{trans('messages.email')}} <span class="text-danger">*</span></label>
                                    <input type="text" placeholder="{{trans('messages.email')}}" id="email" name="email" class="form-control crm-input" value="{{ old('email', $staff->email) }}">
                                    @error('email')
                                        <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="crm-input-group">
                                    <label class="crm-label" for="mobile">{{trans('messages.phone')}}</label>
                                    <input type="text" placeholder="{{trans('messages.phone')}}" id="mobile" name="mobile" class="form-control crm-input" value="{{ old('mobile', $staff->phone) }}">
                                    @error('mobile')
                                        <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Workplace Details -->
                <div class="crm-card">
                    <div class="crm-card-header">
                        <h5><i class="las la-briefcase"></i> Role & Hierarchy</h5>
                    </div>
                    <div class="crm-card-body">
                        <div class="crm-input-group">
                            <label class="crm-label" for="role_id">{{trans('messages.role')}} <span class="text-danger">*</span></label>
                            <select name="role_id" id="role_id" required class="form-control aiz-selectpicker">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $staff->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="crm-input-group">
                                    <label class="crm-label" for="reporting_to_id">Reporting To</label>
                                    <select name="reporting_to_id" id="reporting_to_id" class="form-control aiz-selectpicker" data-live-search="true">
                                        <option value="">Select Staff</option>
                                        @foreach($staffs as $s)
                                            <option value="{{ $s->id }}" {{ old('reporting_to_id', $staff->reporting_to_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('reporting_to_id')
                                        <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="crm-input-group">
                                    <label class="crm-label" for="manager_id">Manager</label>
                                    <select name="manager_id" id="manager_id" class="form-control aiz-selectpicker" data-live-search="true">
                                        <option value="">Select Manager</option>
                                        @foreach($staffs as $s)
                                            <option value="{{ $s->id }}" {{ old('manager_id', $staff->manager_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('manager_id')
                                        <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Security & Settings -->
            <div class="col-lg-5">
                <!-- Section 3: Account Security -->
                <div class="crm-card">
                    <div class="crm-card-header">
                        <h5><i class="las la-key"></i> Account Credentials</h5>
                    </div>
                    <div class="crm-card-body">
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.8rem; border-radius: 8px; border: 1px solid #bae6fd; background: #f0f9ff; color: #0369a1; display: flex; align-items: flex-start;">
                            <i class="las la-info-circle mr-2 mt-1" style="font-size: 1.1rem;"></i>
                            <span>Leave password fields blank if you do not wish to change the password.</span>
                        </div>

                        <div class="crm-input-group">
                            <label class="crm-label" for="password">{{trans('messages.password')}}</label>
                            <input type="password" placeholder="New Password (min 6 characters)" autocomplete="new-password" id="password" name="password" class="form-control crm-input">
                            @error('password')
                                <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="crm-input-group">
                            <label class="crm-label" for="password_confirmation">Confirm Password</label>
                            <input type="password" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation" class="form-control crm-input">
                            @error('password_confirmation')
                                <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Email Notifications -->
                <div class="crm-card">
                    <div class="crm-card-header">
                        <h5><i class="las la-envelope-open-text"></i> Follow-up Mail Setup</h5>
                    </div>
                    <div class="crm-card-body">
                        <div class="crm-input-group">
                            <label class="crm-label" for="followup_mail_status">Daily Follow-up Mail <span class="text-danger">*</span></label>
                            <select name="followup_mail_status" id="followup_mail_status" class="form-control aiz-selectpicker">
                                <option value="1" @if(old('followup_mail_status', $staff->followup_mail_status) == 1) selected @endif>Enable</option>
                                <option value="0" @if(old('followup_mail_status', $staff->followup_mail_status) == 0) selected @endif>Disable</option>
                            </select>
                            @error('followup_mail_status')
                                <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="crm-input-group mb-0">
                            <label class="crm-label">CC Emails</label>
                            <div id="cc-emails-container">
                                @php
                                    $cc_emails = old('cc_emails', (isset($staff) && $staff->followup_cc != NULL) ? json_decode($staff->followup_cc, true) : []);
                                @endphp
                                @if(empty($cc_emails))
                                    <div class="cc-email-row">
                                        <input type="email" name="cc_emails[]" class="form-control crm-input" placeholder="Enter CC email">
                                        <button type="button" class="btn btn-delete-cc remove-cc" style="display: none;"><i class="las la-trash"></i></button>
                                    </div>
                                @else
                                    @foreach($cc_emails as $index => $email)
                                        <div class="cc-email-row">
                                            <input type="email" name="cc_emails[]" class="form-control crm-input" placeholder="Enter CC email" value="{{ $email }}">
                                            <button type="button" class="btn btn-delete-cc remove-cc" @if(count($cc_emails) == 1) style="display: none;" @endif><i class="las la-trash"></i></button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-add-cc mt-2">
                                <i class="las la-plus-circle"></i> Add CC Email
                            </button>
                            @error('cc_emails.*')
                                <div class="crm-error-msg"><i class="las la-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Submission Panel -->
                <div class="crm-card">
                    <div class="crm-card-body text-right">
                        <a href="{{ route('staffs.index') }}" class="crm-btn-cancel mr-2">{{trans('messages.cancel')}}</a>
                        <button type="submit" class="crm-btn-primary">{{trans('messages.Save')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('cc-emails-container');
        const addButton = document.querySelector('.btn-add-cc');

        // Function to toggle delete buttons visibility
        function toggleDeleteButtons() {
            const rows = container.querySelectorAll('.cc-email-row');
            rows.forEach((row, index) => {
                const deleteBtn = row.querySelector('.remove-cc');
                if (rows.length === 1) {
                    deleteBtn.style.display = 'none';
                } else {
                    deleteBtn.style.display = 'flex';
                }
            });
        }

        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            const newRow = document.createElement('div');
            newRow.classList.add('cc-email-row');
            newRow.innerHTML = `
                <input type="email" name="cc_emails[]" class="form-control crm-input" placeholder="Enter CC email">
                <button type="button" class="btn btn-delete-cc remove-cc"><i class="las la-trash"></i></button>
            `;
            container.appendChild(newRow);
            toggleDeleteButtons();
        });

        container.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.remove-cc');
            if (deleteBtn) {
                e.preventDefault();
                deleteBtn.closest('.cc-email-row').remove();
                toggleDeleteButtons();
            }
        });

        // Initialize toggle on load
        toggleDeleteButtons();
    });
</script>
@endsection