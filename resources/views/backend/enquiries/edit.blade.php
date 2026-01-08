@extends('backend.layouts.app', ['title' => 'Edit Enquiry'])
@section('content')
<div class="container">
    <div class="bg-white shadow-lg rounded-xl p-4">
        <h4 class="text-center mb-4">✏️ Edit Enquiry</h4>
        <form method="POST" action="{{ route('enquiries.update', $enquiry) }}">
            @method('PUT')
            @include('partials._form')
            
            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-save me-1"></i> Update Enquiry
                </button>


                

                @if (Session::has('previous_section') && Session::get('previous_section') === 'enquiry_view')
                    <a href="{{ Session::has('enquiry_view_last_url') ? Session::get('enquiry_view_last_url') : route('enquiries.index') }}" class="btn btn-info" >Cancel</a>
                @else
                    <a href="{{ Session::has('enquiries_last_url') ? Session::get('enquiries_last_url') : route('enquiries.index') }}" class="btn btn-info" >Cancel</a>
                @endif

            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    flatpickr("#enquiry_date", {
        dateFormat: "Y-m-d",
        maxDate: "today"
    });
</script>
@endsection