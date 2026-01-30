@extends('backend.layouts.app',['title' => 'Edit Follow-up'])

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
            <h4 class="text-center mb-4">✏️ Edit Follow-up</h4>

            <form action="{{ route('followups.update', $followup->id) }}" method="POST" class="row">
                @csrf
                @method('PUT')

                @include('backend.followups.partials.form', ['followup' => $followup])
                
                <div class="form-group  col-12" id="participants-section" style="{{ old('followup_type', $followup->followup_type ?? '') === 'meeting' ? '' : 'display: none;' }}">
                    <label for="participants">Meeting Participants (excluding yourself)</label>

                    <select name="participants[]" id="participants" class="form-control form-control-sm aiz-selectpicker" multiple data-live-search="true">
                        @foreach($users as $user)
                            @if($user->id !== auth()->id())
                                <option value="{{ $user->id }}"
                                    @if(!empty($participantIds) && in_array($user->id, $participantIds)) selected @endif>
                                    {{ $user->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="form-group  col-12">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="pending" {{ $followup->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $followup->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="canceled" {{ $followup->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="rescheduled" {{ $followup->status == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                    </select>
                </div>

                <div class="form-group  col-12">
                    <label>Post-Follow-up Comment </label>
                    <textarea name="post_comment" class="form-control form-control-sm"  rows="3">{{ old('post_comment', $followup->post_comment ?? '') }}</textarea>
                    @error('post_comment')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>                

                <button class="btn btn-success mt-3 btn-sm">Update</button>
                <a href="{{ Session::has('followups_last_url') ? Session::get('followups_last_url') : route('followups.index') }}" class="btn btn-info mt-3 btn-sm" >Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
