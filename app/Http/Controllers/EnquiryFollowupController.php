<?php

namespace App\Http\Controllers;

use App\Models\EnquiryFollowup;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;

class EnquiryFollowupController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:manage_followups',  ['only' => ['index','destroy']]);
        $this->middleware('permission:view_followups',  ['only' => ['show']]);
        $this->middleware('permission:add_followups',  ['only' => ['create','store']]);
        $this->middleware('permission:edit_followups',  ['only' => ['edit','update','changeStatus']]);
        $this->middleware('permission:view_followup_calendar',  ['only' => ['calendar','calendarEvents','updateStatus']]);
        
    }

    public function index(Request $request)
    {
        $request->session()->put('followups_last_url', url()->full());
        $request->session()->put('previous_section', 'followup');
        // DB::enableQueryLog();
        $users = User::orderBy('name', 'asc')->get();
        $date_range = $request->has('date_range') ? $request->date_range : '';
        $query = EnquiryFollowup::query()->with(['enquiry.customer']);

        if ($request->filled('enquiry_id')) {
            $query->where('enquiry_id', $request->enquiry_id);
        }

        if ($request->filled('followup_type')) {
            $query->where('followup_type', $request->followup_type);
        }

        if ($request->filled('sub_type')) {
            $query->where('sub_type', $request->sub_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('created_by')) {
            $userId = $request->created_by;
            $query->where(function ($q) use ($userId) {
                        $q->where('created_by', $userId)
                        ->orWhereHas('participants', function ($subQ) use ($userId) {
                            $subQ->where('user_id', $userId);
                        });
                    });
        }

        if (!auth()->user()->can('view_all_users_followups')) {
            $userId = auth()->user()->id;
            $query->where(function ($q) use ($userId) {
                        $q->where('created_by', $userId)
                        ->orWhereHas('participants', function ($subQ) use ($userId) {
                            $subQ->where('user_id', $userId);
                        });
                    });
        } 

        if ($request->filled('date_range')) {
            $date_var = array_map('trim', explode("to", $request->date_range));

            $from = Carbon::createFromFormat('d-m-Y H:i:s', $date_var[0])->format('Y-m-d H:i:s');
            $to   = Carbon::createFromFormat('d-m-Y H:i:s', $date_var[1])->format('Y-m-d H:i:s');

            $query->where('followup_time', '>=' ,$from);
            $query->where('followup_time', '<=' ,$to);
        }

        $followups = $query->orderByRaw("
                                COALESCE(
                                    CASE 
                                        WHEN followup_type = 'meeting' THEN followup_from
                                        ELSE followup_time
                                    END,
                                    created_at
                                ) DESC
                            ")->paginate(15);
        // dd(DB::getQueryLog());
        $allQuery = Enquiry::with('customer');
        if (!auth()->user()->can('view_all_users_followups')) {
            $allQuery->where('owner_id', auth()->user()->id);
        } 
        $enquiries = $allQuery->get();

        return view('backend.followups.index', compact('followups', 'enquiries','date_range','users'));
    }

    public function create($enquiry_id = NULL)
    {
        $allQuery = Enquiry::with('customer');
        if (!auth()->user()->can('view_all_users_followups')) {
            $allQuery->where('owner_id', auth()->user()->id);
        } 
        $enquiries = $allQuery->get();

        $users = User::where('banned',0)->whereNotIn('id', [auth()->user()->id])->orderBy('name','asc')->get();

        return view('backend.followups.create', compact('enquiries','enquiry_id','users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enquiry_id' => 'required|exists:enquiries,id',
            'followup_type' => 'required|in:call,email,whatsapp,meeting',
            'sub_type' => 'required|string',
            'followup_time' => 'required_if:followup_type,!=meeting',
            
            'followup_from' => [
                Rule::requiredIf($request->followup_type === 'meeting'),
                'nullable',
                'date',
            ],
            'followup_to' => [
                Rule::requiredIf($request->followup_type === 'meeting'),
                'nullable',
                'date',
                'after:followup_from',
            ],
            'comment' => 'required|string',
            'location' => [
                            'string',
                            'nullable',
                            Rule::requiredIf(function () use ($request) {
                                return $request->followup_type === 'meeting' && $request->sub_type === 'in-person';
                            }),
                        ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $enquiry = Enquiry::find($request->enquiry_id);

        $followup = EnquiryFollowup::create([
            'enquiry_status' => $enquiry->status ?? NULL,
            'enquiry_id' => $request->enquiry_id,
            'followup_type' => $request->followup_type,
            'sub_type' => $request->sub_type,
            'followup_time' => $request->followup_type != 'meeting' ? $request->followup_time : null,
            'followup_from' => $request->followup_type === 'meeting' ? $request->followup_from : null,
            'followup_to' => $request->followup_type === 'meeting' ? $request->followup_to : null,
            'subject' => $request->comment,
            'location' => $request->followup_type === 'meeting' ? $request->location : null,
            'created_by' => auth()->user()->id,
            'status' => $request->status,
            'post_comment' => $request->post_comment, 
        ]);

        if ($request->followup_type === 'meeting') {
            $participantIds = $request->participants ?? [];

            if (!in_array(auth()->id(), $participantIds)) {
                $participantIds[] = auth()->id(); // Ensure creator is included
            }

            // Prepare sync array with pivot data
            $syncData = [];
            foreach ($participantIds as $userId) {
                $syncData[$userId] = ['is_main' => $userId == auth()->id()];
            }
            
            $followup->participants()->sync($syncData);
        }

        flash('Follow-up added successfully.')->success();

        $previous = $request->session()->get('previous_section');

        if($previous === 'enquiry'){
            return redirect()->route('enquiries.index');
        }else{
            return redirect()->route('followups.index');
        }
       
    }

    public function edit($id)
    {
        $followup = EnquiryFollowup::findOrFail($id);

        $followup->load('participants'); // eager load participants
        $participantIds = $followup->participants->pluck('id')->toArray();
        $mainParticipantId = $followup->participants->firstWhere('pivot.is_main', 1)?->id;

        $allQuery = Enquiry::with('customer');
        if (!auth()->user()->can('view_all_users_followups')) {
            $allQuery->where('owner_id', auth()->user()->id);
        } 
        $enquiries = $allQuery->get();

        $users = User::where('banned',0)->whereNotIn('id', [auth()->user()->id])->orderBy('name','asc')->get();
        return view('backend.followups.edit', compact('followup', 'enquiries','participantIds','mainParticipantId','users'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'enquiry_id' => 'required|exists:enquiries,id',
            'followup_type' => 'required|in:call,email,whatsapp,meeting',
            'sub_type' => 'required|string',
            'followup_time' => 'required_if:followup_type,!=meeting',
            'followup_from' => [
                Rule::requiredIf($request->followup_type === 'meeting'),
                'nullable',
                'date',
            ],
            'followup_to' => [
                Rule::requiredIf($request->followup_type === 'meeting'),
                'nullable',
                'date',
                'after:followup_from',
            ],
            'comment' => 'required|string',
            'location' => [
                            'string',
                            'nullable',
                            Rule::requiredIf(function () use ($request) {
                                return $request->followup_type === 'meeting' && $request->sub_type === 'in-person';
                            }),
                        ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $followup = EnquiryFollowup::findOrFail($id);

        $enquiry = Enquiry::find($request->enquiry_id);

        $followup->update([
            'enquiry_status' => ($request->enquiry_id != $followup->enquiry_id) ? $enquiry->status : $followup->enquiry_status,
            'enquiry_id' => $request->enquiry_id,
            'followup_type' => $request->followup_type,
            'sub_type' => $request->sub_type,
            'followup_time' => $request->followup_type != 'meeting' ? $request->followup_time : null,
            'followup_from' => $request->followup_type === 'meeting' ? $request->followup_from : null,
            'followup_to' => $request->followup_type === 'meeting' ? $request->followup_to : null,
            'subject' => $request->comment,
            'location' => $request->followup_type === 'meeting' ? $request->location : null,
            'updated_by' => auth()->user()->id,
            'status' => $request->status,
            'post_comment' => $request->post_comment,
        ]);

        if ($request->followup_type === 'meeting') {
            $participantIds = $request->participants ?? [];

            // Ensure creator is always included
            $participantIds[] = $followup->created_by;

            // Prepare sync array with pivot data
            $syncData = [];
            foreach ($participantIds as $userId) {
                $syncData[$userId] = ['is_main' => $userId == auth()->id()];
            }

            $followup->participants()->sync($syncData);
        }

        flash( 'Follow-up updated successfully.')->success();
        if($request->status == 'rescheduled'){
            return redirect()->route('followups.create',['enquiry_id' => $request->enquiry_id]);
        }else{
            return redirect()->route('followups.index');
        }
    }

    public function calendar()
    {
        $allQuery = Enquiry::with('customer');
        if (!auth()->user()->can('view_all_users_followups')) {
            $allQuery->where('owner_id', auth()->user()->id);
        } 
        $enquiries = $allQuery->orderBy('id', 'desc')->get();

        $users = User::orderBy('name', 'asc')->get();

        return view('backend.followups.calendar', compact('enquiries','users'));
    }

    public function calendarEvents(Request $request)
    {
        $query = EnquiryFollowup::query()->with('enquiry');
    
        if ($request->filled('enquiry_id')) {
            $query->where('enquiry_id', $request->enquiry_id);
        }
    
        if ($request->filled('type')) {
            $query->where('followup_type', $request->type);
        }

        if ($request->filled('created_by')) {
            $userId = $request->created_by;
            $query->where(function ($q) use ($userId) {
                        $q->where('created_by', $userId)
                        ->orWhereHas('participants', function ($subQ) use ($userId) {
                            $subQ->where('user_id', $userId);
                        });
                    });
        }
        
        if (!auth()->user()->can('view_all_users_followups')) {
            $userId = auth()->user()->id;
            // $query->where(function ($q) use ($userId) {
            //             $q->where('created_by', $userId)
            //             ->orWhereHas('participants', function ($subQ) use ($userId) {
            //                 $subQ->where('user_id', $userId);
            //             });
            //         });

            $query->where(function ($q) use ($userId) {
                $q->where('followup_type', 'meeting') // show all meetings
                ->orWhere(function ($q2) use ($userId) {
                    $q2->where('created_by', $userId)
                        ->orWhereHas('participants', function ($subQ) use ($userId) {
                            $subQ->where('user_id', $userId);
                        });
                });
            });
        } 
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        $followups = $query->get();
    
        $events = $followups->map(function ($f) {

            $followupTime = \Carbon\Carbon::parse($f->followup_time);
            $followupFromTime = \Carbon\Carbon::parse($f->followup_from);
            if($f->status === 'completed'){
                $color = '#079f19';
            }
            else if($f->status === 'canceled'){
                $color = '#949494';
            }else if($f->status === 'rescheduled'){
                $color = '#ffc519';
            }else{
                if($f->followup_type === 'meeting'){
                    if($followupFromTime->isFuture()){
                        $color = '#abe4fb';
                    }else{
                        $color = '#c50c0c';
                    }
                }else{
                    if($followupTime->isFuture()){
                        $color = '#abe4fb';
                    }else{
                        $color = '#c50c0c';
                    }
                }
            }
            
            $icon = '';
            switch ($f->followup_type) {
                case 'call': $icon = '<i class="las fs-20 la-phone text-primary me-1"></i>'; break;
                case 'email': $icon = '<i class="las fs-20 la-envelope text-danger me-1"></i>'; break;
                case 'whatsapp': $icon = '<i class="lab fs-20 la-whatsapp text-success me-1"></i>'; break;
                case 'meeting': $icon = '<i class="las fs-20 la-handshake text-warning me-1"></i>'; break;
            }

            return [
                'id' => $f->id,
                'title' => $f->enquiry->enquiry_code ?? '',
                'start' => $f->followup_type === 'meeting' ? $f->followup_from : $f->followup_time,
                'end'   => $f->followup_type === 'meeting' ? $f->followup_to : null,
                'color' => $color,
                'url' => '',
                'icon' => $icon,
                'extendedProps' => [
                    'followup_type' => $f->followup_type,
                    'type' => ucfirst($f->followup_type),
                    'sub_type' => ucfirst($f->sub_type),
                    'location' => $f->location,
                    'enquiry' => $f->enquiry->enquiry_code . ' - ' . $f->enquiry->customer->company_name,
                    'status' => ucfirst($f->status),
                    'org_status' => $f->status,
                    'subject' => $f->subject,
                    'post_comment' => $f->post_comment,
                    'color' => $color,
                    'created_by' => $f->added_by->name ?? '',
                    'created_by_user' => $f->created_by,
                    'participants' => $f->participants->where('pivot.is_main', false)->pluck('name')->join(', ')
                ]
            ];
        });
    
        return response()->json($events);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,canceled,rescheduled',
        ]);

        $followup = EnquiryFollowup::findOrFail($id);
        $followup->status = $request->status;
        $followup->post_comment = $request->post_comment ?? NULL;
        $followup->save();

        return response()->json(['message' => 'Status updated successfully']);
    }


}
