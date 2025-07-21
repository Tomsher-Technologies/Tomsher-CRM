<?php
namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;

class ProjectTypesController extends Controller {

    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_project_type',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_project_type',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_project_type',  ['only' => ['edit','update','updateStatus']]);
    }

    public function index(Request $request) {

        $search = $request->has('search') ? $request->search : '';
        $status = $request->has('status') ? $request->status : '';

        $project_types = ProjectType::orderBy('name','ASC');

        if($search){
            $project_types->where('name', 'like', '%'.$search.'%');
        }

        if($status){
            if($status == 2){
                $status = 0;
            }
            $project_types->where('status', $status);
        }

        $project_types = $project_types->paginate(15);
        return view('backend.project_types.index', compact('project_types','search','status'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:project_types|max:255'
        ]);

        ProjectType::create(['name' => $request->name,'created_by' => auth()->user()->id]);

        flash('Project category added successfully.')->success();
        return response()->json(['success' => 'Project category added successfully.']);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|unique:project_types,name,' . $id
        ]);

        $project_type = ProjectType::findOrFail($id);
        $project_type->update(['name' => $request->name,'updated_by' => auth()->user()->id]);

        flash('Project category updated successfully.')->success();
        return response()->json(['success' => 'Project category updated successfully.']);
    }

    public function updateStatus(Request $request)
    {
        $project_type = ProjectType::findOrFail($request->id);
        $project_type->status = $request->status;
        $project_type->save();
     
        return 1;
    }

    public function destroy($id)
    {
        ProjectType::destroy($id);

        flash('Project category '.trans('messages.deleted_msg'))->success();
        return redirect()->route('project_types.index');
    }
}
