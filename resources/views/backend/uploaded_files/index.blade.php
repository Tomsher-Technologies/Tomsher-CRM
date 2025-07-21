@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h5 class="h4">{{trans('messages.all').' '.trans('messages.uploaded_files')}}</h5>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('uploaded-files.create') }}" class="btn btn-primary">
				<span>{{trans('messages.upload_new_file')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <form id="sort_uploads" action="">
        <div class="card-header row gutters-5">
            <div class="col-md-3">
                <h5 class="mb-0 h6">{{trans('messages.all').' '.trans('messages.files')}}</h5>
            </div>
            <div class="col-md-3 ml-auto mr-0">
                <select class="form-control  form-control-sm aiz-selectpicker" name="sort" onchange="sort_uploads()">
                    <option value="newest" @if($sort_by == 'newest') selected="" @endif>{{ trans('messages.sort_by_newest') }}</option>
                    <option value="oldest" @if($sort_by == 'oldest') selected="" @endif>{{ trans('messages.sort_by_oldest') }}</option>
                    <option value="smallest" @if($sort_by == 'smallest') selected="" @endif>{{ trans('messages.sort_by_smallest') }}</option>
                    <option value="largest" @if($sort_by == 'largest') selected="" @endif>{{ trans('messages.sort_by_largest') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" name="search" placeholder="{{ trans('messages.search_your_files') }}" value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ trans('messages.search') }}</button>
            </div>
        </div>
    </form>
    <div class="card-body">
    	<div class="row gutters-5">
			{{-- @php
				echo '<pre>';
					print_r($all_uploads[0]);
					die;
			@endphp --}}
			@if (isset($all_uploads[0]))
				@foreach($all_uploads as $key => $file)
					@php
						if($file->file_original_name == null){
							$file_name = trans('messages.unknown');
						}else{
							$file_name = $file->file_original_name;
						}
					@endphp
					<div class="col-auto w-140px w-lg-220px">
						<div class="aiz-file-box">
							<div class="dropdown-file" >
								<a class="dropdown-link" data-toggle="dropdown">
									<i class="la la-ellipsis-v"></i>
								</a>
								<div class="dropdown-menu dropdown-menu-right">
									<a href="javascript:void(0)" class="dropdown-item" onclick="detailsInfo(this)" data-id="{{ $file->id }}">
										<i class="las la-info-circle mr-2"></i>
										<span>{{ trans('messages.details_info') }}</span>
									</a>
									<a href="{{ storage_asset($file->file_name) }}" target="_blank" download="{{ $file_name }}.{{ $file->extension }}" class="dropdown-item">
										<i class="la la-download mr-2"></i>
										<span>{{ trans('messages.download') }}</span>
									</a>
									<a href="javascript:void(0)" class="dropdown-item" onclick="copyUrl(this)" data-url="{{ storage_asset($file->file_name) }}">
										<i class="las la-clipboard mr-2"></i>
										<span>{{ trans('messages.copy_link') }}</span>
									</a>
									<a href="javascript:void(0)" class="dropdown-item confirm-alert" data-href="{{ route('uploaded-files.destroy', $file->id ) }}" data-target="#delete-modal">
										<i class="las la-trash mr-2"></i>
										<span>{{ trans('messages.delete') }}</span>
									</a>
								</div>
							</div>
							<div class="card card-file aiz-uploader-select c-default" title="{{ $file_name }}.{{ $file->extension }}">
								<div class="card-file-thumb">
									@if($file->type == 'image')
										<img src="{{ storage_asset($file->file_name) }}" class="img-fit">
									@elseif($file->type == 'video')
										<i class="las la-file-video"></i>
									@else
										<i class="las la-file"></i>
									@endif
								</div>
								<div class="card-body">
									<h6 class="d-flex">
										<span class="text-truncate title">{{ $file_name }}</span>
										<span class="ext">.{{ $file->extension }}</span>
									</h6>
									<p>{{ formatBytes($file->file_size) }}</p>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			@else
				<div class="col-md-12 col-lg-12 col-xl-12">
					<div class="card card-file">
						<div class="card-status card-status-warning text-center">
							{{trans('messages.no_file_found')}}
						</div>
					</div>
				</div>
			@endif
    		
    	</div>
		<div class="aiz-pagination mt-3">
			{{ $all_uploads->appends(request()->input())->links('pagination::bootstrap-5') }}
		</div>
    </div>
</div>
@endsection
@section('modal')
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ trans('messages.delete_confirmation') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{ trans('messages.are_you_sure_delete') }}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
                <a href="" class="btn btn-primary mt-2 comfirm-link">{{ trans('messages.delete') }}</a>
            </div>
        </div>
    </div>
</div>
<div id="info-modal" class="modal fade">
	<div class="modal-dialog modal-dialog-right">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title h6">{{ trans('messages.file_info') }}</h5>
				<button type="button" class="close" data-dismiss="modal">
				</button>
			</div>
			<div class="modal-body c-scrollbar-light position-relative" id="info-modal-content">
				<div class="c-preloader text-center absolute-center">
                    <i class="las la-spinner la-spin la-3x opacity-70"></i>
                </div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('script')
	<script type="text/javascript">
		function detailsInfo(e){
            $('#info-modal-content').html('<div class="c-preloader text-center absolute-center"><i class="las la-spinner la-spin la-3x opacity-70"></i></div>');
			var id = $(e).data('id')
			$('#info-modal').modal('show');
			$.post('{{ route('uploaded-files.info') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#info-modal-content').html(data);
				// console.log(data);
			});
		}
		function copyUrl(e) {
			var url = $(e).data('url');
			var $temp = $("<input>");
		    $("body").append($temp);
		    $temp.val(url).select();
		    try {
			    document.execCommand("copy");
			    AIZ.plugins.notify('success', '{{ trans('messages.link_copied_to_clipboard') }}');
			} catch (err) {
			    AIZ.plugins.notify('danger', '{{ trans('messages.oops_unable_to_copy') }}');
			}
		    $temp.remove();
		}
        function sort_uploads(el){
            $('#sort_uploads').submit();
        }
	</script>
@endsection