<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Industry;
use App\Models\Country;
use App\Models\Emirate;
use App\Models\User;
use App\Models\SalespersonAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;

class CustomerController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_customers',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_customers',  ['only' => ['show']]);
        $this->middleware('permission:add_customer',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_customer',  ['only' => ['edit','update','updateStatus']]);
        $this->middleware('permission:export_customer',  ['only' => ['export']]);
    }

    public function export(Request $request)
    {
        return Excel::download(new CustomersExport($request), 'customers.xlsx');
    }

    public function index(Request $request)
    {
        $request->session()->put('customers_last_url', url()->full());

        $query = Customer::with('projects');

        // Keyword Search
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('customer_code', 'like', "%$keyword%")
                  ->orWhere('company_name', 'like', "%$keyword%")
                  ->orWhere('company_email', 'like', "%$keyword%")
                  ->orWhere('company_country', 'like', "%$keyword%")
                  ->orWhere('emirate', 'like', "%$keyword%")
                  ->orWhereHas('contacts', function ($contact) use ($keyword) {
                        $contact->where('landline_number', 'like', "%{$keyword}%")
                                ->orWhere('mobile_number', 'like', "%{$keyword}%")
                                ->orWhere('whatsapp_number', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter by Industry
        if ($request->filled('industry')) {
            $childIds = [];
            $industryfilter = $request->industry;
            $childIds[] = array($request->industry);
            
            if($industryfilter != ''){
                $childIds[] = Industry::where('parent_id', $industryfilter)->pluck('id')->toArray();
            }

            if(!empty($childIds)){
                $childIds = array_merge(...$childIds);
                $childIds = array_unique($childIds);
            }

            $query->whereIn('industry_id',  $childIds);
        }

        if ($request->filled('user_id')) {
            $query->where('sales_person', $request->user_id);
        }

        // Filter by Active Status (Assuming you have a boolean `is_active`)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by New to Company status
        if ($request->filled('ntc')) {
            $query->where('ntc', $request->ntc);
        }

        // Filter by Created Date Range
        if ($request->filled('date_range')) {
            $date = $request->date_range;
            [$fromRaw, $toRaw] = explode(" to ", $date);
            $from = \Carbon\Carbon::createFromFormat('d-m-Y', trim($fromRaw))->startOfDay();
            $to   = \Carbon\Carbon::createFromFormat('d-m-Y', trim($toRaw))->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $customers = $query->with('contacts')->latest()->paginate(20);

        $industries = $industries = Industry::where('status',1)->orderBy('name','ASC')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        return view('backend.customers.index', compact('customers','industries','users'));
    }

    public function create()
    {
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;

        $customerCode = 'CUS' . str_pad($nextId, 5, '0', STR_PAD_LEFT); // Example: CUS00001

        $industries = Industry::where('status',1)->orderBy('name','ASC')->get();
        $countries = Country::orderBy('name','ASC')->get();
        $emirates = Emirate::orderBy('name','ASC')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        return view('backend.customers.create', compact('customerCode','industries','countries','emirates','users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code'         => 'required|unique:customers,customer_code',
            'company_name'          => 'required',
            'company_email'         => 'nullable|email',
            'website'               => 'nullable|url',
            'address'               => 'nullable|string',
            'new_to_company'        => 'nullable|boolean',
            'google_map_link'       => 'nullable|url',
            'user_id'               => 'required'
        ]);
        
        $customer = Customer::create([
            'customer_code'     => $request->customer_code ?? NULL, 
            'company_name'      => $request->company_name ?? NULL, 
            'company_email'     => $request->company_email ?? NULL, 
            'industry_id'       => $request->industry ?? NULL,  
            'company_address'   => $request->address ?? NULL,  
            'company_country'   => $request->registered_country ?? NULL,  
            'emirate'           => $request->emirate ?? NULL,  
            'website_link'      => $request->website ?? NULL,  
            'ntc'               => $request->new_to_company ?? 0, 
            'google_location'   => $request->google_map_link ?? NULL,
            'sales_person'      => $request->user_id
        ]);

        foreach ($request->contacts as $contact) {
            $customer->contacts()->create([
                'name'              => $contact['name'],
                'email'             => $contact['email'] ?? null,
                'landline_number'   => $contact['landline'] ?? null,
                'mobile_number'     => $contact['mobile'] ?? null,
                'whatsapp_number'   => $contact['whatsapp'] ?? null,
                'designation'       => $contact['designation'] ?? null,
                'is_primary'        => isset($contact['is_primary']) ? 1 : 0,
            ]);
        }

        // Track initial assignment if set
        if ($request->user_id) {
            SalespersonAssignment::create([
                'customer_id' => $customer->id,
                'old_sales_person_id' => null,
                'new_sales_person_id' => $request->user_id,
                'enquiry_id' => null,
                'changed_by' => auth()->id(),
            ]);
        }

        flash('Customer created successfully.')->success();

        if($request->button == 'save_create'){
            return redirect()->route('enquiries.create',['customer_id' => $customer->id]);
        }else{
            return redirect()->route('customers.index');
        }
        
    }

    public function show($id)
    {
        $customer = Customer::with(['contacts', 'industry'])->findOrFail($id);
        return view('backend.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $industries = Industry::where('status',1)->orderBy('name','ASC')->get();
        $countries = Country::orderBy('name','ASC')->get();
        $emirates = Emirate::orderBy('name','ASC')->get();
        $users = User::where('banned',0)->orderBy('name', 'asc')->get();
        return view('backend.customers.edit', compact('customer', 'industries','countries','emirates','users'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_code'         => 'required|unique:customers,customer_code,' . $id,
            'company_name'          => 'required',
            'user_id'          => 'required'
        ]);

        DB::transaction(function () use ($request, $validated, $id) {
            $customer = Customer::findOrFail($id);

            $old_sale_person = $customer->sales_person;
            $customer->update([
                'customer_code'     => $request->customer_code ?? NULL, 
                'company_name'      => $request->company_name ?? NULL, 
                'company_email'     => $request->company_email ?? NULL, 
                'industry_id'       => $request->industry ?? NULL,  
                'company_address'   => $request->address ?? NULL,  
                'company_country'   => $request->registered_country ?? NULL,  
                'emirate'           => $request->emirate ?? NULL,  
                'website_link'      => $request->website ?? NULL,  
                'ntc'               => $request->new_to_company ?? 0, 
                'google_location'   => $request->google_map_link ?? NULL,
                'sales_person'      => $request->user_id
            ]);

            // Remove existing contacts and re-insert
            $customer->contacts()->delete();

            foreach ($request->contacts as $contact) {
                if (!empty($contact['name'])) {
                    $customer->contacts()->create([
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

            // Log assignment change
            if ($request->user_id != $old_sale_person) {
                SalespersonAssignment::create([
                    'customer_id' => $customer->id,
                    'old_sales_person_id' => $old_sale_person,
                    'new_sales_person_id' => $request->user_id,
                    'enquiry_id' => null,
                    'changed_by' => auth()->id(),
                ]);
            }
        });

        flash('Customer updated successfully.')->success();
        $route = $request->session()->get('customers_last_url') ?? route('customers.index');
        return redirect($route);
    }

    public function updateStatus(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $customer->is_active = $request->status;
        $customer->save();
     
        return 1;
    }
}