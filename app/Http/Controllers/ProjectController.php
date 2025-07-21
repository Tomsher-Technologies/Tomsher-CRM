<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Technologies;
use App\Models\ProjectStatusHistory;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->put('projects_last_url', url()->full());
        $customers = Customer::where('is_active', 1)->orderBy('company_name', 'asc')->get();

        $query = Project::with('customer');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('payment_status')) {
            if($request->payment_status == 'completed'){
                $query->where('pending_amount', 0);
            }else{
                $query->where('pending_amount', '!=', 0);
            } 
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('project_name', 'like', "%$keyword%")
                  ->orWhereHas('enquiry', function ($qu) use ($keyword) {
                    $qu->where('enquiry_code', 'like', "%$keyword%");
                });
            });
        }

        $projects = $query->orderBy('created_at','desc')->paginate(15);

        return view('backend.projects.index', compact('projects','customers'));
    }

    public function create()
    {
        $customers = Customer::where('is_active',1)->orderBy('company_name','asc')->get();
        $allTechnologies = Technologies::where('status',1)->orderBy('name','asc')->get();
        return view('backend.projects.create', compact( 'customers', 'allTechnologies'));
    }

    public function show($id)
    {
        $project = Project::with(['customer', 'payments'])->findOrFail($id);
        return view('backend.projects.show', compact('project'));
    }

    public function edit($id)
    {
        $project = Project::with('payments', 'customer', 'enquiry')->findOrFail($id);
        $customers = Customer::where('is_active',1)->orderBy('company_name','asc')->get();
        $enquiries = Enquiry::where('customer_id', $project->customer_id)->get(); // Only related enquiries
        $allTechnologies = Technologies::where('status',1)->orderBy('name','asc')->get();
        return view('backend.projects.edit', compact('project', 'customers', 'enquiries','allTechnologies'));
    }

    public function store(Request $request){
        $request->validate([
            'project_total_cost' => 'required|numeric|min:0',
            'payments.*.title' => 'nullable|string|max:255',
            'payments.*.expected_date' => 'nullable|date',
            'payments.*.amount' => 'nullable|numeric|min:0',
            'payments.*.percentage' => 'nullable|numeric|min:0',
            'payments.*.method' => 'nullable|string|max:255',
        ]);

        $validated=[
            'project_total_cost' => $request->project_total_cost,
            'project_name' => $request->project_name,
            'customer_id' => $request->customer_id,
            'enquiry_id' => $request->enquiry_id,
            'start_date' => $request->start_date,
            'internal_deadline' => $request->internal_deadline,
            'client_deadline' => $request->client_deadline,
            'status' => $request->status,
            'comment' => $request->comment,
            'project_type' => 1,
            'created_by' => auth()->user()->id
        ];

        $totalPaymentAmount = 0;
        if ($request->payments) {
            foreach($request->payments as $payment){
                $totalPaymentAmount += $payment['received_amount'] ?? 0;
            }
        }
      
        if($totalPaymentAmount > $request->project_total_cost){
            flash('Total Received Amount cannot be greater than Project Total Cost')->error();
            return back()->with('error', 'Total Received Amount cannot be greater than Project Total Cost')->withInput();
        }

        $project = Project::create($validated);

        $project->technologies()->sync($request->technology_ids ?? []);

        $total_paid_amount = 0;
        if ($request->payments) {
            foreach ($request->payments as $payment) {
                if (!empty($payment['title']) && (!empty($payment['amount']) || !empty($payment['percentage']))) {
                    $project->payments()->create([
                        'payment_title' => $payment['title'] ?? null,
                        'expected_date' => $payment['expected_date'] ?? null,
                        'amount' => $payment['amount'] ?? 0,
                        'percentage' => $payment['percentage'] ?? 0,
                        'method' => $payment['method'] ?? null,
                        'status' => $payment['status'] ?? null,
                        'received_date' => $payment['received_date'] ?? null,
                        'tax' => $payment['tax_amount'] ?? 0, 
                        'total_amount' => $payment['total_amount'] ?? 0, 
                        'received_amount' => $payment['received_amount'] ?? 0, 
                        'received_tax' => $payment['received_tax'] ?? 0,
                        'received_total_amount' => $payment['total_received'] ?? 0,
                    ]);

                    if($payment['status'] == 'received'){
                        $total_paid_amount = $total_paid_amount + $payment['received_amount'];
                    }
                }
            }
        }

        $project->paid_amount = $total_paid_amount;
        $project->pending_amount = ($request->project_total_cost + ($request->project_total_cost * 0.05)) - $total_paid_amount; // 5% tax included
        $project->save();

        // Check if status has changed
        if ('pending' != $project->status) {
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'old_status' => 'pending',
                'new_status' => $project->status,
                'changed_by' => auth()->id(), // Assuming admin/user is logged in
                'changed_at' => now(),
            ]);
        }

        flash('Project created successfully.')->success();
        return redirect()->route('projects.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'project_total_cost' => 'required|numeric|min:0',
            'payments.*.title' => 'nullable|string|max:255',
            'payments.*.expected_date' => 'nullable|date',
            'payments.*.amount' => 'nullable|numeric|min:0',
            'payments.*.percentage' => 'nullable|numeric|min:0',
            'payments.*.method' => 'nullable|string|max:255',
        ]);

        // echo '<pre>';
        // print_r($request->all());
        // die;

        $project = Project::findOrFail($id);

        $oldStatus = $project->status;

        $project->project_total_cost = $request->project_total_cost;
        $project->project_name = $request->project_name;
        $project->customer_id = $request->customer_id;
        $project->enquiry_id = $request->enquiry_id;
        $project->start_date = $request->start_date;
        $project->internal_deadline = $request->internal_deadline;
        $project->client_deadline = $request->client_deadline;
        $project->status = $request->status;
        $project->comment = $request->comment;
        $project->save();

        $project->technologies()->sync($request->technology_ids ?? []);

        // Delete old payments and recreate (or you can update smartly if needed)
        $project->payments()->delete();

        $totalPaymentAmount = 0;
        if ($request->payments) {
            foreach($request->payments as $payment){
                $totalPaymentAmount += $payment['received_amount'] ?? 0;
            }
        }
      
        if($totalPaymentAmount > $request->project_total_cost){
            flash('Total Received Amount cannot be greater than Project Total Cost')->error();
            return back()->with('error', 'Total Received Amount cannot be greater than Project Total Cost')->withInput();
        }

        $total_paid_amount = 0;
        if ($request->payments) {
           
            foreach ($request->payments as $payment) {
                if (!empty($payment['title']) && (!empty($payment['amount']) || !empty($payment['percentage']))) {
                     $project->payments()->create([
                        'payment_title' => $payment['title'] ?? null,
                        'expected_date' => $payment['expected_date'] ?? null,
                        'amount' => $payment['amount'] ?? 0,
                        'percentage' => $payment['percentage'] ?? 0,
                        'method' => $payment['method'] ?? null,
                        'status' => $payment['status'] ?? null,
                        'received_date' => $payment['received_date'] ?? null,
                        'tax' => $payment['tax_amount'] ?? 0, 
                        'total_amount' => $payment['total_amount'] ?? 0, 
                        'received_amount' => $payment['received_amount'] ?? 0, 
                        'received_tax' => $payment['received_tax'] ?? 0,
                        'received_total_amount' => $payment['total_received'] ?? 0,
                    ]);

                    if($payment['status'] == 'received'){
                        $total_paid_amount = $total_paid_amount + $payment['received_amount'];
                    }

                }
            }
        }

        $project->paid_amount = $total_paid_amount;
        $project->pending_amount = ($request->project_total_cost + ($request->project_total_cost * 0.05)) - $total_paid_amount; // 5% tax included
        $project->save();

        // Check if status has changed
        if ($oldStatus != $project->status) {
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'old_status' => $oldStatus,
                'new_status' => $project->status,
                'changed_by' => auth()->id(), // Assuming admin/user is logged in
                'changed_at' => now(),
            ]);
        }

        flash('Project updated successfully.')->success();
        return redirect()->route('projects.index');
    }

    public function getEnquiries($customerId)
    {
        $enquiries = Enquiry::where('customer_id', $customerId)->get();

        return response()->json($enquiries);
    }

   
}
