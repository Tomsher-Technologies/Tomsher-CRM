@extends('backend.layouts.app',['title' => 'Industries'])

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Industries</h5>
        @can('add_industry')
            <button class="btn btn-success" data-toggle="modal" data-target="#industryModal">Add Industry</button>
        @endcan
    </div>
    <div class="card-body">

        <form class="row" id="sort_brands" action="" method="GET">
            <div class="col-md-4 input-group mb-1">
                <input type="text" class="form-control  form-control-sm" id="search" name="search"@isset($search) value="{{ $search }}" @endisset placeholder="{{ trans('messages.type_name_enter') }}">
            </div>

            <div class="col-sm-3 mb-1">
                <select class="form-control form-control-sm" id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="1" @if ($status == '1') selected @endif>Active</option>
                    <option value="2" @if ($status == '0') selected @endif>Inactive</option>
                </select>
            </div>

            <div class="col-sm-3 mb-1">
                <select id="industry_id" name="industry_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                    <option value="">Select Main Industry --</option>
                    @foreach($parentIndustries as $ind)
                        <option value="{{ $ind->id }}" {{ ($industry_id == $ind->id) ? 'selected' : '' }}>{{ $ind->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 mb-1">
                <button class="btn btn-info " type="submit">Filter</button>
                <a href="{{ route('industries.index') }}" class="btn btn-cancel">Reset</a>
            </div>
        </form>

        <table class="table aiz-table table-bordered mb-0">
            <thead>
                <tr>
                    <th  class="text-center">#</th>
                    <th>{{trans('messages.name')}}</th>
                    <th>Main Industry</th>
                    <th class="text-center">{{trans('messages.status')}}</th>
                    <th class="text-center">{{trans('messages.options')}}</th>
                </tr>
            </thead>
            <tbody>
                @can('view_industry')
                    @foreach($industries as $key => $industry)
                        <tr>
                            <td class="text-center">{{ ($key+1) + ($industries->currentPage() - 1)*$industries->perPage() }}</td>
                            <td>{{ $industry->name }}</td>
                            <td>
                                @if($industry->parent_id)
                                    {{ $industry->parent->name }} 
                                @else
                                    <strong>-</strong>
                                @endif
                            </td>
                            <td class="text-center">
                                @can('edit_industry')
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" onchange="update_status(this)" value="{{ $industry->id }}"
                                            <?php if ($industry->status == 1) {
                                                echo 'checked';
                                            } ?>>
                                        <span></span>
                                    </label>
                                @endcan
                            </td>
                            <td class="text-center">
                                @can('edit_industry')
                                    <button class="btn btn-soft-success btn-sm btn-icon btn-circle editBtn" id="" data-id="{{ $industry->id }}" data-name="{{ $industry->name }}" data-parent="{{ $industry->parent_id }}">
                                        <i class="las la-edit"></i>
                                    </button>
                                @endcan
                                {{-- <a href="#" class="btn btn-soft-danger btn-sm btn-icon btn-circle confirm-delete" data-href="{{route('industries.destroy', $industry->id)}}" title="{{ trans('messages.delete') }}">
                                    <i class="las la-trash"></i>
                                </a> --}}
                            </td>
                        </tr>
                    @endforeach
                @endcan
            </tbody>
        </table>
        <div class="aiz-pagination">
            @can('view_industry')
                {{ $industries->appends(request()->input())->links('pagination::bootstrap-5') }}
            @endcan
        </div>
    </div>
</div>

    <!-- Add/Edit Industry Modal -->
    <div class="modal fade" id="industryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Industry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="industryForm">
                        @csrf
                        <div class="form-group">
                            <label for="name">Industry Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="name" placeholder="Enter industry name" >
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Parent Industry</label>
                            <select id="parent_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                                <option value="">-- Main Industry --</option>
                                @foreach($parentIndustries as $ind)
                                    <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="industryId">
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
            // Open modal for adding new industry
         
            $(document).on("click", ".editBtn", function() {
                $('#errorShow').html('');
                let id = $(this).data("id");
                let name = $(this).data("name");
                let parent = $(this).data("parent");
               
                $("#industryId").val(id);
                $("#name").val(name);
                $('#parent_id').val(parent).selectpicker('refresh');
                $("#modalTitle").text("Edit Industry");
                $("#saveBtn").text("Update");
                $('#industryModal').modal('show');
            });

            $(document).on("click", ".btn-success", function() {
                $("#industryId").val('');
                $("#name").val('');
                $("#modalTitle").text("Add Industry");
                $('#errorShow').html('');
                $("#saveBtn").text("Save");
            });

            // Handle form submission
            $("#industryForm").submit(function(e) {
                e.preventDefault();
                $('#errorShow').html('');

                let id = $("#industryId").val();
                let url = id ? "/industries/update/"+id : "{{ route('industries.store') }}";
                let method = id ? "POST" : "POST";
                let name = $("#name").val();

                if(name != null && name != ''){
                    $.ajax({
                        url: url,
                        type: method,
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: $("#name").val(),
                            parent_id: $("#parent_id").val(),
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
            $.post('{{ route('industries.status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', 'Industry status updated successfully');
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