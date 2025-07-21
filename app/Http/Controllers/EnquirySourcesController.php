<?php
namespace App\Http\Controllers;

use App\Models\EnquirySource;
use Illuminate\Http\Request;

class EnquirySourcesController extends Controller {

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_enquiry_source',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_enquiry_source',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_enquiry_source',  ['only' => ['edit','update','updateStatus']]);
    }

    public function index(Request $request) {

        $search = $request->has('search') ? $request->search : '';
        $status = $request->has('status') ? $request->status : '';

        $enquiry_sources = EnquirySource::orderBy('name','ASC');

        if($search){
            $enquiry_sources->where('name', 'like', '%'.$search.'%');
        }

        if($status){
            if($status == 2){
                $status = 0;
            }
            $enquiry_sources->where('status', $status);
        }

        $enquiry_sources = $enquiry_sources->paginate(15);
        return view('backend.enquiry_sources.index', compact('enquiry_sources','search','status'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:enquiry_sources|max:255'
        ]);

        EnquirySource::create(['name' => $request->name,'created_by' => auth()->user()->id]);

        flash('Enquiry source added successfully.')->success();
        return response()->json(['success' => 'Enquiry source added successfully.']);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|unique:enquiry_sources,name,' . $id
        ]);

        $enquiry_source = EnquirySource::findOrFail($id);
        $enquiry_source->update(['name' => $request->name,'updated_by' => auth()->user()->id]);

        flash('Enquiry source updated successfully.')->success();
        return response()->json(['success' => 'Enquiry source updated successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $enquiry_source = EnquirySource::findOrFail($request->id);
        $enquiry_source->status = $request->status;
        $enquiry_source->save();
     
        return 1;
    }

    public function destroy($id)
    {
        EnquirySource::destroy($id);

        flash('Enquiry source '.trans('messages.deleted_msg'))->success();
        return redirect()->route('enquiry_sources.index');
    }
}
