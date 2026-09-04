@extends('backend.layouts.app',['title' => 'Dashboard'])

@section('content')
    
    @php
        $months = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];

        $years = range(now()->year - 10, now()->year); // Display the last 5 years
        $statusDetails = getEnquiryStatuses();
    @endphp
    <div class="row d-flex align-items-stretch dashboard-overview-row">
        <div class="col-md-12 d-flex">
            <div class="card w-100 dashboard-overview-card">
                <div class="card-header dashboard-overview-header">
                    <div class="dashboard-title-block w-25">
                        <span class="dashboard-kicker">Overview</span>
                        <h5>Dashboard</h5>
                    </div>
                    <div class="dashboard-filter-panel">
                        @canany(['view_total_counts', 'view_enquiries_by_current_status','view_enquiries_by_source','view_enquiries_by_project_type','view_enquiries_by_milestone'])
                            <form method="GET" action="{{ route('admin.dashboard') }}" class="dashboard-filter-form">
                                <div class="dashboard-filter-field">
                                    <select name="user_id" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                                        <option value="">All Users</option>

                                        @can('view_all_users_filter')
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ auth()->id() }}"
                                                {{ request('user_id') == auth()->id() ? 'selected' : '' }}>
                                                {{ auth()->user()->name }}
                                            </option>
                                        @endcan

                                    </select>
                                </div>
                                
                                <div class="dashboard-filter-field">
                                    <select name="source_mode" id="source_mode" class="form-control form-control-sm">
                                        <option value="">All Source Modes</option>
                                        <option value="inhouse" {{ request('source_mode') == "inhouse" ? 'selected' : '' }}>
                                            Inhouse Lead
                                        </option>
                                        <option value="self" {{ request('source_mode') == "self" ? 'selected' : '' }}>
                                            Self Lead
                                        </option>
                                        <option value="cross_up_sell" {{ request('source_mode') == "cross_up_sell" ? 'selected' : '' }}>
                                            Cross/Up Sell
                                        </option>
                                    </select>
                                </div>

                                <div class="dashboard-filter-field dashboard-filter-date">
                                    <input type="text" class="aiz-date-range form-control form-control-sm" value="{{ request('date_range') ?? now()->startOfMonth()->format('d-m-Y').' to '.now()->endOfMonth()->format('d-m-Y') }}" name="date_range" placeholder="Filter by date" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                                </div>
                            
                                <div class="dashboard-filter-actions">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">Cancel</a>
                                </div>
                            </form>
                        @endcanany
                    </div>
                </div>
                <div class="card-body dashboard-overview-body">
                    
                    @can('view_total_counts')
                        <div class="dashboard-metrics-grid">
                            {{-- Total Customers Block --}}
                            <a href="{{ route('customers.index', array_filter([
                                'user_id' => request('user_id') ?: null,
                                'date_range' => request('date_range') ?: now()->startOfMonth()->format('d-m-Y').' to '.now()->endOfMonth()->format('d-m-Y'),
                                'ntc' => 1
                            ])) }}" class="dashboard-metric-card">
                                <div class="dashboard-metric-inner dashboard-metric-customers-inner">
                                    <div class="dashboard-metric-icon-wrap">
                                        <img src="{{ asset('assets/img/3d_customers.png') }}" class="dashboard-metric-3d-icon" alt="New Customers">
                                    </div>
                                    <div class="dashboard-metric-copy">
                                        <span class="dashboard-metric-title">New Customers</span>
                                        <strong class="dashboard-metric-value">{{ $totalCustomers }}</strong>
                                    </div>
                                </div>
                            </a>
                            {{-- Total Enquiries Block --}}
                            <a href="{{ preg_replace('/(%5B|\[)\d+(%5D|\])/i', '$1$2', route('enquiries.index', array_filter([
                                'enquiry_date' => request('date_range') ?: now()->startOfMonth()->format('d-m-Y').' to '.now()->endOfMonth()->format('d-m-Y'),
                                'added_by' => request('user_id') ? [request('user_id')] : null,
                                'source_mode' => request('source_mode') ? [request('source_mode')] : null
                            ]))) }}" class="dashboard-metric-card">
                                <div class="dashboard-metric-inner dashboard-metric-data-inner">
                                    <div class="dashboard-metric-icon-wrap">
                                        <img src="{{ asset('assets/img/3d_enquiries.png') }}" class="dashboard-metric-3d-icon" alt="Total Enquiries">
                                    </div>
                                    <div class="dashboard-metric-copy">
                                        <span class="dashboard-metric-title">Total Enquiries</span>
                                        <strong class="dashboard-metric-value">{{ $totalEnquiries }}</strong>
                                    </div>
                                </div>
                            </a>
                            {{-- Zoho Enquiries Block --}}
                            <a href="{{ preg_replace('/(%5B|\[)\d+(%5D|\])/i', '$1$2', route('enquiries.index', array_filter([
                                'enquiry_date' => request('date_range') ?: now()->startOfMonth()->format('d-m-Y').' to '.now()->endOfMonth()->format('d-m-Y'),
                                'added_by' => request('user_id') ? [request('user_id')] : null,
                                'source_mode' => request('source_mode') ? [request('source_mode')] : null
                            ]) + ['enquiry_source_id' => $zohoSourceIds])) }}" class="dashboard-metric-card">
                                <div class="dashboard-metric-inner dashboard-metric-enquiries-inner">
                                    <div class="dashboard-metric-icon-wrap">
                                        <img src="{{ asset('assets/img/3d_enquiries.png') }}" class="dashboard-metric-3d-icon" alt="Zoho Enquiries">
                                    </div>
                                    <div class="dashboard-metric-copy">
                                        <span class="dashboard-metric-title">Zoho Enquiries</span>
                                        <strong class="dashboard-metric-value">{{ $zohoEnquiries }}</strong>
                                    </div>
                                </div>
                            </a>
                            {{-- Total Data Block --}}
                            {{-- <div class="dashboard-metric-card">
                                <div class="dashboard-metric-inner dashboard-metric-data-inner">
                                    <div class="dashboard-metric-icon-wrap">
                                        <img src="{{ asset('assets/img/3d_data.png') }}" class="dashboard-metric-3d-icon" alt="Total Data">
                                    </div>
                                    <div class="dashboard-metric-copy">
                                        <span class="dashboard-metric-title">Total Data</span>
                                        <strong class="dashboard-metric-value">{{ $totalData }}</strong>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- Total Followups Block --}}
                            <a href="{{ preg_replace('/(%5B|\[)\d+(%5D|\])/i', '$1$2', route('followups.index', array_filter([
                                'date_range' => request('date_range') ?: now()->startOfMonth()->format('d-m-Y').' to '.now()->endOfMonth()->format('d-m-Y'),
                                'created_by' => request('user_id'),
                                'source_mode' => request('source_mode') ? [request('source_mode')] : null
                            ]))) }}" class="dashboard-metric-card">
                                <div class="dashboard-metric-inner dashboard-metric-followups-inner">
                                    <div class="dashboard-metric-icon-wrap">
                                        <img src="{{ asset('assets/img/3d_followups.png') }}" class="dashboard-metric-3d-icon" alt="Total Followups">
                                    </div>
                                    <div class="dashboard-metric-copy">
                                        <span class="dashboard-metric-title">Total Followups</span>
                                        <strong class="dashboard-metric-value">{{ $totalFollowups }}</strong>
                                    </div>
                                </div>
                            </a>
                            {{-- Total Projects Block --}}
                            <a href="{{ preg_replace('/(%5B|\[)\d+(%5D|\])/i', '$1$2', route('projects.index', array_filter([
                                'date_range' => request('date_range') ?: now()->startOfMonth()->format('d-m-Y').' to '.now()->endOfMonth()->format('d-m-Y'),
                                'created_by' => request('user_id'),
                                'source_mode' => request('source_mode') ? [request('source_mode')] : null
                            ]))) }}" class="dashboard-metric-card">
                                <div class="dashboard-metric-inner dashboard-metric-projects-inner">
                                    <div class="dashboard-metric-icon-wrap">
                                        <img src="{{ asset('assets/img/3d_projects.png') }}" class="dashboard-metric-3d-icon" alt="Total Projects">
                                    </div>
                                    <div class="dashboard-metric-copy">
                                        <span class="dashboard-metric-title">Total Projects</span>
                                        <strong class="dashboard-metric-value">{{ $totalProjects }}</strong>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    
    <div class="row d-flex">
        @can('view_enquiries_by_current_status')
            @php
                $currentStatusChartData = [];
                foreach ($statusDetails as $key => $status) {
                    $count = $statusCounts[$key] ?? 0;
                    if ($count > 0) {
                        $currentStatusChartData[] = [
                            'label' => $status['label'],
                            'value' => $count,
                            'bg' => $status['bg'] ?: '#eef2f6',
                            'color' => $status['list_color'] ?: '#111827'
                        ];
                    }
                }
            @endphp
           
            <div class="col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header  dashboard-section-header">
                        <h6>Enquiries by Current Status</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="padding: 12px;">
                        @if (count($currentStatusChartData) > 0)
                            <div style="width: 100%; max-width: 380px; margin: 0 auto;">
                                <canvas id="enquiryCurrentStatusChart" style=""></canvas>
                            </div>
                        @else
                            <div class="d-flex justify-content-center align-items-center" style="">
                                <p class="text-muted mb-0">No Enquiry Data Available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endcan

        @can('view_enquiries_by_milestone')
            <div class="col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header  dashboard-section-header">
                        <h6>
                            Enquiries By Milestone 
                        </h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="padding: 12px;">
                        <div style="width: 100%; max-width: 380px; margin: 0 auto;">
                            <canvas id="enquiryStatusChart" style=""></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <!-- Month-Year Filter -->
        @php
            
            // Extracting source names and the count of enquiries from existing data
            $sourceNames = $enquirySources->pluck('name', 'id')->toArray();
            // Filter out the sources with zero counts in PHP
            $filteredEnquiriesBySource = array_filter($enquiriesBySource, function($count) {
                return $count > 0;
            });
    
            $filteredSourceNames = array_intersect_key($sourceNames, $filteredEnquiriesBySource);

        @endphp
       

    @can('view_enquiries_by_source')
        <div class="row d-flex align-items-stretch">
            <div class="col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header  dashboard-section-header">
                        <h6>
                            Enquiries by Source Mode
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="enquirySourceModePieChart" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex">
                <div class="card w-100">
                    <div class="card-header  dashboard-section-header">
                        <h6>
                            Enquiries by Source Chart
                        </h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="padding: 12px;">
                        @if (count($filteredSourceNames) > 0)
                            <canvas id="enquirySourcePieChart" style="max-height: 400px;"></canvas>
                        @else
                            <p class="text-muted mb-0">No Enquiry Data Available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @php
        if ($selectedMonth || $selectedYear) {
            if ($selectedMonth && $selectedYear) {
                $enquiriesBarTitle = \Carbon\Carbon::create()->month($selectedMonth)->format('F') . ' - ' . $selectedYear;
            } elseif ($selectedMonth) {
                $enquiriesBarTitle = \Carbon\Carbon::create()->month($selectedMonth)->format('F');
            } elseif ($selectedYear) {
                $enquiriesBarTitle = $selectedYear;
            }
        } else {
            $enquiriesBarTitle = \Carbon\Carbon::now()->format('F') . ' - ' . \Carbon\Carbon::now()->format('Y');
        }

    @endphp  

    <div class="row d-flex align-items-stretch">
        @can('view_enquiries_by_project_type')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header  dashboard-section-header">
                        <h6>
                            Enquiries by Project Type 
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="projectTypeChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        @endcan
    
       
    </div>

    @can('view_enquiries_total')
        <div class="row d-flex align-items-stretch">
            <div class="col-md-12 d-flex">
                <div class="card w-100 dashboard-graph-card"  id="enquiries-total-section">
                    <div class="card-header dashboard-graph-header">
                        <div class="dashboard-graph-title">
                            <h6>Enquiry Activity Overview</h6>
                            <span class="dashboard-kicker">({{$enquiriesBarTitle}})</span>
                        </div>

                        <form method="GET" action="{{ route('admin.dashboard') }}#enquiries-total-section" class="dashboard-graph-filter-form">
                            <div class="dashboard-graph-filter-field">
                                <select name="user_id_graph" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                                    <option value="">All Users</option>

                                    @can('view_all_users_filter')
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id_graph') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ auth()->id() }}"
                                            {{ request('user_id_graph') == auth()->id() ? 'selected' : '' }}>
                                            {{ auth()->user()->name }}
                                        </option>
                                    @endcan

                                </select>
                            </div>
                            <div class="dashboard-graph-filter-field">
                                <select name="source_mode_graph" id="source_mode_graph" class="form-control form-control-sm">
                                    <option value="">All Source Modes</option>
                                    <option value="inhouse" {{ request('source_mode_graph') == "inhouse" ? 'selected' : '' }}>
                                        Inhouse Lead
                                    </option>
                                    <option value="self" {{ request('source_mode_graph') == "self" ? 'selected' : '' }}>
                                        Self Lead
                                    </option>
                                    <option value="cross_up_sell" {{ request('source_mode_graph') == "cross_up_sell" ? 'selected' : '' }}>
                                        Cross/Up Sell
                                    </option>
                                </select>
                            </div>
                            <div class="dashboard-graph-filter-field">
                                <select name="month" class="form-control form-control-sm">
                                    <option value="">All Months</option>
                                    @foreach($months as $key => $month)
                                        <option value="{{ $key }}" {{ (request()->has('month') ? request('month') == $key : $key == now()->month) ? 'selected' : '' }}>
                                            {{ $month }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="dashboard-graph-filter-field">
                                <select name="year" class="form-control form-control-sm">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ (request()->has('year') ? request('year') == $year : $year == now()->year) ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="dashboard-graph-filter-actions">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('admin.dashboard') }}#enquiries-total-section" class="btn btn-light btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-body dashboard-graph-body">
                        <canvas id="enquiryChart" style="max-height: 500px;min-height: 500px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endcan
   
@endsection

@section('style')
<style>
    .dashboard-overview-row,
    .dashboard-status-row {
        /* margin-bottom: 18px; */
    }

    .dashboard-overview-card,
    .dashboard-status-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .dashboard-overview-card {
        overflow: visible;
    }

    .dashboard-overview-header,
    .dashboard-section-header {
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        padding: 16px 18px;
    }

    .dashboard-title-block h5,
    .dashboard-section-header h6 {
        color: #111827;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0;
        line-height: 1.2;
        margin: 0;
    }

    .dashboard-section-header h6 {
        font-size: 16px;
    }

    .dashboard-kicker {
        color: #6b7280;
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0;
        line-height: 1;
        margin-bottom: 5px;
        text-transform: uppercase;
        margin-top: 3%;
    }

    .dashboard-filter-panel {
        flex: 1;
        position: relative;
        z-index: 5;
    }

    .dashboard-overview-header,
    .dashboard-filter-form,
    .dashboard-filter-field {
        overflow: visible;
    }

    .dashboard-filter-field .dropdown-menu {
        z-index: 1050;
    }

    .dashboard-filter-form {
        align-items: center;
        display: grid;
        gap: 8px;
        grid-template-columns: minmax(145px, 1fr) minmax(145px, 1fr) minmax(220px, 1.25fr) auto;
        justify-content: end;
        margin: 0;
        width: 100%;
    }

    .dashboard-filter-field .form-control,
    .dashboard-filter-actions .btn {
        border-radius: 6px;
        min-height: 36px;
    }

    .dashboard-filter-field .form-control {
        background-color: #ffffff;
        border-color: #d1d5db;
        color: #374151;
        font-weight: 500;
    }

    .dashboard-filter-field .form-control:focus {
        background-color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .dashboard-filter-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        white-space: nowrap;
    }

    .dashboard-filter-actions .btn {
        font-weight: 600;
        padding-left: 16px;
        padding-right: 16px;
    }

    .dashboard-filter-actions .btn-light {
        background: #ffffff;
        border-color: #d1d5db;
        color: #374151;
    }

    .dashboard-overview-body,
    .dashboard-status-body {
        background: #f9fafb;
        padding: 16px;
    }

    .dashboard-metrics-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .dashboard-metric-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        min-height: 110px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
        display: block;
        text-decoration: none !important;
    }

    .dashboard-metric-card::before,
    .dashboard-metric-card::after {
        content: none;
    }

    .dashboard-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        text-decoration: none !important;
    }

    .dashboard-status-tile:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .dashboard-metric-inner {
        border-radius: 8px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }

    .dashboard-metric-customers-inner {
        background: #e8f7ff;
    }

    .dashboard-metric-enquiries-inner {
        background: #e6f9f0;
    }

    .dashboard-metric-data-inner {
        background: #f0ebff;
    }

    .dashboard-metric-followups-inner {
        background: #ffece3;
    }

    .dashboard-metric-projects-inner {
        background: #e3fbf5;
    }

    .dashboard-metric-icon-wrap {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-metric-3d-icon {
        width: 100%;
        height: 100%;
        object-fit: contain;
        mix-blend-mode: multiply;
    }

    .dashboard-metric-copy {
        min-width: 0;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dashboard-metric-title {
        color: #4a5568;
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        letter-spacing: 0;
        line-height: 1.25;
        margin: 0;
    }

    .dashboard-metric-value {
        color: #4f46e5;
        display: block;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: 0;
        line-height: 1;
        margin-top: 4px;
    }

    .dashboard-status-content span {
        color: #374151;
    }

    .dashboard-status-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }

    .dashboard-status-tile {
        align-items: center;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-left: 5px solid var(--status-bg);
        border-radius: 8px;
        display: grid;
        gap: 12px;
        grid-template-columns: minmax(0, 1fr) auto;
        min-height: 72px;
        overflow: hidden;
        padding: 14px 14px 14px 16px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-status-tile::before {
        content: none;
    }

    .dashboard-status-tile::after {
        content: none;
    }

    .dashboard-status-count {
        align-items: center;
        background: var(--status-bg);
        border-radius: 8px;
        color: var(--status-color);
        display: flex;
        flex: 0 0 auto;
        justify-content: center;
        min-width: 54px;
        padding: 9px 12px;
    }

    .dashboard-status-content {
        align-items: center;
        display: flex;
        gap: 10px;
        min-width: 0;
        padding: 0;
    }

    .dashboard-status-marker {
        background: var(--status-bg);
        border-radius: 999px;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.04);
        flex: 0 0 12px;
        height: 12px;
        width: 12px;
    }

    .dashboard-status-content span {
        color: #374151;
        display: block;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0;
        line-height: 1.35;
        word-break: break-word;
    }

    .dashboard-status-count strong {
        color: var(--status-color);
        display: block;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1;
        margin: 0;
    }

    #enquiries-total-section {
        scroll-margin-top: 24px;
    }

    .dashboard-graph-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        overflow: visible;
    }

    .dashboard-graph-header {
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        padding: 16px 18px;
    }

    .dashboard-graph-title {
        flex: 0 0 300px;
        /* min-width: 30%; */
        margin-top: 1%;
    }

    .dashboard-graph-title h6 {
        color: #111827;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0;
        line-height: 1.25;
        margin: 0 0 2px;
    }

    .dashboard-graph-filter-form {
        align-items: center;
        display: grid;
        flex: 1;
        gap: 8px;
        grid-template-columns: minmax(145px, 1fr) minmax(145px, 1fr) minmax(120px, 0.75fr) minmax(120px, 0.75fr) auto;
        margin: 0;
    }

    .dashboard-graph-filter-field,
    .dashboard-graph-filter-form {
        overflow: visible;
    }

    .dashboard-graph-filter-field .form-control,
    .dashboard-graph-filter-actions .btn {
        border-radius: 6px;
        min-height: 36px;
    }

    .dashboard-graph-filter-field .form-control {
        border-color: #d1d5db;
        color: #374151;
        font-weight: 500;
    }

    .dashboard-graph-filter-field .dropdown-menu {
        z-index: 1050;
    }

    .dashboard-graph-filter-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        white-space: nowrap;
    }

    .dashboard-graph-filter-actions .btn {
        font-weight: 600;
        padding-left: 16px;
        padding-right: 16px;
    }

    .dashboard-graph-filter-actions .btn-light {
        background: #ffffff;
        border-color: #d1d5db;
        color: #374151;
    }

    .dashboard-graph-body {
        background: #ffffff;
        padding: 18px;
    }

    @media (max-width: 1199.98px) {
        .dashboard-overview-header {
            align-items: stretch;
            flex-direction: column;
        }

        .dashboard-filter-form,
        .dashboard-graph-filter-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-filter-date,
        .dashboard-filter-actions,
        .dashboard-graph-filter-actions {
            grid-column: span 2;
        }

        .dashboard-graph-header {
            align-items: stretch;
            flex-direction: column;
        }

        .dashboard-graph-title {
            flex: none;
            min-width: 0;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-overview-header,
        .dashboard-section-header,
        .dashboard-overview-body,
        .dashboard-status-body {
            padding: 14px;
        }

        .dashboard-filter-form,
        .dashboard-graph-filter-form,
        .dashboard-metrics-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-filter-date,
        .dashboard-filter-actions,
        .dashboard-graph-filter-actions {
            grid-column: auto;
        }

        .dashboard-filter-actions,
        .dashboard-graph-filter-actions {
            justify-content: stretch;
        }

        .dashboard-filter-actions .btn,
        .dashboard-graph-filter-actions .btn {
            flex: 1;
        }
    }
</style>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <script type="text/javascript">

        const baseColors = [
                    '#FF6384', // Pink
                    '#36A2EB', // Blue
                    '#FFCE56', // Yellow
                    '#9966FF', // Purple
                    '#E74C3C', // Red
                    '#2ECC71', // Green
                    '#34495E', // Dark Blue Grey
                    '#9B59B6', // Violet
                    '#7F8C8D',// Grey
                    '#E67E22', // Dark Orange
                    '#16A085', // Dark Teal
                    '#8E44AD', // Deep Purple
                    '#27AE60', // Deep Green
                    '#D35400', // Burnt Orange
                    '#C0392B', // Dark Red
                    // '#4BC0C0', // Teal
                    // '#F1C40F', // Gold
                    // '#1ABC9C', // Aqua
                    // '#FF9F40', // Orange
                ];


        function generateUniqueColors(total) {
            const colors = [];

            for (let i = 0; i < total; i++) {
                if (i < baseColors.length) {
                    colors.push(baseColors[i]);
                } else {
                    const hue = (i * 137.508) % 360;
                    colors.push(`hsl(${hue}, 70%, 50%)`);
                }
            }

            return colors;
        }

        document.addEventListener('DOMContentLoaded', function () {

            const canvascurrentStatus = document.getElementById('enquiryCurrentStatusChart'); 
            if (canvascurrentStatus) { 
                const ctx = canvascurrentStatus.getContext('2d');
                if (ctx) {
                    const currentStatusLabels = {!! json_encode(array_column($currentStatusChartData ?? [], 'label')) !!};
                    const currentStatusData = {!! json_encode(array_column($currentStatusChartData ?? [], 'value')) !!};
                    const currentStatusBgs = {!! json_encode(array_column($currentStatusChartData ?? [], 'bg')) !!};
                    const currentStatusTextColors = {!! json_encode(array_column($currentStatusChartData ?? [], 'color')) !!};

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: currentStatusLabels,
                            datasets: [{
                                data: currentStatusData,
                                backgroundColor: currentStatusBgs,
                                borderColor: '#fff',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                datalabels: {
                                    color: (context) => {
                                        const index = context.dataIndex;
                                        return currentStatusTextColors[index] || '#111827';
                                    },
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    },
                                    formatter: (value) => {
                                        return value;
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.raw;
                                        }
                                    }
                                }
                            },
                        },
                        plugins: [ChartDataLabels]
                    });
                }
            }

            const canvasenquirySource = document.getElementById('enquirySourcePieChart'); 
            if (canvasenquirySource) { 
                const ctx = canvasenquirySource.getContext('2d');
                if (ctx) {
                    const enquirySourceNames = {!! json_encode(array_values($filteredSourceNames)) !!};
                    const enquirySourceCounts = {!! json_encode(array_values($filteredEnquiriesBySource)) !!};

                    const backgroundColors = generateUniqueColors(enquirySourceNames.length);

                    const enquirySourcePieChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: enquirySourceNames,
                            datasets: [{
                                label: 'Total Enquiries',
                                data: enquirySourceCounts,
                                backgroundColor: backgroundColors,
                                borderColor: '#fff',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    },
                                    formatter: (value, context) => {
                                        return value; // shows the enquiry count inside the slice
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.raw;
                                        }
                                    }
                                }
                            },
                            aspectRatio: 1.5,
                        },
                        plugins: [ChartDataLabels]
                    });
                }
            }

            const sourceModeData = @json($enquiriesBySourceMode);

            const labelssourceMode = Object.keys(sourceModeData);
            const datasourceMode = Object.values(sourceModeData);
            
            const canvasenquirySourceMode = document.getElementById('enquirySourceModePieChart'); 
            if (canvasenquirySourceMode) { 
                const ctxMode = canvasenquirySourceMode.getContext('2d');
                if (ctxMode) {
                    new Chart(ctxMode, {
                        type: 'pie',
                        data: {
                            labels: labelssourceMode.map(label =>
                                label.charAt(0).toUpperCase() + label.slice(1)
                            ),
                            datasets: [{
                                data: datasourceMode,
                                backgroundColor: [
                                    '#4aadf7',
                                    '#48e158',
                                    '#F59E0B'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    },
                                    formatter: (value, context) => {
                                        return value; // shows the enquiry count inside the slice
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.raw;
                                        }
                                    }
                                }
                            },
                            aspectRatio: 1.5,
                        },
                        plugins: [ChartDataLabels]
                    });
                }
            }
        });

        var chartData = @json($chartData);  // Getting the data from the controller

        // Labels (Days, Months, or Years)
        var labels = Object.keys(chartData); 


        // Data for Total, Pending, and Contacted Enquiries
        var totalData = [];
        var pendingData = [];
        var contactedData = [];

        // Loop through the chartData to push the respective counts
        labels.forEach(function(label) {
            totalData.push(chartData[label].total);
            pendingData.push(chartData[label].pending);
            contactedData.push(chartData[label].contacted);
        });

        const canvasenquiryChart = document.getElementById('enquiryChart');
        if (canvasenquiryChart) {
            const ctxEn = canvasenquiryChart.getContext('2d');
            
            var enquiryChart = new Chart(ctxEn, {
                type: 'bar',  // You can use 'bar', 'line', or other types based on your preference
                data: {
                    labels: labels,  // The days, months, or years
                    datasets: [{
                        label: 'Total Enquiries',
                        data: totalData,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',  // Blue for total
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Pending Enquiries',
                        data: pendingData,
                        backgroundColor: 'rgba(255, 193, 7, 0.5)',  // Yellow for pending
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Contacted Enquiries',
                        data: contactedData,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',  // Green for contacted
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ $chartType == "monthwise" ? "Month" : ($chartType == "yearwise_for_month" ? "Year" : "Day") }}'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Enquiries'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        tooltip: {
                            enabled: true
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#475569',
                            font: {
                                weight: 'bold',
                                size: 9
                            },
                            formatter: (value) => {
                                return value === 0 ? '' : value; // hide 0 values to avoid clutter
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
        

        

        var statusCounts = @json($statusCountsMilestone); // you already have this
        
        var statusLabels = {
            @foreach($statusDetails as $key => $status)
                '{{ e($key) }}': '{{ e($status['label']) }}'{{ !$loop->last ? ',' : '' }}
            @endforeach
        };

        var backgroundColors = {
            @foreach($statusDetails as $key => $status)
                '{{ e($key) }}': '{{ e($status['bg']) }}'{{ !$loop->last ? ',' : '' }}
            @endforeach
        };
       
        var filteredLabels = [];
        var filteredData = [];
        var filteredbackgroundColors = [];

        if(statusCounts.length != 0){
            for (var status in statusCounts) {
                if (statusLabels[status]) {
                    filteredLabels.push(statusLabels[status]);
                    filteredData.push(statusCounts[status]);
                    filteredbackgroundColors.push(backgroundColors[status]);
                }
            }
        }else{
            for (var status in statusLabels) {
                if (statusLabels[status]) {
                    filteredLabels.push(statusLabels[status]);
                    // filteredData.push(0);
                    filteredbackgroundColors.push(backgroundColors[status]);
                }
            }
        }
        
        const canvasenquiryStatus = document.getElementById('enquiryStatusChart'); 
        if (canvasenquiryStatus) { 
            const ctxStat = canvasenquiryStatus.getContext('2d');

            if (filteredData.length > 0) {
                
                new Chart(ctxStat, {
                    type: 'doughnut',
                    data: {
                        labels: filteredLabels,
                        datasets: [{
                            data: filteredData,
                            backgroundColor: filteredbackgroundColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.raw || 0;
                                        return label + ': ' + value;
                                    }
                                }
                            },
                            datalabels: {
                                display: true, // Show labels
                                color: '#000', // Text color
                                formatter: (value) => {
                                    return value === 0 ? '' : value; // If value is 0, don't display it
                                },
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });
            } else {
                console.log(filteredLabels);
                console.log(filteredbackgroundColors);

                new Chart(ctxStat, {
                    type: 'doughnut',
                    data: {
                        labels: filteredLabels, 
                        datasets: [{
                            data: [0,0,0,0,0,0,0,0], // Just a dummy value to draw the circle
                            backgroundColor: filteredbackgroundColors, // Light gray
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        cutout: '70%', // Inner radius to create the ring
                        plugins: {
                            legend: {
                                display: true // Hide legend
                            },
                            tooltip: {
                                enabled: false // Hide tooltip
                            }
                        }
                    }
                });
            }
        }


        const canvasprojectType = document.getElementById('projectTypeChart'); 
        if (canvasprojectType) { 
            const ctxProjectType = canvasprojectType.getContext('2d');

            // Project types and their enquiry counts
            const projectTypeLabels = @json(array_keys($enquiriesByProjectType)); // Project type names
            const projectTypeCounts = @json(array_values($enquiriesByProjectType)); // Enquiry counts

            // Colors for each project type
            const colors = ['#abe4fb'];

            // Create the bar chart
            new Chart(ctxProjectType, {
                type: 'bar',
                data: {
                    labels: projectTypeLabels,  // Project types (including "No Project Type")
                    datasets: [{
                        label: 'Enquiries',
                        data: projectTypeCounts, // Enquiry counts for each project type
                        backgroundColor: colors.slice(0, projectTypeCounts.length), // Limit the number of colors to match data count
                        borderColor: colors.slice(0, projectTypeCounts.length),
                        borderWidth: 1,
                        maxBarThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            enabled: true
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#475569',
                            font: {
                                weight: 'bold',
                                size: 11
                            },
                            formatter: (value) => {
                                return value;
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    </script>
@endsection
