<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Search;
use App\Models\Enquiry;
use App\Models\Customer;
use App\Models\EnquirySource;
use App\Models\ProjectType;
use App\Models\Project;
use Artisan;
use Cache;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function admin_dashboard(Request $request)
    {
        $filter = $chartType = '';
        $chartData = [];
        
        // Current Month Enquiries by Source
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        $query = Enquiry::query();

        // Apply filter if selected
        if (!empty($selectedMonth) && empty($selectedYear)) {
            $query->whereMonth('enquiry_date', $selectedMonth);
        } elseif (!empty($selectedYear) && empty($selectedMonth)) {
            $query->whereYear('enquiry_date', $selectedYear);
        } elseif (!empty($selectedMonth) && !empty($selectedYear)) {
            $query->whereYear('enquiry_date', $selectedYear)
                         ->whereMonth('enquiry_date', $selectedMonth);
        }

        $statusCounts = (clone $query)
                        ->select('enquiries.status', \DB::raw('count(*) as total')) // Explicitly use 'enquiries.status'
                        ->join('enquiry_statuses as es', 'es.status_key', '=', 'enquiries.status') // Join with 'enquiries' table
                        ->groupBy('enquiries.status') // Group by 'enquiries.status'
                        ->orderBy('es.sort_order', 'asc') // Order by sort_order
                        ->pluck('total', 'enquiries.status') // Pluck the total count with status as key
                        ->toArray();

        $enquiriesBySource = (clone $query)
                            ->select('enquiry_source_id', \DB::raw('count(*) as total'))
                            ->groupBy('enquiry_source_id')
                            ->pluck('total', 'enquiry_source_id')
                            ->toArray();

        // Enquiry Sources
        $enquirySources = EnquirySource::all();
        $totalEnquiries = (clone $query)->count();

        $customerQuery = Customer::query();

        if (!empty($selectedMonth) && empty($selectedYear)) {
            // Filter by month of any year
            $customerQuery->whereMonth('created_at', $selectedMonth);
        } elseif (!empty($selectedYear) && empty($selectedMonth)) {
            // Filter by full year
            $customerQuery->whereYear('created_at', $selectedYear);
        } elseif (!empty($selectedYear) && !empty($selectedMonth)) {
            // Filter by specific month + year
            $customerQuery->whereYear('created_at', $selectedYear)
                          ->whereMonth('created_at', $selectedMonth);
        }

        $totalCustomers = $customerQuery->count();

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
        

        $queryProjectType = Enquiry::query()
                        ->leftJoin('enquiry_project_types', 'enquiries.id', '=', 'enquiry_project_types.enquiry_id')
                        ->leftJoin('project_types', 'enquiry_project_types.project_type_id', '=', 'project_types.id')
                        ->selectRaw('IFNULL(project_types.name, "No Project Type") as project_type, COUNT(*) as total')
                        ->groupBy('project_type');

        // Apply filters if any (month/year)
        if (!empty($selectedMonth) && empty($selectedYear)) {
            $queryProjectType->whereMonth('enquiry_date', $selectedMonth);
        } elseif (!empty($selectedYear) && empty($selectedMonth)) {
            $queryProjectType->whereYear('enquiry_date', $selectedYear);
        } elseif (!empty($selectedMonth) && !empty($selectedYear)) {
            $queryProjectType->whereYear('enquiry_date', $selectedYear)
                ->whereMonth('enquiry_date', $selectedMonth);
        }

        // Get the enquiry count by project type (including "No Project Type" category)
        $enquiriesByProjectType = $queryProjectType->pluck('total', 'project_type')->toArray();

        // Get the project types (in case you need the names in a separate list)
        $projectTypes = ProjectType::all();

        $projectsQuery = Project::query();

        if (!empty($selectedMonth) && empty($selectedYear)) {
            // Filter by month of any year
            $projectsQuery->whereMonth('created_at', $selectedMonth);
        } elseif (!empty($selectedYear) && empty($selectedMonth)) {
            // Filter by full year
            $projectsQuery->whereYear('created_at', $selectedYear);
        } elseif (!empty($selectedYear) && !empty($selectedMonth)) {
            // Filter by specific month + year
            $projectsQuery->whereYear('created_at', $selectedYear)
                          ->whereMonth('created_at', $selectedMonth);
        }

        $totalProjects = $projectsQuery->count();

        return view('backend.dashboard', compact('filter','statusCounts', 'totalEnquiries','totalCustomers','enquiriesBySource','enquirySources', 'currentMonth', 'currentYear','selectedMonth','selectedYear','chartData','chartType', 'totalProjects','enquiriesByProjectType','projectTypes'));
    }

    function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        flash(trans('messages.cache_cleared_successfully'))->success();
        return back();
    }
}
