@extends('backend.layouts.blank')
@section('content')
    <div class="row">
        <div class="col-xl-10 mx-auto">
            <!-- Start: error page -->
            <div class=" content-center">
                <div class="error-page text-center">
                    
                    <img src="{{ static_asset('assets/img/500.svg') }}" class="img-fluid w-75" style="height: 600px;">
                    <h1 class="h2 fw-700 mt-5">Something went wrong!</h1>
                    <p class="fs-16 opacity-60">Sorry for the inconvenience, but we're working on it. <br> Error code: 500</p>
                    
                </div>
            </div>
            <!-- End: error page -->
        </div>
    </div>
    
@endsection
