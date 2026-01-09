<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnquiryScopeOfWork;
use App\Models\EnquiryScopeComment;
use App\Models\EnquiryScopeHistory;
use App\Models\User;
use App\Models\ProjectType;
use App\Models\EnquirySource;

class EnquiryScopeOfWorkController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_enquiry_scope_work',  ['only' => ['index']]);
        $this->middleware('permission:view_enquiry_scope_work',  ['only' => ['show']]);
        $this->middleware('permission:edit_enquiry_scope_work',  ['only' => ['update','storeComment']]);
    }

    public function index(Request $request)
    {
        $request->session()->put('enquiry_scopes_last_url', url()->full());

        $users = User::orderBy('name', 'asc')->get();
        $projectTypes = ProjectType::orderBy('name', 'asc')->get();

        $scopes = EnquiryScopeOfWork::with([
                                        'enquiry.customer',
                                        'enquiry.projectTypes',
                                        'enquiry.owner'
                                    ])
                                    ->when($request->filled('keyword'), function ($q) use ($request) {
                                        $keyword = $request->keyword;
                                        $q->where(function ($query) use ($keyword) {
                                            $query->where('title', 'like', "%{$keyword}%")
                                                ->orWhereHas('enquiry', function ($enquiry) use ($keyword) {
                                                    $enquiry->where('enquiry_code', 'like', "%{$keyword}%")
                                                        ->orWhereHas('customer', function ($customer) use ($keyword) {
                                                            $customer->where('company_name', 'like', "%{$keyword}%");
                                                        });
                                                });
                                        });
                                    })
                                    ->when($request->filled('project_type_id') || $request->filled('added_by'), function ($q) use ($request) {
                                        $q->whereHas('enquiry', function ($query) use ($request) {

                                            if ($request->filled('project_type_id')) {
                                                $query->whereHas('projectTypes', function ($p) use ($request) {
                                                    $p->where('project_type_id', $request->project_type_id);
                                                });
                                            }

                                            if ($request->filled('added_by')) {
                                                $query->where('owner_id', $request->added_by);
                                            }

                                        });
                                    })
                                    ->when($request->filled('status'), function ($q) use ($request) {
                                        $q->where('status', $request->status);
                                    })
                                    ->latest()
                                    ->paginate(20);

        return view('backend.enquiry_scopes.index', compact('scopes', 'users', 'projectTypes'));
    }

    public function show($id)
    {
        $scope = EnquiryScopeOfWork::with([
                                        'enquiry.customer',
                                        'enquiry.owner',
                                        'histories.editor',
                                        'comments.commenter'
                                    ])->findOrFail($id);

        return view('backend.enquiry_scopes.show', compact('scope'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scope_content' => 'required|string',
        ]);

        $scope = EnquiryScopeOfWork::findOrFail($id);

        // Only save history if content has changed
        if ($scope->scope_content !== $request->scope_content) {

            // Save old HTML content to history
            EnquiryScopeHistory::create([
                'scope_of_work_id' => $scope->id,
                'scope_content'    => $scope->scope_content, // HTML preserved
                'edited_by'        => auth()->id(),
            ]);

            // Update scope with new HTML content
            $scope->update([
                'scope_content' => $request->scope_content, // HTML preserved
                'updated_by'    => auth()->id(),
            ]);
        }

        return redirect()
            ->route('enquiry-scopes.show', $scope->id)
            ->with('success', 'Scope content updated successfully.');
    }

    public function storeComment(Request $request, EnquiryScopeOfWork $scope)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
    
        $scope->comments()->create([
            'comment'    => $request->comment,
            'commented_by' => auth()->id(),
        ]);

        // Update scope with new comment
        $scope->update([
            'scope_comment' => $request->comment, 
            'updated_by'    => auth()->id(),
            'status' => ($scope->status != 'closed') ? 'responded' : $scope->status
        ]);

        return back()->with('success', 'Comment added');
    }

}
