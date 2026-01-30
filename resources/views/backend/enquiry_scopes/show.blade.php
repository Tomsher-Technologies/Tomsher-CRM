@extends('backend.layouts.app', ['title' => 'Enquiry Scope Of Work'])

@section('content')


<div class="container-fluid">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ================= CURRENT SCOPE ================= --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mt-3">
                <strong>{{ $scope->title }}</strong>
                <span class="ml-1 badge badge-{{ $scope->status == 'open' ? 'warning' : ($scope->status == 'responded' ? 'info' : 'success') }}"  style="min-width: 70px; text-align:center;height:25px">
                    {{ ucfirst($scope->status) }}
                </span>
            </h5>

            {{-- <span class="badge badge-{{ 
                $scope->status == 'open' ? 'warning' :
                ($scope->status == 'responded' ? 'info' : 'success')
            }}"  style="min-width: 65px; text-align:center;">
                {{ ucfirst($scope->status) }}
            </span> --}}

            <a href="{{ Session::has('enquiry_scopes_last_url') ? Session::get('enquiry_scopes_last_url') : route('enquiry-scopes.index') }}" class="btn btn-primary btn-sm"><i class="las la-arrow-left fs-16" style="margin-top: 2px;"></i> Go Back</a>
        </div>

        <div class="card-body">
            <h6 class="font-weight-bold text-primary">Scope Content</h6>
            <div class="border p-3 rounded" style="max-height:500px; overflow:auto;">
                {!! $scope->scope_content !!}
            </div>

            <div class="mt-3">
                {{-- <h6 class="font-weight-bold text-primary">Sales Comment:</h6>
            
                <div class="border p-3 rounded">{!! $scope->sales_comment ?? '<i>No Comments</i>' !!}</div> --}}

                @can('edit_enquiry_scope_work')
                    @if($scope->status != 'closed')
                        <div class="d-flex justify-content-end mt-2">
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#editScopeModal">
                                Edit Scope Content
                            </button>
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    {{-- ================= EDIT SCOPE MODAL ================= --}}
    <div class="modal fade" id="editScopeModal">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('enquiry-scopes.update', $scope->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="text-primary">Edit Scope Content</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <textarea name="scope_content" class="aiz-text-editor form-control"  data-min-height="400">{{ old('scope_content', $scope->scope_content) }}</textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success btn-sm">Save Changes</button>
                        <button type="button" class="btn btn-danger btn-sm" class="close" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= ADD COMMENT ================= --}}
    @can('edit_enquiry_scope_work')
        @if($scope->status != 'closed')
            <div class="card mb-3">
                <div class="card-header"><h6 class="font-weight-bold text-primary">Add Comment</h6></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enquiry-scopes.comments.store', $scope) }}">
                        @csrf
                        <textarea name="comment" class="form-control mb-2" rows="3" required></textarea>
                        <button class="btn btn-primary btn-sm">Add Comment</button>
                    </form>
                </div>
            </div>
        @endif
    @endcan

    {{-- ================= COMMENT HISTORY ================= --}}
    <div class="card mb-3">
        <div class="card-header"><h6 class="font-weight-bold text-primary">Comment History</h6></div>

        <div class="card-body" style="max-height:400px; overflow:auto;">
            @forelse($scope->comments as $comment)
                <div class="border-left pl-3 mb-3">
                    <span class="fs-14"><strong>{{ $comment->commenter->name }}</strong></span>
                    <span class="text-muted ml-1">
                        {{ $comment->created_at->format('d M Y h:i A') }}
                    </span>
                    <p class="mb-0 mt-1">{!! nl2br($comment->comment) !!}</p>
                </div>
            @empty
                <p class="text-muted">No comments yet.</p>
            @endforelse
        </div>
    </div>

    {{-- ================= SCOPE HISTORY ================= --}}
    <div class="card">
        <div class="card-header"><h6 class="font-weight-bold text-primary">Scope Edit History</h6></div>

        <div class="card-body">
            <div class="accordion" id="scopeHistory">

                @forelse($scope->histories as $key => $history)
                    <div class="card">
                        <div class="card-header p-2">
                            <button class="btn btn-link" data-toggle="collapse"
                                data-target="#history{{ $key }}">
                                Edited by {{ $history->editor->name }}
                                ({{ $history->created_at->format('d M Y h:i A') }})
                            </button>
                        </div>

                        <div id="history{{ $key }}" class="collapse">
                            <div class="card-body">
                                <div class="border p-2" style="max-height:500px; overflow:auto;">
                                    {!! $history->scope_content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No edit history found.</p>
                @endforelse

            </div>
        </div>
    </div>

</div>

@endsection
