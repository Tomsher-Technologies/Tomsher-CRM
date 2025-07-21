<div >
	<div class="form-group">
		<label>{{ trans('messages.file').' '.trans('messages.name') }}</label>
		<input type="text" class="form-control form-control-sm" value="{{ $file->file_name }}" disabled>
	</div>
	<div class="form-group">
		<label>{{ trans('messages.file').' '.trans('messages.type') }}</label>
		<input type="text" class="form-control form-control-sm" value="{{ $file->type }}" disabled>
	</div>
	<div class="form-group">
		<label>{{ trans('messages.file').' '.trans('messages.size') }}</label>
		<input type="text" class="form-control form-control-sm" value="{{ formatBytes($file->file_size) }}" disabled>
	</div>
	<div class="form-group">
		<label>{{ trans('messages.uploaded_by') }}</label>
		<input type="text" class="form-control form-control-sm" value="{{ $file->user->name }}" disabled>
	</div>
	<div class="form-group">
		<label>{{ trans('messages.uploaded_at') }}</label>
		<input type="text" class="form-control form-control-sm" value="{{ $file->created_at }}" disabled>
	</div>
	<div class="form-group text-center">
		@php
			if($file->file_original_name == null){
			    $file_name = trans('messages.unknown');
			}else{
				$file_name = $file->file_original_name;
			}
		@endphp
		<a class="btn btn-secondary" href="{{ storage_asset($file->file_name) }}" target="_blank" download="{{ $file_name }}.{{ $file->extension }}">{{ trans('messages.download') }}</a>
	</div>
</div>