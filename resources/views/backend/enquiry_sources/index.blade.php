@extends('backend.layouts.app',['title' => 'Enquiry Sources'])

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 ">All Enquiry Sources</h5>
        @can('add_enquiry_source')
            <button class="btn btn-success" data-toggle="modal" data-target="#enquiry_sourceModal">Add Enquiry Source</button>
        @endcan
    </div>
    <div class="card-body">
        <form class="row" id="sort_brands" action="" method="GET">
            <div class="col-md-4 input-group mb-1">
                <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($search) value="{{ $search }}" @endisset placeholder="{{ trans('messages.type_name_enter') }}">
            </div>

            <div class="col-sm-4 mb-1">
                <select class="form-control form-control-sm" id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="1" @if ($status == '1') selected @endif>Active</option>
                    <option value="2" @if ($status == '0') selected @endif>Inactive</option>
                </select>
            </div>

            <div class="col-md-4 mb-1">
                <button class="btn btn-info " type="submit">Filter</button>
                <a href="{{ route('enquiry_sources.index') }}" class="btn btn-cancel">Reset</a>
            </div>
        </form>
        <table class="table aiz-table table-bordered mb-0">
            <thead>
                <tr>
                    <th  class="text-center">#</th>
                    <th>{{trans('messages.name')}}</th>
                    <th class="text-center">{{trans('messages.status')}}</th>
                    <th class="text-center">{{trans('messages.options')}}</th>
                </tr>
            </thead>
            <tbody>
                @can('view_enquiry_source')
                    @foreach($enquiry_sources as $key => $enquiry_source)
                        <tr>
                            <td class="text-center">{{ ($key+1) + ($enquiry_sources->currentPage() - 1)*$enquiry_sources->perPage() }}</td>
                            <td>{{ $enquiry_source->name }}</td>
                            <td class="text-center">
                                @can('edit_enquiry_source')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_status(this)" value="{{ $enquiry_source->id }}"
                                            <?php if ($enquiry_source->status == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <span></span>
                                    </label>
                                @endcan
                            </td>
                            <td class="text-center">
                                @can('edit_enquiry_source')
                                    <button class="btn btn-soft-success btn-sm btn-icon btn-circle editBtn" id="" data-id="{{ $enquiry_source->id }}" data-name="{{ $enquiry_source->name }}">
                                        <i class="las la-edit"></i>
                                    </button>
                                @endcan
                                {{-- <a href="#" class="btn btn-soft-danger btn-sm btn-icon btn-circle confirm-delete" data-href="{{route('enquiry_sources.destroy', $enquiry_source->id)}}" title="{{ trans('messages.delete') }}">
                                    <i class="las la-trash"></i>
                                </a> --}}
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination">
            @can('view_enquiry_source')
                {{ $enquiry_sources->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
        </div>
    </div>
</div>

    <!-- Add/Edit Enquiry Source Modal -->
    <div class="modal fade" id="enquiry_sourceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Enquiry Source</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="enquiry_sourceForm">
                        @csrf
                        <label>Enquiry Source Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" placeholder="Enter enquiry source name" >
                        <input type="hidden" id="enquiry_sourceId">
                        <span id="errorShow" class="mt-1 error" style="color: red;"></span><br>
                        <button type="submit" class="btn btn-primary mt-3" id="saveBtn">Save</button>
                        <button type="button" class="btn btn-secondary mt-3" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
       
       $(document).ready(function() {
            // Open modal for adding new enquiry_source
         
            $(document).on("click", ".editBtn", function() {
                $('#errorShow').html('');
                let id = $(this).data("id");
                let name = $(this).data("name");
               
                $("#enquiry_sourceId").val(id);
                $("#name").val(name);
                $("#modalTitle").text("Edit Enquiry Source");
                $("#saveBtn").text("Update");
                $('#enquiry_sourceModal').modal('show');
            });

            $(document).on("click", ".btn-success", function() {
                $("#enquiry_sourceId").val('');
                $("#name").val('');
                $("#modalTitle").text("Add Enquiry Source");
                $('#errorShow').html('');
                $("#saveBtn").text("Save");
            });

            // Handle form submission
            $("#enquiry_sourceForm").submit(function(e) {
                e.preventDefault();
                $('#errorShow').html('');

                let id = $("#enquiry_sourceId").val();
                let url = id ? "/enquiry-sources/update/"+id : "{{ route('enquiry_sources.store') }}";
                let method = id ? "POST" : "POST";
                let name = $("#name").val();

                if(name != null && name != ''){
                    $.ajax({
                        url: url,
                        type: method,
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: $("#name").val()
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            $('#errorShow').html(xhr.responseJSON.errors.name[0]);
                        }
                    });
                }else{
                    $('#errorShow').html('The name field is required.');
                }
            });

        });
        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('enquiry_sources.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', 'Enquiry source status updated successfully');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);

                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }
            });
        }
    </script>
@endsection