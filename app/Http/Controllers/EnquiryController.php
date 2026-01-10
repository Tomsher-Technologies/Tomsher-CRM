<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Customer;
use App\Models\EnquirySource;
use App\Models\ProjectType;
use App\Models\User;
use App\Models\EnquiryStatusHistory;
use App\Models\EnquiryTransferHistory;
use App\Models\EnquiryFollowup;
use App\Models\Project;
use App\Models\EnquiryProposalItem;
use App\Models\EnquiryScopeOfWork;
use App\Models\EnquiryScopeHistory;
use App\Models\EnquiryScopeComment;
use App\Models\SalespersonAssignment;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_enquiries',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_enquiries',  ['only' => ['show']]);
        $this->middleware('permission:add_enquiries',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_enquiries',  ['only' => ['edit','update','changeStatus']]);
    }

    public function index(Request $request)
    {
        $request->session()->put('enquiries_last_url', url()->full());
        $request->session()->put('previous_section', 'enquiry');
        $request->session()->put('enquiry_scopes_last_url', url()->full());

        $customers = Customer::orderBy('company_name', 'asc')->get();
        $sources = EnquirySource::orderBy('name', 'asc')->get();
        $projectTypes = ProjectType::orderBy('name', 'asc')->get();
        $users = User::orderBy('name', 'asc')->get();

        $date = $request->enquiry_date;

        $query = Enquiry::with(['customer', 'source', 'projectTypes']);
        
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
    
        if ($request->filled('enquiry_source_id')) {
            $query->where('enquiry_source_id', $request->enquiry_source_id);
        }

        if ($request->filled('source_mode')) {
            $query->where('source_mode', $request->source_mode);
        }
    
        if ($request->filled('project_type_id')) {
            $query->whereHas('projectTypes', function ($q) use ($request) {
                $q->where('project_type_id', $request->project_type_id);
            });
        }

        if ($request->filled('enquiry_code')) {
            $query->where('enquiry_code', $request->enquiry_code);
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        if ($request->filled('added_by')) {
            $query->where('owner_id', $request->added_by);
        }
        
        if ($date != null) {
            $query->whereDate('enquiry_date', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('enquiry_date', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $updated_date = $request->last_updated_date ?? '';
        if ($updated_date != null) {
            $query->whereDate('updated_at', '>=', date('Y-m-d', strtotime(explode(" to ", $updated_date)[0])))->whereDate('updated_at', '<=', date('Y-m-d', strtotime(explode(" to ", $updated_date)[1])));
        }

        if (!auth()->user()->can('view_all_users_enquiries')) {
            $query->where('owner_id', auth()->user()->id);
        } 

        $sortBy = $request->get('sort_by');

        switch ($sortBy) {
            case 'updated_asc':
                $query->orderBy('updated_at', 'asc');
                break;

            case 'updated_desc':
                $query->orderBy('updated_at', 'desc');
                break;

            case 'enquiry_asc':
                $query->orderBy('enquiry_date', 'asc');
                break;

            case 'enquiry_desc':
                $query->orderBy('enquiry_date', 'desc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $enquiries = $query->paginate(30);


        return view('backend.enquiries.index', compact('enquiries','customers', 'sources', 'projectTypes','users'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->customer_id ?? '';
        $customers = Customer::where('is_active', 1)->orderBy('company_name', 'asc')->get();
        $sources = EnquirySource::where('status', 1)->orderBy('name', 'asc')->get();
        $projectTypes = ProjectType::where('status', 1)->orderBy('name', 'asc')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        return view('backend.enquiries.create', compact('customers', 'sources', 'projectTypes','users','customer_id'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'enquiry_date' => 'required|date',
            'enquiry_source_id' => 'required|exists:enquiry_sources,id',
            'project_details' => 'required|string',
            'source_mode' => 'required',
            'status' => 'nullable|in:new_enquiry,started_discussion,proposal_submitted,project_approved,project_rejected,not_interested,not_responding,invalid_spam,ongoing_discussion,preparing_scope',
            'comments' => 'nullable|string'
        ]);

        $validated['added_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['owner_id'] = $request->user_id ?? auth()->id();

        unset($validated['project_type_id']);

        $customer = Customer::findOrFail($request->customer_id);

        // Fetch all existing enquiry codes for the customer
        $existingCodes = Enquiry::where('customer_id', $customer->id)
                        ->pluck('enquiry_code')
                        ->toArray();
    
        // Extract existing numbers
        $existingNumbers = [];
        foreach ($existingCodes as $code) {
            $parts = explode('-', $code);
            if (count($parts) == 2 && is_numeric($parts[1])) {
                $existingNumbers[] = (int) $parts[1];
            }
        }
    
        // Find the next available number
        $nextNumber = 1;
        if (!empty($existingNumbers)) {
            $nextNumber = max($existingNumbers) + 1;
        }
    
        // Format the number (e.g., 001)
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
        $enquiryCode = $customer->customer_code . '-' . $formattedNumber;

        $validated['enquiry_code'] = $enquiryCode;

        $enquiry = Enquiry::create($validated);
        if (!empty($request->project_type_id)) {
            $enquiry->projectTypes()->sync($request->project_type_id);
        }

        // Save status change as timeline activity
        $enquiry->statusHistories()->create([
            'status' => 'new_enquiry',
            'status_date' => $request->enquiry_date,
            'comment' => NULL,
            'submitted_cost' => 0,
            'approved_cost' => 0,
            'changed_by' => auth()->id(),
        ]);

        EnquiryTransferHistory::create([
            'enquiry_id' => $enquiry->id,
            'transferred_by' => NULL,
            'transferred_to' => $request->user_id ?? auth()->id(),
        ]);

        if ($customer->sales_person != $enquiry->owner_id) {
            // Log change
            \App\Models\SalespersonAssignment::create([
                'customer_id' => $customer->id,
                'old_sales_person_id' => $customer->sales_person,
                'new_sales_person_id' => $enquiry->owner_id,
                'enquiry_id' => $enquiry->id ?? null,
                'changed_by' => auth()->id(),
            ]);
        
            // Update customer
            $customer->sales_person = $enquiry->owner_id;
            $customer->save();
        }

        flash('Enquiry added successfully.')->success();
        return redirect()->route('enquiries.index');
    }

    public function edit(Enquiry $enquiry)
    {
        $customers = Customer::where('is_active', 1)->orderBy('company_name', 'asc')->get();
        $sources = EnquirySource::where('status', 1)->orderBy('name', 'asc')->get();
        $projectTypes = ProjectType::where('status', 1)->orderBy('name', 'asc')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        return view('backend.enquiries.edit', compact('enquiry', 'customers', 'sources', 'projectTypes','users'));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'enquiry_date' => 'nullable|date',
            'enquiry_source_id' => 'required|exists:enquiry_sources,id',
            'source_mode' => 'required',
            'project_details' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['updated_at'] = date('Y-m-d H:i:s');

        unset($validated['project_type_id']);
        unset($validated['user_id']);

        $enquiry->update($validated);
        $enquiry->projectTypes()->sync($request->project_type_id);

        if($request->has('user_id') && $enquiry->owner_id != $request->user_id){
            $enquiry->owner_id = $request->user_id;
            $enquiry->save();
    
            EnquiryTransferHistory::create([
                'enquiry_id' => $enquiry->id,
                'transferred_by' => auth()->id(),
                'transferred_to' => $request->user_id,
            ]);
        }

        $customer = Customer::findOrFail($request->customer_id);
        if ($customer && $customer->sales_person !== $enquiry->owner_id ) {
            // Log change
            \App\Models\SalespersonAssignment::create([
                'customer_id' => $customer->id,
                'old_sales_person_id' => $customer->sales_person,
                'new_sales_person_id' => $enquiry->owner_id,
                'enquiry_id' => $enquiry->id ?? null,
                'changed_by' => auth()->id(),
            ]);
        
            // Update customer
            $customer->sales_person = $enquiry->owner_id;
            $customer->save();
        }
       
        flash('Enquiry updated successfully.')->success();

        $previous = $request->session()->get('previous_section');

        if($previous === 'enquiry_view'){
            $route = $request->session()->get('enquiry_view_last_url');
            return redirect($route);
        }else{
            $route = $request->session()->get('enquiries_last_url');
            return redirect($route);
        }
    }

    public function show(Request $request, Enquiry $enquiry)
    {
        $request->session()->put('previous_section', 'enquiry_view');
        $request->session()->put('enquiry_view_last_url', url()->full());

        $enquiry = Enquiry::with(['followups' => function($query) {
            $query->orderBy('followup_time', 'asc');
        }])->findOrFail($enquiry->id);
        return view('backend.enquiries.show', compact('enquiry'));
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        return redirect()->route('enquiries.index')->with('success', 'Enquiry deleted.');
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'status' => 'required|in:new_enquiry,started_discussion,proposal_submitted,project_approved,project_rejected,not_interested,not_responding,invalid_spam,signed_payment_pending,ongoing_discussion,preparing_scope',
            'status_date' => 'required|date',
            'comment' => 'nullable|string',
            // 'submitted_cost' => 'required_if:status,proposal_submitted|nullable|numeric',
            'approved_cost' => 'required_if:status,project_approved|nullable|numeric',

            'scope_title' => 'required_if:status,preparing_scope|nullable|string|max:255',
            'scope_content' => 'required_if:status,preparing_scope|nullable|string',
        ]);

        $enquiry = Enquiry::findOrFail($request->enquiry_id);

        if($enquiry->status == 'preparing_scope' && $request->status != 'preparing_scope'){
            $scope = EnquiryScopeOfWork::where('enquiry_id', $enquiry->id)->first();
            if($scope){
                $scope->update([
                    'status' => 'closed'
                ]);
            }
        }
        $enquiry->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($request->status == 'project_approved'){
            $enquiry->approved_cost = $request->approved_cost ?? 0;
            $enquiry->save();
        }
        // Save status change as timeline activity
        $statusHistory =  $enquiry->statusHistories()->create([
                                'status' => $request->status,
                                'status_date' => $request->status_date,
                                'comment' => $request->comment,
                                'submitted_cost' => $request->submitted_cost ?? 0,
                                'approved_cost' => $request->approved_cost ?? 0,
                                'changed_by' => auth()->id(),
                            ]);

        if ($request->status == 'proposal_submitted') {
            $enquiry->proposalItems()->update(['status' => 0]);
            // Save the new proposal items
            if ($request->proposal_items) {
                foreach ($request->proposal_items as $proposalItemData) {
                    if (!empty($proposalItemData['title'])) {
                        $proposalItemData['status_history_id'] = $statusHistory->id;
                        $enquiry->proposalItems()->create($proposalItemData);
                    }
                }
            }
        }        
                    
        if ($request->status == 'project_approved' ) {

            if($request->has('selected_proposal_item_id') && $request->selected_proposal_item_id !=''){
                $enquiry->proposalItems()->update(['selected' => 0]);
                $item = EnquiryProposalItem::find($request->selected_proposal_item_id);
                $item->selected = 1;
                $item->save();
            }
            if(!$enquiry->project_created){
                // Create Project
                $project = new Project();
                $project->enquiry_id = $enquiry->id;
                $project->customer_id = $enquiry->customer_id;
                $project->project_name = $enquiry->customer->company_name . ' - ' . optional($enquiry->projectTypes->first())->name;
                $project->status = 'pending';
                $project->project_total_cost = $enquiry->approved_cost ?? 0;
                $project->created_by = auth()->user()->id;
                $project->pending_amount = $enquiry->approved_cost ?? 0;
                $project->save();
            }else{
                $project = Project::where('enquiry_id', $enquiry->id)->first();
                $project->project_total_cost = $enquiry->approved_cost ?? 0;
                $project->updated_by = auth()->user()->id;
                $project->save();
            }
           
    
            // Update enquiry
            $enquiry->project_created = true;
            $enquiry->save();
        }

        if ($request->status === 'preparing_scope') {

            $scope = EnquiryScopeOfWork::where('enquiry_id', $enquiry->id)->first();

            if ($scope) {
                if ($scope->scope_content !== $request->scope_content) {
                    EnquiryScopeHistory::create([
                        'scope_of_work_id' => $scope->id,
                        'scope_content' => $request->scope_content,
                        'edited_by' => auth()->id(),
                    ]);
                }

                if($scope->sales_comment != $request->comment){
                    $scope->comments()->create([
                        'comment'    => $request->comment,
                        'is_sales_team' => 1,
                        'commented_by' => auth()->id(),
                    ]);
                }

                $scope->update([
                    'title' => $request->scope_title,
                    'status' => 'open',
                    'scope_content' => $request->scope_content,
                    'sales_comment' => $request->comment,
                    'updated_by' => auth()->id(),
                ]);
            }else {
                $scope = EnquiryScopeOfWork::create([
                    'enquiry_id' => $enquiry->id,
                    'title' => $request->scope_title,
                    'scope_content' => $request->scope_content,
                    'sales_comment' => $request->comment,
                    'created_by' => auth()->id(),
                ]);

                $scope->comments()->create([
                    'comment'    => $request->comment,
                    'is_sales_team' => 1,
                    'commented_by' => auth()->id(),
                ]);
            }
        }

        flash('Enquiry status updated successfully.')->success();
        return response()->json(['success' => true]);
    }

    public function createEnquiryFollowup($enquiryId)
    {
        $enquiry = Enquiry::findOrFail($enquiryId);
        return view('followups.create', compact('enquiry'));
    }

    public function getProposalItems($id, $status)
    {
        $enquiry = [];
        if($status == 'proposal_submitted' || $status == 'project_approved'){
            $enquiry = Enquiry::with(['proposalItems' => function ($query) {
                $query->where('status', 1);  // Filter proposal items where status is 1
             }])->findOrFail($id);
        }

        if($status == 'preparing_scope'){
            $scope = EnquiryScopeOfWork::where('enquiry_id', $id)->first();
        }
        
        $enquiryStatusData = EnquiryStatusHistory::where('enquiry_id', $id)->where('status', $status)->orderBy('id','desc')->first();
        return response()->json([
            'proposal_items' => $enquiry->proposalItems ?? [],
            'comment' => $enquiryStatusData->comment ?? '',
            'status_date' => $enquiryStatusData->status_date ?? date('Y-m-d'),
            'status' => $status,
            'approved_cost' => $enquiryStatusData->approved_cost ?? 0,
            'scope_title' => $scope->title ?? '',
            'scope_content' => $scope->scope_content ?? ''
        ]);
    }

}
