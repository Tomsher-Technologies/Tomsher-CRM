<?php
namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller {

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_industries',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_industry',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_industry',  ['only' => ['edit','update','updateStatus']]);
    }

    public function index(Request $request) {

        $search = $request->has('search') ? $request->search : '';
        $status = $request->has('status') ? $request->status : '';
        $industry_id = $request->has('industry_id') ? $request->industry_id : '';

        $industries = Industry::orderBy('name','ASC');

        if($search){
            $industries->where('name', 'like', '%'.$search.'%');
        }

        if($industry_id){
            $industries->where('parent_id', $industry_id);
        }

        if($status){
            if($status == 2){
                $status = 0;
            }
            $industries->where('status', $status);
        }

        $industries = $industries->paginate(20);

        $parentIndustries = Industry::whereNull('parent_id')->where('status',1)->get();
        return view('backend.industries.index', compact('industries','search','status','parentIndustries','industry_id'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:industries|max:255'
        ]);

        Industry::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => auth()->user()->id
        ]);

        flash('Industry added successfully.')->success();
        return response()->json(['success' => 'Industry added successfully.']);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|unique:industries,name,' . $id
        ]);

        $industry = Industry::findOrFail($id);
        $industry->update(['name' => $request->name,'parent_id' => $request->parent_id,'updated_by' => auth()->user()->id]);

        flash('Industry updated successfully.')->success();
        return response()->json(['success' => 'Industry updated successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $industry = Industry::findOrFail($request->id);
        $industry->status = $request->status;
        $industry->save();
     
        return 1;
    }

    public function destroy($id)
    {
        Industry::destroy($id);

        flash('Industry '.trans('messages.deleted_msg'))->success();
        return redirect()->route('industries.index');
    }
}
