@extends('backend.layouts.app',['title' => 'Add Follow-up'])

@section('content')
<style>
    .full-height-center {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f8f9fa;
    }
</style>

<div class="full-height-center">
    <div class="container" style="max-width: 800px;">
        <div class="bg-white shadow-lg rounded-xl p-4">
            <h4 class="text-center mb-4">➕ Add Follow-up</h4>
            <form action="{{ route('followups.store') }}" class="row" method="POST">
                @csrf

                <!-- Enquiry Dropdown -->
                <div class="form-group col-12">
                    <label>Enquiry <span class="text-danger">*</span></label>
                    <select name="enquiry_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">Select Enquiry</option>
                        @foreach($enquiries as $enquiry)
                            <option {{ (old('enquiry_id', $enquiry_id ?? '')== $enquiry->id) ? 'selected' : '' }} value="{{ $enquiry->id }}">{{ $enquiry->enquiry_code }} - {{ $enquiry->customer->company_name }}</option>
                        @endforeach
                    </select>
                    @error('enquiry_id')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Follow-up Type -->
                <div class="form-group col-12">
                    <label>Follow-up Type <span class="text-danger">*</span></label>
                    <select name="followup_type" id="followup_type" class="form-control form-control-sm" onchange="handleTypeChange()" >
                        <option value="">Select</option>
                        <option {{ (old('followup_type')== "call") ? 'selected' : '' }} value="call">Call</option>
                        <option {{ (old('followup_type')== "email") ? 'selected' : '' }} value="email">Email</option>
                        <option {{ (old('followup_type')== "whatsapp") ? 'selected' : '' }} value="whatsapp">WhatsApp</option>
                        <option {{ (old('followup_type')== "meeting") ? 'selected' : '' }} value="meeting">Meeting</option>
                    </select>
                    @error('followup_type')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Sub-Type -->
                <div class="form-group col-12">
                    <label>Sub-Type <span class="text-danger">*</span></label>
                    <input type="hidden" id="old_sub_type" value="{{ old('sub_type') }}">
                    <select name="sub_type" id="sub_type" class="form-control form-control-sm" >
                        <!-- Populated by JS -->
                    </select>
                    @error('sub_type')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Follow-up Time -->
                <div class="form-group col-12" id="followup-time-group">
                    <label>Time <span class="text-danger">*</span></label>
                    <input type="text" name="followup_time" id="followup_time" class="form-control form-control-sm"  value="{{ old('followup_time') }}"/>
                    @error('followup_time')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group col-6" id="meeting-from-group" style="display: none;">
                    <label>Meeting From <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="followup_from" id="followup_from" class="form-control form-control-sm" value="{{ old('followup_from') }}"/>
                    @error('followup_from')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group col-6" id="meeting-to-group" style="display: none;">
                    <label>Meeting To <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="followup_to" id="followup_to" class="form-control form-control-sm" value="{{ old('followup_to') }}"/>
                    @error('followup_to')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Subject -->
                <div class="form-group col-12">
                    <label>Pre-Follow-up Comment <span class="text-danger">*</span></label>
                    <textarea name="comment" class="form-control form-control-sm" rows="3">{{ old('comment') }}</textarea>
                    @error('comment')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <!-- Location (Only for Meeting) -->
                <div class="form-group col-12" id="location_group" style="display:none;">
                    <label>Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control form-control-sm"  value="{{ old('location') }}"/>
                    @error('location')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group col-12" id="participants-section" style="display: none;">
                    <label for="participants">Meeting Participants (excluding yourself)</label>
                    <select name="participants[]" id="participants" class="form-control form-control-sm aiz-selectpicker" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @if(!empty($selectedParticipants) && in_array($user->id, $selectedParticipants)) selected @endif>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="rescheduled" {{ old('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>Post-Follow-up Comment</label>
                    <textarea name="post_comment" class="form-control form-control-sm" rows="3">{{ old('post_comment') }}</textarea>
                    @error('post_comment')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="col-12 text-center">
                    <button class="btn btn-success mt-3">💾 Save Follow-up</button>
                    

                    @if (Session::has('previous_section') && Session::get('previous_section') === 'enquiry')
                        <a href="{{ Session::has('enquiries_last_url') ? Session::get('enquiries_last_url') : route('enquiries.index') }}" class="btn btn-info mt-3" >Cancel</a>
                    @elseif (Session::has('previous_section') && Session::get('previous_section') === 'followup')
                        <a href="{{ Session::has('followups_last_url') ? Session::get('followups_last_url') : route('followups.index') }}" class="btn btn-info mt-3" >Cancel</a>
                    @elseif (Session::has('previous_section') && Session::get('previous_section') === 'enquiry_view')
                        <a href="{{ Session::has('enquiry_view_last_url') ? Session::get('enquiry_view_last_url') : route('enquiries.index') }}" class="btn btn-info mt-3" >Cancel</a>
                    @else
                        <a href="{{ route('followups.index') }}" class="btn btn-info mt-3" >Cancel</a>
                    @endif
                    
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function handleTypeChange() {
        const type = document.getElementById('followup_type').value;
        const subType = document.getElementById('sub_type');
        const locationGroup = document.getElementById('location_group');
        const oldSubType = document.getElementById('old_sub_type')?.value;
        const participantsSection = document.getElementById('participants-section');

        subType.innerHTML = '';
        participantsSection.style.display = 'none';
        $('#followup-time-group').show();
        $('#meeting-from-group, #meeting-to-group').hide();

        if (type === 'call' || type === 'email' || type === 'whatsapp') {
            subType.innerHTML = `
                <option value="incoming" ${oldSubType === 'incoming' ? 'selected' : ''}>Incoming</option>
                <option value="outgoing" ${oldSubType === 'outgoing' ? 'selected' : ''}>Outgoing</option>
            `;
            locationGroup.style.display = 'none';
        } else if (type === 'meeting') {
            subType.innerHTML = `
                <option value="online" ${oldSubType === 'online' ? 'selected' : ''}>Online</option>
                <option value="in-person" ${oldSubType === 'in-person' ? 'selected' : ''}>In-Person</option>
            `;
            locationGroup.style.display = 'block';
            participantsSection.style.display = 'block';
            $('#followup-time-group').hide();
            $('#meeting-from-group, #meeting-to-group').show();
        } else {
            locationGroup.style.display = 'none';
        }
    }

    // Call it once on page load if there's an old value
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('followup_type').value) {
            handleTypeChange();
        }

        flatpickr("#followup_time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: false
        });
        flatpickr("#followup_from", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: false
        });
        flatpickr("#followup_to", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: false
        });
    });
</script>
@endsection
