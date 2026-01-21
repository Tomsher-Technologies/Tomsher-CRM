<?php
namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\DataContact;
use App\Models\SalespersonAssignment;
use App\Models\Customer;
use App\Models\EnquirySource;
use App\Models\Industry;
use App\Models\Country;
use App\Models\Emirate;
use App\Models\Enquiry;
use App\Models\EnquiryTransferHistory;
use App\Models\User;
use App\Models\DataStatus;
use App\Models\DataStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\DataImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DataController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_data',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_data',  ['only' => ['show']]);
        $this->middleware('permission:add_data',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_data',  ['only' => ['edit','update','updateStatus']]);
    }

    public function index(Request $request)
    {
        $request->session()->put('data_last_url', url()->full());

        $query = Data::query();

        // Keyword Search
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('data_code', 'like', "%$keyword%")
                  ->orWhere('company_name', 'like', "%$keyword%")
                  ->orWhere('company_email', 'like', "%$keyword%")
                  ->orWhere('company_country', 'like', "%$keyword%")
                  ->orWhere('emirate', 'like', "%$keyword%");
            });
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source_id',  $request->source);
        }

        if ($request->filled('user_id')) {
            $query->where('sales_person', $request->user_id);
        }

        // Filter by Active Status (Assuming you have a boolean `is_active`)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $last_updated_date  = $request->last_date ?? '';
        $follow_date        = $request->follow_date ?? '';
        $entry_date         = $request->entry_date ?? '';

        if ($last_updated_date != null) {
            $query->whereBetween('last_updated', [
                Carbon::createFromFormat('d-m-Y', explode(" to ", $last_updated_date)[0])->startOfDay(),
                Carbon::createFromFormat('d-m-Y', explode(" to ", $last_updated_date)[1])->endOfDay(),
            ]);
        }

        if ($follow_date != null) {
            $query->whereBetween('next_followup', [
                Carbon::createFromFormat('d-m-Y', explode(" to ", $follow_date)[0])->startOfDay(),
                Carbon::createFromFormat('d-m-Y', explode(" to ", $follow_date)[1])->endOfDay(),
            ]);
        }

        if ($entry_date != null) {
            $query->whereBetween('entry_date', [
                Carbon::createFromFormat('d-m-Y', explode(" to ", $entry_date)[0])->startOfDay(),
                Carbon::createFromFormat('d-m-Y', explode(" to ", $entry_date)[1])->endOfDay(),
            ]);
        }

        $sortBy = $request->get('sort_by');

        switch ($sortBy) {
            case 'next_followup_asc':
                $query->orderBy('next_followup', 'asc');
                break;

            case 'next_followup_desc':
                $query->orderBy('next_followup', 'desc');
                break;

            case 'last_updated_asc':
                $query->orderBy('last_updated', 'asc');
                break;

            case 'last_updated_desc':
                $query->orderBy('last_updated', 'desc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $data = $query->with('contacts')->paginate(20);

        $sources = EnquirySource::where('status', 1)->orderBy('name', 'asc')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        return view('backend.data.index', compact('data','users','sources'));
    }

    public function create()
    {
        $lastdata = Data::orderBy('id', 'desc')->first();
        $nextId = $lastdata ? $lastdata->id + 1 : 1;

        $dataCode = 'DATA' . str_pad($nextId, 5, '0', STR_PAD_LEFT); // Example: CUS00001

        $industries = Industry::where('status',1)->orderBy('name','ASC')->get();
        $countries = Country::orderBy('name','ASC')->get();
        $emirates = Emirate::orderBy('name','ASC')->get();
        $sources = EnquirySource::where('status', 1)->orderBy('name', 'asc')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        $status = DataStatus::where('is_active', 1)->orderBy('sort_order', 'asc')->get();
        return view('backend.data.create', compact('dataCode','industries','countries','emirates','users','sources','status'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_code'             => 'required|unique:datas,data_code',
            'company_name'          => 'required',
            'company_email'         => 'nullable|email',
            'website'               => 'nullable|url',
            'address'               => 'nullable|string',
            'google_map_link'       => 'nullable|url',
            'user_id'               => 'required'
        ]);
        
        $data = Data::create([
            'data_code'         => $request->data_code ?? NULL, 
            'entry_date'        => $request->entry_date ?? NULL,
            'status'            => $request->status ?? 'to_be_contacted',
            'source_id'         => $request->source_id ?? NULL,
            'requirement'       => $request->requirement ?? NULL,
            'company_name'      => $request->company_name ?? NULL, 
            'company_email'     => $request->company_email ?? NULL, 
            'industry_id'       => $request->industry ?? NULL,  
            'company_address'   => $request->address ?? NULL,  
            'company_country'   => $request->registered_country ?? NULL,  
            'emirate'           => $request->emirate ?? NULL,  
            'website_link'      => $request->website ?? NULL,  
            'google_location'   => $request->google_map_link ?? NULL,
            'sales_person'      => $request->user_id ?? NULL
        ]);

        foreach ($request->contacts as $contact) {
            $data->contacts()->create([
                'name'              => $contact['name'],
                'email'             => $contact['email'] ?? null,
                'landline_number'   => $contact['landline'] ?? null,
                'mobile_number'     => $contact['mobile'] ?? null,
                'whatsapp_number'   => $contact['whatsapp'] ?? null,
                'designation'       => $contact['designation'] ?? null,
                'is_primary'        => isset($contact['is_primary']) ? 1 : 0,
            ]);
        }

        $statusHistory =  $data->statusHistories()->create([
                                'status' => $request->status ?? 'to_be_contacted',
                                'status_date' => $request->entry_date ?? date('Y-m-d'),
                                'comment' => NULL,
                                'followup_date' => NULL,
                                'changed_by' => auth()->id(),
                            ]);

        flash('Data created successfully.')->success();

        return redirect()->route('data.index');
    }

    public function show($id)
    {
        $data = Data::with(['contacts', 'industry'])->findOrFail($id);
        return view('backend.data.show', compact('data'));
    }

    public function edit($id)
    {
        $data = Data::findOrFail($id);
        $industries = Industry::where('status',1)->orderBy('name','ASC')->get();
        $countries = Country::orderBy('name','ASC')->get();
        $emirates = Emirate::orderBy('name','ASC')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        $sources = EnquirySource::where('status', 1)->orderBy('name', 'asc')->get();
        $status = DataStatus::where('is_active', 1)->orderBy('sort_order', 'asc')->get();
        return view('backend.data.edit', compact('data', 'industries','countries','emirates','users','sources','status'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'data_code'         => 'required|unique:datas,data_code,' . $id,
            'company_name'          => 'required',
            'user_id'          => 'required'
        ]);

        DB::transaction(function () use ($request, $validated, $id) {
            $data = Data::findOrFail($id);

            $old_sale_person = $data->sales_person;
            $data->update([
                'data_code'         => $request->data_code ?? NULL, 
                'entry_date'        => $request->entry_date ?? NULL,
                // 'status'            => $request->status ?? 'to_be_contacted',
                'source_id'         => $request->source_id ?? NULL,
                'requirement'       => $request->requirement ?? NULL,
                'company_name'      => $request->company_name ?? NULL, 
                'company_email'     => $request->company_email ?? NULL, 
                'industry_id'       => $request->industry ?? NULL,  
                'company_address'   => $request->address ?? NULL,  
                'company_country'   => $request->registered_country ?? NULL,  
                'emirate'           => $request->emirate ?? NULL,  
                'website_link'      => $request->website ?? NULL,  
                'google_location'   => $request->google_map_link ?? NULL,
                'sales_person'      => $request->user_id ?? NULL
            ]);

            // Remove existing contacts and re-insert
            $data->contacts()->delete();

            foreach ($request->contacts as $contact) {
                if (!empty($contact['name'])) {
                    $data->contacts()->create([
                        'name'              => $contact['name'],
                        'email'             => $contact['email'] ?? null,
                        'landline_number'   => $contact['landline'] ?? null,
                        'mobile_number'     => $contact['mobile'] ?? null,
                        'whatsapp_number'   => $contact['whatsapp'] ?? null,
                        'designation'       => $contact['designation'] ?? null,
                        'is_primary'        => isset($contact['is_primary']) ? 1 : 0,
                    ]);
                }
            }
        });

        flash('Data updated successfully.')->success();
        $route = $request->session()->get('data_last_url') ?? route('data.index');
        return redirect($route);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'data_id' => 'required|exists:datas,id',
            'status' => 'required|in:to_be_contacted,contacted,follow_up,not_interested,not_responding,invalid_spam,convert_to_enquiry',
            'status_date' => 'required|date',
            'comment' => 'nullable|string',
        ]);

        $data = Data::findOrFail($request->data_id);

        $data->update([
            'status' => $request->status,
            'last_updated' => $request->status_date,
            'next_followup' => $request->followup_date,
            'last_comment' => $request->comment,
            'updated_by' => auth()->id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
       
        // Save status change as timeline activity
        $statusHistory =  $data->statusHistories()->create([
                                'status' => $request->status,
                                'status_date' => $request->status_date,
                                'comment' => $request->comment,
                                'followup_date' => $request->followup_date,
                                'changed_by' => auth()->id(),
                            ]);

        if($request->status === 'convert_to_enquiry'){
            $lastCustomer = Customer::orderBy('id', 'desc')->first();
            $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;

            $customerCode = 'CUS' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $customer = Customer::create([
                'customer_code'     => $customerCode ?? NULL, 
                'company_name'      => $data->company_name ?? NULL, 
                'company_email'     => $data->company_email ?? NULL, 
                'industry_id'       => $data->industry_id ?? NULL,  
                'company_address'   => $data->company_address ?? NULL,  
                'company_country'   => $data->company_country ?? NULL,  
                'emirate'           => $data->emirate ?? NULL,  
                'website_link'      => $data->website_link ?? NULL,  
                'ntc'               => 1, 
                'google_location'   => $data->google_location ?? NULL,
                'sales_person'      => $data->sales_person
            ]);

            foreach ($data->contacts as $contact) {
                $customer->contacts()->create([
                    'name'              => $contact->name ?? null,
                    'email'             => $contact->email ?? null,
                    'landline_number'   => $contact->landline_number ?? null,
                    'mobile_number'     => $contact->mobile_number ?? null,
                    'whatsapp_number'   => $contact->whatsapp_number ?? null,
                    'designation'       => $contact->designation ?? null,
                    'is_primary'        => $contact->is_primary,
                ]);
            }

            // Track initial assignment if set
            if ($data->sales_person) {
                SalespersonAssignment::create([
                    'customer_id' => $customer->id,
                    'old_sales_person_id' => null,
                    'new_sales_person_id' => $data->sales_person,
                    'enquiry_id' => null,
                    'changed_by' => auth()->id(),
                ]);
            }

            $formattedNumber = str_pad(1, 3, '0', STR_PAD_LEFT);
    
            $enquiryCode = $customerCode . '-' . $formattedNumber;

            $validated['customer_id'] = $customer->id;

            $validated['enquiry_date'] = date('Y-m-d');
            $validated['enquiry_source_id'] = $data->source_id;
            $validated['project_details'] = $data->requirement;
            $validated['source_mode'] = 'self';
            $validated['status'] = 'new_enquiry';

            $validated['enquiry_code'] = $enquiryCode;
            $validated['added_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();
            $validated['owner_id'] = $data->sales_person ?? auth()->id();
            $enquiry = Enquiry::create($validated);

            $enquiry->statusHistories()->create([
                'status' => 'new_enquiry',
                'status_date' => date('Y-m-d'),
                'comment' => NULL,
                'submitted_cost' => 0,
                'approved_cost' => 0,
                'changed_by' => auth()->id(),
            ]);

            EnquiryTransferHistory::create([
                'enquiry_id' => $enquiry->id,
                'transferred_by' => NULL,
                'transferred_to' => $data->sales_person ?? auth()->id(),
            ]);
        }

        flash('Data status updated successfully.')->success();
        return response()->json(['success' => true]);
    }

    public function getStatusData($id, $status)
    {
        $statusData = DataStatusHistory::where('data_id', $id)->where('status', $status)->orderBy('id','desc')->first();
        return response()->json([
            'comment' => $statusData->comment ?? '',
            'status_date' => $statusData->status_date ?? date('Y-m-d'),
            'status' => $status,
            'followup_date' => $statusData->followup_date ?? date('Y-m-d')
        ]);
    }

    public function importData(){
        return view('backend.data.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        $import = new \App\Imports\DataImport();
        Excel::import($import, $request->file('file'));

        $errors = $import->getRowErrors();

        // echo '<pre>';
        // print_r($errors);
        // die;
        if (!empty($errors)) {
            return back()->with('import_errors', $errors);
        }

        flash('Data imported successfully.')->success();
        return back()->with('success', 'Data imported successfully!');
    }

    public function timeline($id)
    {
        $data = Data::with(['statusHistories.changedBy'])->findOrFail($id);
        return view('backend.data.timeline', compact('data'));
    }

} 
