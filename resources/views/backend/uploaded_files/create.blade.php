@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h5 class="h4">{{trans('messages.upload_new_file')}}</h5>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('uploaded-files.index') }}" class="btn btn-link text-reset">
				<i class="las la-angle-left"></i>
				<span>{{trans('messages.back_to_uploaded_files')}}</span>
			</a>
		</div>
	</div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{trans('messages.drag_drop_your_files')}}</h5>
    </div>
    <div class="card-body">
    	<div id="aiz-upload-files" class="h-420px" style="min-height: 65vh">
    		
    	</div>
    </div>
</div>
@endsection

@section('script')
	<script type="text/javascript">
		$(document).ready(function() {
			AIZ.plugins.aizUppy();
		});
	</script>
@endsection