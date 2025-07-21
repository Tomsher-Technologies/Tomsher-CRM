<!-- Enquiry Dropdown -->
<div class="form-group  col-12">
    <label>Enquiry <span class="text-danger">*</span></label>
    <select name="enquiry_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
        @foreach($enquiries as $enquiry)
            <option value="{{ $enquiry->id }}"
                {{ old('enquiry_id', $followup->enquiry_id ?? '') == $enquiry->id ? 'selected' : '' }}>
                {{ $enquiry->enquiry_code }} - {{ $enquiry->customer->company_name }}
            </option>
        @endforeach
    </select>
    @error('enquiry_id')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>

<!-- Follow-up Type -->
<div class="form-group  col-12">
    <label>Follow-up Type <span class="text-danger">*</span></label>
    <select name="followup_type" id="followup_type" class="form-control form-control-sm" onchange="handleTypeChange()" >
        @php $type = old('followup_type', $followup->followup_type ?? '') @endphp
        <option value="call" {{ $type === 'call' ? 'selected' : '' }}>Call</option>
        <option value="email" {{ $type === 'email' ? 'selected' : '' }}>Email</option>
        <option value="whatsapp" {{ $type === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
        <option value="meeting" {{ $type === 'meeting' ? 'selected' : '' }}>Meeting</option>
    </select>
    @error('followup_type')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>
@php
    $sub_type = old('sub_type', $followup->sub_type ?? '');
    $type = old('followup_type', $followup->followup_type ?? '');
@endphp
<!-- Sub-Type -->
<div class="form-group  col-12">
    <label>Sub-Type <span class="text-danger">*</span></label>
    <input type="hidden" id="old_sub_type" value="{{ $sub_type }}">
    <select name="sub_type" id="sub_type" class="form-control form-control-sm" >
       

        @if(in_array($type, ['call', 'email', 'whatsapp']))
            <option value="incoming" {{ $sub_type === 'incoming' ? 'selected' : '' }}>Incoming</option>
            <option value="outgoing" {{ $sub_type === 'outgoing' ? 'selected' : '' }}>Outgoing</option>
        @elseif($type === 'meeting')
            <option value="online" {{ $sub_type === 'online' ? 'selected' : '' }}>Online</option>
            <option value="in-person" {{ $sub_type === 'in-person' ? 'selected' : '' }}>In-Person</option>
        @endif
    </select>
    @error('sub_type')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>

<!-- Time -->
<div class="form-group  col-12" id="followup-time-group" style="{{ old('followup_type', $followup->followup_type) != 'meeting' ? '' : 'display: none;' }}">
    <label>Time <span class="text-danger">*</span></label>
    <input type="text" name="followup_time" id="followup_time" class="form-control form-control-sm" value="{{ old('followup_time', isset($followup) ? \Carbon\Carbon::parse($followup->followup_time)->format('Y-m-d h:i:s') : '') }}">
    @error('followup_time')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>

<div class="form-group col-6" id="meeting-from-group" style="{{ old('followup_type', $followup->followup_type) == 'meeting' ? '' : 'display: none;' }}">
    <label>Meeting From <span class="text-danger">*</span></label>
    <input type="datetime-local" name="followup_from" id="followup_from" class="form-control form-control-sm"
           value="{{ old('followup_from', \Carbon\Carbon::parse($followup->followup_from)->format('Y-m-d h:i:s')) }}"/>
    @error('followup_from')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>

<div class="form-group col-6" id="meeting-to-group" style="{{ old('followup_type', $followup->followup_type) == 'meeting' ? '' : 'display: none;' }}">
    <label>Meeting To <span class="text-danger">*</span></label>
    <input type="datetime-local" name="followup_to" id="followup_to" class="form-control form-control-sm"
           value="{{ old('followup_to', \Carbon\Carbon::parse($followup->followup_to)->format('Y-m-d h:i:s')) }}"/>
    @error('followup_to')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>

<!-- Subject -->
<div class="form-group  col-12">
    <label>Pre-Follow-up Comment <span class="text-danger">*</span></label>
    <textarea name="comment" class="form-control form-control-sm" rows="3">{{ old('comment', $followup->subject ?? '') }}</textarea>
    @error('comment')
        <span class="text-danger">{{$message}}</span>
    @enderror
</div>

<!-- Location -->
<div class="form-group  col-12" id="location_group" style="{{ (old('followup_type', $followup->followup_type ?? '') === 'meeting') ? '' : 'display:none;' }}">
    <label>Location <span class="text-danger">*</span></label>
    <input type="text" name="location" class="form-control form-control-sm" value="{{ old('location', $followup->location ?? '') }}">
    @error('location')
        <span class="text-danger">{{$message}}</span>
    @enderror
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
