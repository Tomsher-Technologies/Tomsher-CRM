@extends('backend.layouts.app',['title' => 'Follow-up Calendar'])

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Follow-up Calendar</h4>
          
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Enquiry</label>
                    <select id="filter-enquiry" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                        <option value="">All</option>
                        @foreach($enquiries as $enquiry)
                            <option value="{{ $enquiry->id }}">
                                {{ $enquiry->enquiry_code }} - {{ $enquiry->customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @can('view_all_users_followups')
                    <div class="col-md-3">
                        <label>Users</label>
                        <select name="created_by" id="created_by" class="form-control form-control-sm aiz-selectpicker" data-live-search="true">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="col-md-3">
                    <label>Follow-up Type</label>
                    <select id="filter-type" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="Call">Call</option>
                        <option value="Email">Email</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Meeting">Meeting</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select id="filter-status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="rescheduled">Rescheduled</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
            </div>

            <div id="calendar-legend" style="margin-bottom: 10px;">
                <span class="legend-item">
                  <span class="color-dot" style="background-color: #079f19;"></span> <span>Completed</span>
                </span>
                <span class="legend-item">
                  <span class="color-dot" style="background-color: #c50c0c;"></span> <span>Due</span>
                </span>
                <span class="legend-item">
                  <span class="color-dot" style="background-color: #abe4fb;"></span> <span>Upcoming</span>
                </span>
                <span class="legend-item">
                  <span class="color-dot" style="background-color: #949494;"></span> <span>Canceled</span>
                </span>
                <span class="legend-item">
                  <span class="color-dot" style="background-color: #ffc519;"></span> <span>Rescheduled</span>
                </span>
            </div>
              
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Follow-up Modal -->

  <div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Follow-up Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered aiz-table">
                    <tbody>
                        <tr>
                            <td style="width:25%;"><strong>Enquiry</strong></td>
                            <td><span id="modal-enquiry"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Customer Info</strong></td>
                            <td><span id="modal-customer"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Follow-up Type</strong></td>
                            <td><span id="modal-type" ></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Sub-Type</strong></td>
                            <td><span id="modal-subtype"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Time</strong></td>
                            <td><span id="modal-time"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Pre-Follow-up Comment</strong></td>
                            <td><span id="modal-subject"></span></td>
                        </tr>
                        <tr id="modal-location-wrapper" style="display: none;">
                            <td style="width:25%;"><strong>Location</strong></td>
                            <td><span id="modal-location"></span></td>
                        </tr>
                        
                        <tr>
                            <td style="width:25%;"><strong>Status</strong></td>
                            <td><span id="modal-status" class="badge badge-inline"></span></td>
                        </tr>
                        <tr id="modal-participants-wrapper" style="display: none;">
                            <td style="width:25%;"><strong>Participants</strong></td>
                            <td><span id="modal-participants"></span></td>
                        </tr>
                        <tr>
                            <td style="width:25%;"><strong>Created By</strong></td>
                            <td><span id="modal-created_by"></span></td>
                        </tr>

                        <tr>
                            <td style="width:25%;"><strong>Post-Follow-up Comment</strong></td>
                            <td><span id="modal-comment"></span></td>
                        </tr>
                        
                       
                        <tr id="edit-followup-row">
                            <td>Change Status</td>
                            <td>
                                <div class="form-group col-12 d-flex">
                                    <label class="col-4">Follow-up Status </label>
                                    <select id="followup-status" class="form-control form-control-sm col-8">
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="canceled">Canceled</option>
                                        <option value="rescheduled">Rescheduled</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 d-flex">
                                    <label class="col-4">Post-Follow-up Comment </label>
                                    <textarea name="post_comment" id="post_comment" class="form-control form-control-sm col-8"  rows="3"></textarea>
                                </div>
                                <div class="form-group col-12" style="text-align: end;">
                                    <button id="update-status-btn" class="btn btn-success w-auto btn-sm" >Update Status</button>
                                </div>
                                
                            </td>
                        </tr>
                        
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>

    let calendar;
    const loggedInUserId = {{ auth()->id() }};
    const canEditFollowupCalendarStatus = {{ auth()->user()->can('edit_all_user_followups') ? 'true' : 'false' }};
    const canEditFollowups = {{ auth()->user()->can('edit_followups') ? 'true' : 'false' }};

    function loadCalendar(filters = {}) {
        if (calendar) calendar.destroy();

        calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            height: "auto",
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            },
            events: {
                url: '{{ route("followups.calendar.events") }}',
                method: 'GET',
                extraParams: filters
            },
            eventContent: function(info) {
                const viewType = info.view.type; 
                const iconHtml = info.event.extendedProps.icon ?? '';
                const title = info.event.title;
                const color = info.event.extendedProps.color;
                 // shorten title in month view
                const displayTitle = viewType === 'dayGridMonth' && title.length > 8
                    ? title.substring(0, 8) + '…'
                    : title;

                const timeText = info.timeText + ' - ' + displayTitle;

                const wrapper = document.createElement('div');
                wrapper.classList.add('d-flex', 'align-items-center');
                console.log(timeText);
                wrapper.innerHTML = `
                <span style="width:14px; height:14px; background:${color}; border-radius:50%; display:inline-block; margin-right:2px;margin-left: 4px;"></span>
                    ${iconHtml}
                    <div style="font-weight: bold;margin-left:4px;">${timeText}</div>
                `;

                return { domNodes: [wrapper] };
            },
            
            eventClick: function (info) {
                const event = info.event;

                let icon = '';
                switch (event.extendedProps.followup_type) {
                    case 'call': icon = '<i class="las fs-18 la-phone text-primary me-1"></i>'; break;
                    case 'email': icon = '<i class="las fs-18 la-envelope text-danger me-1"></i>'; break;
                    case 'whatsapp': icon = '<i class="lab fs-18 la-whatsapp text-success me-1"></i>'; break;
                    case 'meeting': icon = '<i class="las fs-18 la-handshake text-warning me-1"></i>'; break;
                }

                document.getElementById('modal-subject').innerHTML = event.extendedProps.subject;
                document.getElementById('modal-enquiry').innerHTML = event.extendedProps.enquiry;
                document.getElementById('modal-customer').innerHTML = event.extendedProps.customer;
                document.getElementById('modal-type').innerHTML = icon +' '+ event.extendedProps.type;
                document.getElementById('modal-subtype').innerHTML = event.extendedProps.sub_type;
                
                document.getElementById('modal-created_by').innerHTML = event.extendedProps.created_by;
                document.getElementById('modal-comment').innerHTML = event.extendedProps.post_comment;
                
                if(event.end != null){
                    document.getElementById('modal-time').innerHTML = formatDateTime(event.start)+' <strong>&nbsp; to &nbsp;</strong> '+formatDateTime(event.end);
                }else{
                    document.getElementById('modal-time').innerHTML = formatDateTime(event.start);
                }
                var postCommentField = document.getElementById('post_comment');
                if (postCommentField) {
                    postCommentField.value = event.extendedProps.post_comment || '';
                }

                if (event.extendedProps.type.toLowerCase() === 'meeting') {
                    document.getElementById('modal-location-wrapper').style.display = 'contents';
                    document.getElementById('modal-participants-wrapper').style.display = 'contents';
                    document.getElementById('modal-location').innerHTML = event.extendedProps.location;
                    document.getElementById('modal-participants').innerHTML = event.extendedProps.participants;
                } else {
                    document.getElementById('modal-location-wrapper').style.display = 'none';
                    document.getElementById('modal-participants-wrapper').style.display = 'none';
                }

                const statusEl = document.getElementById('modal-status');
                const status = event.extendedProps.status;

                statusEl.textContent = status;

                // Set badge color
                if (status === 'Completed') {
                    statusEl.className = 'badge  badge-inline completed';
                } else if (status === 'Pending') {
                    const followupTime = new Date(event.start); // This is the followup time
                    const now = new Date();
                    if (followupTime > now) {
                        statusEl.className = 'badge  badge-inline pending-upcoming';
                    }else{
                        statusEl.className = 'badge  badge-inline pending-due';
                    }
                } else if (status === 'Canceled') {
                    statusEl.className = 'badge  badge-inline badge-secondary';
                } else if (status === 'Rescheduled') {
                    statusEl.className = 'badge  badge-inline badge-warning';
                }

                $('#followup-status').val(event.extendedProps.org_status); // pre-select current status
                const btnUpdate = document.getElementById('update-status-btn');
                if (btnUpdate) {
                    btnUpdate.setAttribute('data-followup-id',info.event.id);
                }
                
                const createdBy = event.extendedProps.created_by_user;

                const canEditPostComment =
                    canEditFollowupCalendarStatus ||
                    (canEditFollowups && createdBy == loggedInUserId);

                if (canEditPostComment) {
                    document.getElementById('edit-followup-row').style.display = '';
                } else {
                    document.getElementById('edit-followup-row').style.display = 'none';
                }
                // console.log(event.extendedProps.org_status);
                // console.log(info.event.id);

                new bootstrap.Modal(document.getElementById('followupModal')).show();
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            buttonText: {
                listWeek: 'Week' ,
                dayGridMonth : 'Month',
                today : 'Today'
            },
            
        });

        calendar.render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadCalendar();

        ['#filter-enquiry', '#filter-type', '#filter-status','#created_by'].forEach(id => {
            document.querySelector(id).addEventListener('change', function () {
                loadCalendar({
                    enquiry_id: document.getElementById('filter-enquiry').value,
                    type: document.getElementById('filter-type').value,
                    status: document.getElementById('filter-status').value,
                    created_by :  document.getElementById('created_by').value,
                });
            });
        });

    });

   
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'update-status-btn') {
            const followupId = e.target.getAttribute('data-followup-id');
            const newStatus = document.getElementById('followup-status').value;
            const post_comment = document.getElementById('post_comment').value;
            
            $.ajax({
                url: `/followups/${followupId}/update-status`,  // you'll define this route
                type: 'POST',
                data: {
                    status: newStatus,
                    post_comment:post_comment,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    document.getElementById('followupModal').classList.remove('show');
                    document.getElementById('followupModal').style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelector('.modal-backdrop')?.remove();
                    // Optionally refetch calendar events
                    AIZ.plugins.notify('success', response.message);
                    calendar.refetchEvents();
                },
                error: function(xhr) {
                    alert('Error updating status.');
                }
            });
        }
    });

    function formatDateTime(dateStr) {
        const date = new Date(dateStr);

        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const year = date.getFullYear();

        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12 || 12; // Convert 0 to 12
        hours = String(hours).padStart(2, '0');

        return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
    }

   
</script>
@endsection

@section('style')
<style>
    #calendar-legend {
        /* display: flex; */
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        text-align: end;
    }
  
    .legend-item {
        /* display: flex; */
        align-items: center;
        gap: 6px;
        margin-left: 10px;
    }
  
    .color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
    
@endsection

