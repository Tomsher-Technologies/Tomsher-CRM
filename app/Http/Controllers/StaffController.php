<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Role;
use App\Models\User;
use Hash;

class StaffController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_staffs',  ['only' => ['index','destroy']]);
        $this->middleware('permission:add_staff',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_staff',  ['only' => ['edit','update','updateStatus']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = $request->has('search') ? $request->search : '';
        $role_id = $request->has('role_id') ? $request->role_id : '';
        $users = User::where('user_type','staff')->orderBy('id','desc');
        
        if($sort_search){
            $users = $users->where(function ($query) use ($sort_search){
                        $query->where('name', 'like','%' . $sort_search . '%')
                            ->orWhere('email', 'like', '%' . $sort_search . '%')
                            ->orWhere('phone', 'like', '%' . $sort_search . '%');
                    });
        }
        
        if ($role_id != '') {
            $users->whereHas('roles', function ($q) use ($role_id) {
                $q->where('name', $role_id);
            });
        }
        $users = $users->paginate(10);
       
        return view('backend.staffs.index', compact('users','sort_search','role_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('is_active', 1)->get();
        return view('backend.staffs.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'role' => 'required'
        ]);

        if(User::where('email', $request->email)->first() == null){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->user_type = "staff";
            $user->password = Hash::make($request->password);
            if($user->save()){
                $user->assignRole($request->role);
                flash(trans('messages.staff_create_msg'))->success();
                return redirect()->route('staffs.index');
            }
        }

        flash(trans('messages.email_used'))->error();
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $staff = User::findOrFail(decrypt($id));
        $roles = Role::where('is_active', 1)->get();
        return view('backend.staffs.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->mobile;
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }
        if($user->save()){

            $user->syncRoles([$request->role_id]);

            flash(trans('messages.staff_update_msg'))->success();
            return redirect()->route('staffs.index');
        }

        flash(trans('messages.something_went_wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        flash(trans('messages.staff_delete_msg'))->success();
        return redirect()->route('staffs.index');
    }

    public function updateStatus(Request $request)
    {
        $user = User::findOrFail($request->id);
        
        $user->banned = $request->status;
        $user->save();
       
        return 1;
    }
}
