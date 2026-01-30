@extends('backend.layouts.app',['title' => 'Import Data'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Import Data</h5>
        <div class="d-flex gap-2 mb-0">
            <a href="{{ asset('assets/samples/Data_import.xlsx') }}"
            class="btn btn-success"
            download>
                Download Sample Excel
            </a>
        </div>
    </div>
   <div class="card-body">
        <div class="row">
            <!-- Left Column: Import Form -->
            <div class="col-md-4 m-auto p-0">
                <h6 class="fw-bold mb-2">Import Excel File</h6>
                
                <form action="{{ route('data.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="file" name="file" class="form-control" >
                    </div>
                    <div class="input-group mb-2">
                        <button class="btn btn-primary">Import Excel</button>
                    </div>
                    @error('file')
                        <div class="text-danger"> {{ $message }}</div>
                    @enderror
                </form>

            </div>

            <!-- Right Column: Status Reference -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">Status Reference</h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1"><strong>to_be_contacted</strong></li>
                            <li class="mb-1"><strong>contacted</strong></li>
                            <li class="mb-1"><strong>ongoing_discussion</strong></li>
                            <li class="mb-1"><strong>not_interested</strong></li>
                            <li class="mb-1"><strong>not_responding</strong></li>
                            <li class="mb-1"><strong>invalid_spam</strong></li>
                            <li><strong>convert_to_enquiry</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold">Source Reference</h6>
                    </div>

                    <div class="card-body p-3">
                        <div class="row">
                            @foreach ($sources->chunk(ceil($sources->count() / 2)) as $chunk)
                                <div class="col-6">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($chunk as $src)
                                            <li class="mb-1">
                                                <strong>{{ $src->name }}</strong>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                @if(session('import_errors'))
                    <div class="alert alert-danger mt-3">
                        <strong>Import Errors:</strong>
                        <ul class="mb-0">
                            @foreach(session('import_errors') as $error)
                                <li>Row {{ $error['row'] }} – {{ $error['error'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@section('style')
<style>
    
</style>    
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>


</script>
@endsection