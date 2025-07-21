@extends('backend.layouts.app',['title' => 'Project Categories'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Project Categories</h5>
        @can('add_project_type')
            <button class="btn btn-success" data-toggle="modal" data-target="#categoryModal">Add Project Category</button>
        @endcan
    
    </div>
    <div class="card-body">
        <form class="row" id="sort_brands" action="" method="GET">
            <div class="col-md-4 input-group  mb-1">
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
                <a href="{{ route('project_category.index') }}" class="btn btn-cancel">Reset</a>
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
                @can('view_project_type')
                    @foreach($project_types as $key => $project_type)
                        <tr>
                            <td class="text-center">{{ ($key+1) + ($project_types->currentPage() - 1)*$project_types->perPage() }}</td>
                            <td>{{ $project_type->name }}</td>
                            <td class="text-center">
                                @can('edit_project_type')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_status(this)" value="{{ $project_type->id }}"
                                            <?php if ($project_type->status == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <span></span>
                                    </label>
                                @endcan
                            </td>
                            <td class="text-center">
                                @can('edit_project_type')
                                    <button class="btn btn-soft-success btn-sm btn-icon btn-circle editBtn" id="" data-id="{{ $project_type->id }}" data-name="{{ $project_type->name }}">
                                        <i class="las la-edit"></i>
                                    </button>
                                @endcan
                                {{-- <a href="#" class="btn btn-soft-danger btn-sm btn-icon btn-circle confirm-delete" data-href="{{route('project_types.destroy', $project_type->id)}}" title="{{ trans('messages.delete') }}">
                                    <i class="las la-trash"></i>
                                </a> --}}
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination">
            @can('view_project_type')
                {{ $project_types->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
        </div>
    </div>
</div>

    <!-- Add/Edit Project Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Project Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        @csrf
                        <label>Project Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="name" placeholder="Enter project category name" >
                        <input type="hidden" id="project_typeId">
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
            // Open modal for adding new project_type
         
            $(document).on("click", ".editBtn", function() {
                $('#errorShow').html('');
                let id = $(this).data("id");
                let name = $(this).data("name");
               
                $("#project_typeId").val(id);
                $("#name").val(name);
                $("#modalTitle").text("Edit Project Category");
                $("#saveBtn").text("Update");
                $('#categoryModal').modal('show');
            });

            $(document).on("click", ".btn-success", function() {
                $("#project_typeId").val('');
                $("#name").val('');
                $("#modalTitle").text("Add Project Category");
                $('#errorShow').html('');
                $("#saveBtn").text("Save");
            });

            // Handle form submission
            $("#categoryForm").submit(function(e) {
                e.preventDefault();
                $('#errorShow').html('');

                let id = $("#project_typeId").val();
                let url = id ? "/project_category/update/"+id : "{{ route('project_category.store') }}";
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
            $.post('{{ route('project_category.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', 'Project category status updated successfully');
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