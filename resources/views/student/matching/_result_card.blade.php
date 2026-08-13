@php
    $pct        = round($match->final_score * 100);
    $labelText  = match($match->match_label) {
        'top_match'      => 'Top Match',
        'good_match'     => 'Good Match',
        'possible_match' => 'Possible Match',
        default          => 'Match',
    };
    $labelClass = match($match->match_label) {
        'top_match'      => 'label-top',
        'good_match'     => 'label-good',
        'possible_match' => 'label-possible',
        default          => 'label-possible',
    };
    $reasons = [
        'top_match'      => 'Strong alignment across academic, financial, and qualification criteria.',
        'good_match'     => 'Meets most requirements. Complete documentation recommended.',
        'possible_match' => 'Minimum eligibility met. Competitiveness depends on applicant pool.',
    ];
@endphp

<div class="result-card {{ $type }}">
    <div class="r-info">
        <span class="match-label-badge {{ $labelClass }}">{{ $labelText }}</span>
        <h6>{{ $match->scholarship->name }}</h6>
        <div class="r-meta">
            <span><i class="bi bi-building"></i> {{ $match->scholarship->funding_source ?? $match->scholarship->category->name }}</span>
            <span><i class="bi bi-cash"></i> {{ $match->scholarship->formatted_allowance }}</span>
            @if($match->scholarship->application_close_at)
                <span><i class="bi bi-calendar3"></i> Deadline: {{ \Carbon\Carbon::parse($match->scholarship->application_close_at)->format('M d, Y') }}</span>
            @endif
        </div>
    </div>

    <div class="r-score">
        <div class="pct">{{ $pct }}%</div>
        <div class="bar">
            <div class="bar-fill {{ $type }}" style="width:{{ $pct }}%;"></div>
        </div>
        <div style="font-size:.7rem;color:#6b7280;">Match Score</div>
    </div>

    <div class="d-none d-lg-block" style="max-width:200px;">
        <div class="r-reason">"{{ $reasons[$match->match_label] }}"</div>
    </div>

    {{-- Action button based on status --}}
    <div class="d-flex flex-column gap-1" style="min-width:110px;">
        @if($match->status === 'approved')
            <span class="badge text-bg-success text-center py-2">
                <i class="bi bi-patch-check-fill me-1"></i>Approved
            </span>
            <a href="{{ route('student.documents.index', $match) }}"
               class="btn btn-sm btn-outline-success" style="font-size:.75rem;border-radius:6px;">
                <i class="bi bi-folder2-open me-1"></i>Documents
            </a>

        @elseif($match->status === 'rejected')
            <span class="badge text-bg-danger text-center py-2">
                <i class="bi bi-x-circle-fill me-1"></i>Rejected
            </span>

        @elseif($match->status === 'under_review')
            <span class="badge text-bg-secondary text-center py-2">
                <i class="bi bi-hourglass-split me-1"></i>Under Review
            </span>

        @elseif($match->status === 'documents_pending')
            <a href="{{ route('student.documents.index', $match) }}"
               class="btn btn-sm btn-warning" style="font-size:.75rem;border-radius:6px;font-weight:600;">
                <i class="bi bi-upload me-1"></i>Upload Docs
            </a>

        @elseif($match->status === 'applied')
            <span class="badge text-bg-primary text-center py-2 mb-1">
                <i class="bi bi-send-check me-1"></i>Applied
            </span>
            <a href="{{ route('student.documents.index', $match) }}"
               class="btn btn-sm btn-outline-primary" style="font-size:.75rem;border-radius:6px;">
                <i class="bi bi-upload me-1"></i>Documents
            </a>

        @elseif($match->status === 'matched')
            <form method="POST" action="{{ route('student.scholarships.apply', $match->scholarship) }}">
                @csrf
                <button type="submit"
                        class="btn btn-sm w-100 text-white"
                        style="background:var(--sm-navy);border-radius:6px;font-weight:600;font-size:.8rem;">
                    <i class="bi bi-send me-1"></i>Apply Now
                </button>
            </form>
        @endif
    </div>
</div>
