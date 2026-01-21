<ul class="timeline">
    @foreach($data->statusHistories()->orderBy('status_date','asc')->get() as $history)
        <li class="timeline-item">
            <span class="timeline-dot"></span>

            <div class="timeline-content">
                <h6 class="mb-1 fw-bold">
                    {{ ucwords(str_replace('_',' ', $history->status)) }}
                </h6>

                <div class="text-muted small">
                    {{ \Carbon\Carbon::parse($history->status_date)->format('d M Y') }}
                    · By {{ $history->changedBy->name ?? 'System' }}
                </div>

                @if($history->comment)
                    <div class="timeline-box mt-1">
                        <strong>Comment</strong>
                        <p class="mb-0 mt-1">{{ $history->comment }}</p>
                    </div>
                @endif

                @if ($history->followup_date)
                    <div class="mt-2 text-info mt-1">
                        <i class="fa fa-calendar"></i>
                        <strong>Next Follow-up:</strong>
                        {{ \Carbon\Carbon::parse($history->followup_date)->format('d M Y') }}
                    </div>
                @endif
            </div>
        </li>
    @endforeach
</ul>
