@extends('backend.layouts.app',['title' => 'Technologies'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Technologies</h5>
        @can('add_technology')
            <button class="btn btn-success" data-toggle="modal" data-target="#technologyModal">Add Technology</button>
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
                <a href="{{ route('technologies.index') }}" class="btn btn-cancel">Reset</a>
            </div>
        </form>
        <table class="table table-bordered aiz-table mb-0">
            <thead>
                <tr>
                    <th  class="text-center">#</th>
                    <th>{{trans('messages.name')}}</th>
                    <th class="text-center">{{trans('messages.status')}}</th>
                    <th class="text-center">{{trans('messages.options')}}</th>
                </tr>
            </thead>
            <tbody>
                @can('view_technologies')
                    @foreach($technologies as $key => $technology)
                        <tr>
                            <td class="text-center">{{ ($key+1) + ($technologies->currentPage() - 1)*$technologies->perPage() }}</td>
                            <td>{{ $technology->name }}</td>
                            <td class="text-center">
                                @can('edit_technology')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_status(this)" value="{{ $technology->id }}"
                                            <?php if ($technology->status == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <span></span>
                                    </label>
                                @endcan
                            </td>
                            <td class="text-center">
                                @can('edit_technology')
                                    <button class="btn btn-soft-success btn-sm btn-icon btn-circle editBtn" id="" data-id="{{ $technology->id }}" data-name="{{ $technology->name }}">
                                        <i class="las la-edit"></i>
                                    </button>
                                @endcan
                                {{-- <a href="#" class="btn btn-soft-danger btn-sm btn-icon btn-circle confirm-delete" data-href="{{route('technologies.destroy', $technology->id)}}" title="{{ trans('messages.delete') }}">
                                    <i class="las la-trash"></i>
                                </a> --}}
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination mt-1">
            @can('view_technologies')
                {{ $technologies->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
        </div>
    </div>
</div>

    <!-- Add/Edit Technology Modal -->
    <div class="modal fade" id="technologyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Technology</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="technologyForm">
                        @csrf
                        <label>Technology Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="name" placeholder="Enter technology name" >
                        <input type="hidden" id="technologyId">
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
            // Open modal for adding new technology
         
            $(document).on("click", ".editBtn", function() {
                $('#errorShow').html('');
                let id = $(this).data("id");
                let name = $(this).data("name");
               
                $("#technologyId").val(id);
                $("#name").val(name);
                $("#modalTitle").text("Edit Technology");
                $("#saveBtn").text("Update");
                $('#technologyModal').modal('show');
            });

            $(document).on("click", ".btn-success", function() {
                $("#technologyId").val('');
                $("#name").val('');
                $("#modalTitle").text("Add Technology");
                $('#errorShow').html('');
                $("#saveBtn").text("Save");
            });

            // Handle form submission
            $("#technologyForm").submit(function(e) {
                e.preventDefault();
                $('#errorShow').html('');

                let id = $("#technologyId").val();
                let url = id ? "/technologies/update/"+id : "{{ route('technologies.store') }}";
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
            $.post('{{ route('technologies.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', 'Technology status updated successfully');
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