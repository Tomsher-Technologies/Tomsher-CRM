<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Enquiry;
use App\Models\Customer;
use App\Models\EnquirySource;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\EnquiryStatusHistory;
use Artisan;
use Cache;
use Carbon\Carbon;
use DB;

class AdminController extends Controller
{
    public function admin_dashboard(Request $request)
    {
        $filter = $chartType = $from_date = $to_date ='';
        $chartData = [];
        
        // Current Month Enquiries by Source
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        $date_range = $request->input('date_range');
        $user_id = $request->input('user_id');

        if (!empty($date_range) && str_contains($date_range, ' to ')) {
            [$from_date, $to_date] = array_map('trim', explode(' to ', $date_range));
        }
        
        $users = User::orderBy('name', 'asc')->get();

        $query = Enquiry::query();

        // Apply filter if selected
        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('enquiry_date', [
                Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay(),
                Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay(),
            ]);
        }

        $statusCounts = (clone $query)
                        ->select('enquiries.status', \DB::raw('count(*) as total')) // Explicitly use 'enquiries.status'
                        ->join('enquiry_statuses as es', 'es.status_key', '=', 'enquiries.status') // Join with 'enquiries' table
                        ->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })
                        ->groupBy('enquiries.status') // Group by 'enquiries.status'
                        ->orderBy('es.sort_order', 'asc') // Order by sort_order
                        ->pluck('total', 'enquiries.status') // Pluck the total count with status as key
                        ->toArray();

        $enquiriesBySource = (clone $query)
                            ->select('enquiry_source_id', \DB::raw('count(*) as total'))
                            ->when($user_id, function ($q) use ($user_id) {
                                $q->where('owner_id', $user_id);
                            })
                            ->groupBy('enquiry_source_id')
                            ->pluck('total', 'enquiry_source_id')
                            ->toArray();


        // Enquiries by milestone
        $queryMile = EnquiryStatusHistory::query()
                    ->select('enquiry_status_histories.status', \DB::raw('COUNT(DISTINCT enquiry_id) as total'))
                    ->join('enquiry_statuses as es', 'es.status_key', '=', 'enquiry_status_histories.status')
                    ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                        $q->whereBetween('enquiry_status_histories.status_date', [
                            Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay(),
                            Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay(),
                        ]);
                    })
                    ->when($user_id, function ($q) use ($user_id) {
                        // Filter by owner of enquiry
                        $q->whereHas('enquiry', function ($q2) use ($user_id) {
                            $q2->where('owner_id', $user_id);
                        });
                    })
                    ->groupBy('enquiry_status_histories.status')
                    ->orderBy('es.sort_order', 'asc'); // or custom ordering if needed

        $statusCountsMilestone = $queryMile->pluck('total', 'status')->toArray();

        // Enquiry Sources
        $enquirySources = EnquirySource::all();
        $totalEnquiries = (clone $query)->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })->count();

        $customerQuery = Customer::query();

        if (!empty($from_date) && !empty($to_date)) {
            $customerQuery->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay(),
                Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay(),
            ]);
        }

        $totalCustomers = $customerQuery->when($user_id, function ($q) use ($user_id) {
                                $q->where('sales_person', $user_id);
                            })->count();
    
        $queryProjectType = Enquiry::query()
                        ->leftJoin('enquiry_project_types', 'enquiries.id', '=', 'enquiry_project_types.enquiry_id')
                        ->leftJoin('project_types', 'enquiry_project_types.project_type_id', '=', 'project_types.id')
                        ->selectRaw('IFNULL(project_types.name, "No Project Type") as project_type, COUNT(*) as total')
                        ->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })
                        ->groupBy('project_type');

        if (!empty($from_date) && !empty($to_date)) {
            $queryProjectType->whereBetween('enquiry_date', [
                Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay(),
                Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay(),
            ]);
        }

        // Get the enquiry count by project type (including "No Project Type" category)
        $enquiriesByProjectType = $queryProjectType->pluck('total', 'project_type')->toArray();

        $projectTypes = ProjectType::all();

        $projectsQuery = Project::query();

        if (!empty($from_date) && !empty($to_date)) {
            $projectsQuery->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $from_date)->startOfDay(),
                Carbon::createFromFormat('d-m-Y', $to_date)->endOfDay(),
            ]);
        }

        $totalProjects = $projectsQuery->when($user_id, function ($q) use ($user_id) {
                                            $q->whereHas('enquiry', function ($sub) use ($user_id) {
                                                $sub->where('owner_id', $user_id);
                                            });
                                        })->count();


        // Enquiries - Total, Pending & Contacted 

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

      
        if (empty($selectedMonth) && empty($selectedYear)) {
            // No filter -> Current month -> daywise counts, showing all days of current month
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        
            // Query to get total, pending, and contacted enquiries daywise
            $chartData = Enquiry::selectRaw('DAY(enquiry_date) as label, 
                                            COUNT(*) as total, 
                                            SUM(CASE WHEN status = "new_enquiry" THEN 1 ELSE 0 END) as pending, 
                                            SUM(CASE WHEN status != "new_enquiry" THEN 1 ELSE 0 END) as contacted')
                        ->whereBetween('enquiry_date', [$start, $end])
                        ->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })
                        ->groupBy('label')
                        ->orderBy('label')
                        ->get();
        
            // Ensure all days are included, even those with no data (value = 0)
            $daysInMonth = Carbon::now()->daysInMonth;
            $fullMonthData = [];
        
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $data = $chartData->firstWhere('label', $day);
                $fullMonthData[$day] = [
                    'total' => $data ? $data->total : 0,
                    'pending' => $data ? $data->pending : 0,
                    'contacted' => $data ? $data->contacted : 0
                ];
            }
        
            $chartData = $fullMonthData;
            $chartType = 'daywise';
        }
        elseif (!empty($selectedMonth) && empty($selectedYear)) {
            // Only month selected -> show yearwise counts for that month across all years
            $chartData = Enquiry::selectRaw('YEAR(enquiry_date) as label, 
                                            COUNT(*) as total, 
                                            SUM(CASE WHEN status = "new_enquiry" THEN 1 ELSE 0 END) as pending, 
                                            SUM(CASE WHEN status != "new_enquiry" THEN 1 ELSE 0 END) as contacted')
                        ->whereMonth('enquiry_date', $selectedMonth)
                        ->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })
                        ->groupBy('label')
                        ->orderBy('label')
                        ->get();
        
            // Get all years that should be shown
            $years = Enquiry::whereMonth('enquiry_date', $selectedMonth)
                            ->distinct()
                            ->pluck(\DB::raw('YEAR(enquiry_date) as year'))
                            ->toArray();
        
            // Add missing years with 0 count
            $fullYearData = [];
            foreach ($years as $year) {
                $data = $chartData->firstWhere('label', $year);
                $fullYearData[$year] = [
                    'total' => $data ? $data->total : 0,
                    'pending' => $data ? $data->pending : 0,
                    'contacted' => $data ? $data->contacted : 0
                ];
            }
        
            $chartData = $fullYearData;
            $chartType = 'yearwise_for_month';
        }
        elseif (!empty($selectedYear) && empty($selectedMonth)) {
            // Only year selected -> monthwise counts for that year
            $chartData = Enquiry::selectRaw('MONTH(enquiry_date) as label, 
                                            COUNT(*) as total, 
                                            SUM(CASE WHEN status = "new_enquiry" THEN 1 ELSE 0 END) as pending, 
                                            SUM(CASE WHEN status != "new_enquiry" THEN 1 ELSE 0 END) as contacted')
                        ->whereYear('enquiry_date', $selectedYear)
                        ->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })
                        ->groupBy('label')
                        ->orderBy('label')
                        ->get();
        
            // Ensure all months are included, even those with no data (value = 0)
            $fullYearData = [];
            for ($month = 1; $month <= 12; $month++) {
                $data = $chartData->firstWhere('label', $month);
                $fullYearData[$monthNames[$month]] = [
                    'total' => $data ? $data->total : 0,
                    'pending' => $data ? $data->pending : 0,
                    'contacted' => $data ? $data->contacted : 0
                ];
            }
        
            $chartData = $fullYearData;
            $chartType = 'monthwise';
        }
        elseif (!empty($selectedYear) && !empty($selectedMonth)) {
            // Both month and year selected -> daywise counts for that month and year
            $chartData = Enquiry::selectRaw('DAY(enquiry_date) as label, 
                                            COUNT(*) as total, 
                                            SUM(CASE WHEN status = "new_enquiry" THEN 1 ELSE 0 END) as pending, 
                                            SUM(CASE WHEN status != "new_enquiry" THEN 1 ELSE 0 END) as contacted')
                        ->whereYear('enquiry_date', $selectedYear)
                        ->whereMonth('enquiry_date', $selectedMonth)
                        ->when($user_id, function ($q) use ($user_id) {
                            $q->where('enquiries.owner_id', $user_id);
                        })
                        ->groupBy('label')
                        ->orderBy('label')
                        ->get();
        
            // Ensure all days are included, even those with no data (value = 0)
            $daysInMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
            $fullMonthData = [];
        
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $data = $chartData->firstWhere('label', $day);
                $fullMonthData[$day] = [
                    'total' => $data ? $data->total : 0,
                    'pending' => $data ? $data->pending : 0,
                    'contacted' => $data ? $data->contacted : 0
                ];
            }
        
            $chartData = $fullMonthData;
            $chartType = 'daywise';
        }

        return view('backend.dashboard', compact('filter','statusCounts', 'totalEnquiries','totalCustomers','enquiriesBySource','enquirySources', 'currentMonth', 'currentYear','selectedMonth','selectedYear','chartData','chartType', 'totalProjects','enquiriesByProjectType','projectTypes','users','statusCountsMilestone'));
    }

    function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        flash(trans('messages.cache_cleared_successfully'))->success();
        return back();
    }
}
