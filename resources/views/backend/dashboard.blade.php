@extends('backend.layouts.app',['title' => 'Dashboard'])

@section('content')
    
    {{-- @if (Auth::user()->user_type == 'admin' || (Auth::user()->user_type == 'staff' && Auth::user()->hasPermissionTo('some-permission'))) --}}
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
        <div class="row gutters-10 card">
        
            <div class="col-lg-12 card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                    </div>
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="row col-md-6 ">
                        <div class="col-md-4">
                            <select name="month" class="form-control form-control-sm">
                                <option value="">All Months</option>
                                @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="year" class="form-control form-control-sm">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 text-end d-flex">
                            <button type="submit" class="btn btn-primary ">Filter</button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger ml-1" >Cancel</a>
                        </div>
                    </form>
                </div>
                <div class="row gutters-10">
                    {{-- Total Customers Block --}}
                    <div class="col-md-2">
                        <div class="card shadow" style="background: linear-gradient(to right, #008080, #00bfae); color: #fff; border-radius: 12px;">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Customers</h6>
                                <h5>{{ $totalCustomers }}</h5>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    {{-- Total Enquiries Block --}}
                    <div class="col-md-2">
                        <div class="card shadow" style="background: linear-gradient(to right, #8348bd, #b659c2); color: #fff; border-radius: 12px;">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Enquiries</h6>
                                <h5>{{ $totalEnquiries }}</h5>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    {{-- Total Projects Block --}}
                    <div class="col-md-2">
                        <div class="card shadow" style="background: linear-gradient(to right, #c54a56e7, #e37391); color: #fff; border-radius: 12px;">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Projects</h6>
                                <h5>{{ $totalProjects }}</h5>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- @endif --}}

    <div class="row d-flex align-items-stretch">
        <div class="col-md-12 d-flex">
            <div class="card w-100">
                <div class="card-header">
                    <h6>
                        Enquiries by Status
                    </h6>
                </div>
                <div class="card-body col-lg-12">
                    <div class="row gutters-10">
                        {{-- Status Wise Blocks --}}
                        @foreach($statusDetails as $key => $status)
                            <div class="col-md-2">
                                <div class="card shadow" style="background-color: {{ $status['bg'] }}; color: {{ $status['list_color'] }}; border-radius: 12px;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $status['label'] }}</h6>
                                        <h5>{{ $statusCounts[$key] ?? 0 }}</h5>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
                                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month-Year Filter -->
        @php
            if ($selectedMonth || $selectedYear) {
                if ($selectedMonth && $selectedYear) {
                    // If both month and year are selected, set title as month-year
                    $enquiriesTitle = \Carbon\Carbon::create()->month($selectedMonth)->format('F') . ' - ' . $selectedYear;
                } elseif ($selectedMonth) {
                    // If only month is selected
                    $enquiriesTitle = \Carbon\Carbon::create()->month($selectedMonth)->format('F');
                } elseif ($selectedYear) {
                    // If only year is selected
                    $enquiriesTitle = $selectedYear;
                }
            } else {
                // If no filter is selected
                $enquiriesTitle = 'All Time';
            }
           
            // Extracting source names and the count of enquiries from existing data
            $sourceNames = $enquirySources->pluck('name', 'id')->toArray();
            // Filter out the sources with zero counts in PHP
            $filteredEnquiriesBySource = array_filter($enquiriesBySource, function($count) {
                return $count > 0;
            });
    
            $filteredSourceNames = array_intersect_key($sourceNames, $filteredEnquiriesBySource);

        @endphp
       

    <div class="row d-flex align-items-stretch">
        <div class="col-md-6 d-flex">
            <div class="card w-100">
                <div class="card-header">
                    <h6>
                        Enquiries by Source <small class="text-muted">({{$enquiriesTitle}})</small>
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mt-2 aiz-table">
                        <thead>
                            <tr>
                                <th>Enquiry Source</th>
                                <th class="text-center">Enquiries</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enquiriesBySource as $sourceId => $count)
                                @php
                                    $source = $enquirySources->firstWhere('id', $sourceId);
                                @endphp
                                <tr>
                                    <td>{{ $source ? $source->name : 'Unknown' }}</td>
                                    <td class="text-center">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card w-100">
                <div class="card-header">
                    <h6>
                        Enquiries by Source Chart<small class="text-muted">({{$enquiriesTitle}})</small>
                    </h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    @if (count($filteredSourceNames) > 0)
                        <canvas id="enquirySourcePieChart" style="max-height: 400px;"></canvas>
                    @else
                        <p class="text-muted mb-0">No Enquiry Data Available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex align-items-stretch">
        <div class="col-md-12 d-flex">
            <div class="card w-100">
                <div class="card-header">
                    <h6>
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
                        
                        Enquiries - Total, Pending & Contacted <small class="text-muted">({{$enquiriesBarTitle}})</small>
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="enquiryChart" style="max-height: 500px;min-height: 500px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex align-items-stretch">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6>
                        Enquiries by Project Type <small class="text-muted">({{ $enquiriesTitle}})</small>
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="projectTypeChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    
        <div class="col-md-6 d-flex">
            <div class="card w-100">
                <div class="card-header">
                    <h6>
                        Enquiries By Milestone <small class="text-muted">({{$enquiriesTitle}})</small>
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="enquiryStatusChart" style="max-height: 400px;min-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>
   
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <script type="text/javascript">

        // Function to generate unique colors dynamically using HSL
        function generateUniqueColors(total) {
            const colors = [];
            const hueStep = 360 / total; // Spread hues evenly
            for (let i = 0; i < total; i++) {
                const hue = i * hueStep;
                colors.push(`hsl(${hue}, 70%, 50%)`); // HSL format (Hue, Saturation, Lightness)
            }
            return colors;
        }

        // In your script
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('enquirySourcePieChart').getContext('2d');
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
                                position: 'top',
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

        const ctxEn = document.getElementById('enquiryChart').getContext('2d');

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
                }
            }
        });

        var ctxStat = document.getElementById('enquiryStatusChart').getContext('2d');

        var statusCounts = @json($statusCounts); // you already have this
        
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
                            color: '#fff', // Text color
                            formatter: (value) => {
                                return value === 0 ? '' : value; // If value is 0, don't display it
                            },
                            font: {
                                weight: 'bold',
                                size: 12
                            }
                        }
                    }
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





        const ctxProjectType = document.getElementById('projectTypeChart').getContext('2d');
    
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
                    borderWidth: 1
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
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    </script>
@endsection
