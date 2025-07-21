<?php
namespace App\Http\Controllers;

use App\Models\Technologies;
use Illuminate\Http\Request;

class TechnologyController extends Controller {

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_technologies',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_technology',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_technology',  ['only' => ['edit','update','updateStatus']]);
    }

    public function index(Request $request) {

        $search = $request->has('search') ? $request->search : '';
        $status = $request->has('status') ? $request->status : '';

        $technologies = Technologies::orderBy('name','ASC');

        if($search){
            $technologies->where('name', 'like', '%'.$search.'%');
        }

        if($status){
            if($status == 2){
                $status = 0;
            }
            $technologies->where('status', $status);
        }

        $technologies = $technologies->paginate(15);
        return view('backend.technologies.index', compact('technologies','search','status'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:technologies|max:255'
        ]);

        Technologies::create(['name' => $request->name,'created_by' => auth()->user()->id]);

        flash('Technology added successfully.')->success();
        return response()->json(['success' => 'Technology added successfully.']);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|unique:technologies,name,' . $id
        ]);

        $technology = Technologies::findOrFail($id);
        $technology->update(['name' => $request->name,'updated_by' => auth()->user()->id]);

        flash('Technology updated successfully.')->success();
        return response()->json(['success' => 'Technology updated successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $technology = Technologies::findOrFail($request->id);
        $technology->status = $request->status;
        $technology->save();
     
        return 1;
    }

    public function destroy($id)
    {
        Technologies::destroy($id);

        flash('Technology '.trans('messages.deleted_msg'))->success();
        return redirect()->route('technologies.index');
    }
}
