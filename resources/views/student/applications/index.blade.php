@extends('layouts.student')
@section('title', 'My Applications')

@push('styles')
<style>
    body {
        background:#f0f2f5;
    }
    .page-wrapper {
        display:flex;
    }
    .content-area {
        flex:1;
        min-height:100vh;
        overflow-x:hidden;
    }
    .avatar {
        width:36px;
        height:36px;
        border-radius:50%;
        background:var(--sm-navy);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:600;
        font-size:.875rem;
    }
    .app-card {
        background:#fff;
        border-radius:12px;
        border:1px solid #e5e7eb;
        padding:1.25rem 1.5rem;
        margin-bottom:1rem;
    }
    .app-card.status-approved {
        border-left:4px solid #16a34a;
    }
    .app-card.status-rejected {
        border-left:4px solid #dc2626;
    }
    .app-card.status-under_review {
        border-left:4px solid #8b5cf6;
    }
    .app-card.status-applied {
        border-left:4px solid #2563eb;
    }
    .app-card.status-documents_pending {
        border-left:4px solid #f59e0b;
    }
    .app-card.status-matched {
        border-left:4px solid #9ca3af;
    }
    .app-card.status-withdrawn {
        border-left:4px solid #d1d5db; opacity:.6;
    }
    .status-pill {
        font-size:.72rem;
        font-weight:600;
        padding:.3rem .8rem;
        border-radius:20px;
    }
    .score-bar {
        height:6px;
        border-radius:3px;
        background:#e5e7eb;
        overflow:hidden;
        width:80px;
    }
    .score-fill {
        height:100%;
        border-radius:3px;
        background:var(--sm-navy);
    }
</style>
@endpush

@section('content')
        <div class="p-4">
            @foreach(['success','error','info'] as $msg)
                @if(session($msg))
                    <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show rounded-3 mb-3">
                        {{ session($msg) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            <h4 class="fw-bold mb-1">My Applications</h4>
            <p class="text-muted mb-4">Track all your scholarship applications and their current status.</p>

            {{-- Status summary --}}
            @php
                $counts = [
                    'applied'           => $applications->where('status','applied')->count(),
                    'under_review'      => $applications->where('status','under_review')->count(),
                    'documents_pending' => $applications->where('status','documents_pending')->count(),
                    'approved'          => $applications->where('status','approved')->count(),
                    'rejected'          => $applications->where('status','rejected')->count(),
                ];
            @endphp
            <div class="row g-2 mb-4">
                @foreach([
                    'applied'           => ['Applied',          'primary',  'bi-send'],
                    'under_review'      => ['Under Review',     'purple',   'bi-hourglass-split'],
                    'documents_pending' => ['Docs Pending',     'warning',  'bi-upload'],
                    'approved'          => ['Approved',         'success',  'bi-patch-check'],
                    'rejected'          => ['Rejected',         'danger',   'bi-x-circle'],
                ] as $status => [$label, $color, $icon])
                    <div class="col">
                        <div class="text-center p-2 rounded-3 border" style="background:#fff;">
                            <i class="bi {{ $icon }}" style="color:var(--bs-{{ $color }},#6b7280);font-size:1.1rem;"></i>
                            <div style="font-size:1.25rem;font-weight:700;">{{ $counts[$status] }}</div>
                            <div style="font-size:.72rem;color:#6b7280;">{{ $label }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Application cards --}}
            @forelse($applications as $app)
            @php
                $statusConfig = match($app->status) {
                    'approved'          => ['Approved',           'success', 'bi-patch-check-fill'],
                    'rejected'          => ['Rejected',           'danger',  'bi-x-circle-fill'],
                    'under_review'      => ['Under Review',       'purple',  'bi-hourglass-split'],
                    'documents_pending' => ['Documents Pending',  'warning', 'bi-upload'],
                    'applied'           => ['Applied',            'primary', 'bi-send-check'],
                    'matched'           => ['Matched — Not Yet Applied', 'secondary', 'bi-stars'],
                    'withdrawn'         => ['Withdrawn',          'secondary','bi-dash-circle'],
                    default             => ['Unknown',            'secondary','bi-question'],
                };
            @endphp
            <div class="app-card status-{{ $app->status }}">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h6 class="fw-bold mb-0">{{ $app->scholarship->name }}</h6>
                            <span class="badge text-bg-{{ $statusConfig[1] }}" style="font-size:.7rem;">
                                <i class="bi {{ $statusConfig[2] }} me-1"></i>{{ $statusConfig[0] }}
                            </span>
                        </div>
                        <div class="text-muted" style="font-size:.8rem;">
                            {{ $app->scholarship->category->name }}
                            &nbsp;•&nbsp;
                            {{ $app->scholarship->formatted_allowance }}
                            @if($app->applied_at)
                                &nbsp;•&nbsp; Applied {{ $app->applied_at->format('M d, Y') }}
                            @endif
                        </div>

                        {{-- Decision notes from admin --}}
                        @if($app->decision_notes && in_array($app->status, ['approved','rejected','under_review']))
                            <div class="mt-2 p-2 rounded-3" style="background:{{ $app->status === 'approved' ? '#f0fdf4' : ($app->status === 'rejected' ? '#fef2f2' : '#f5f3ff') }};font-size:.825rem;">
                                <i class="bi bi-chat-left-text me-2"></i>
                                <strong>Admin Note:</strong> {{ $app->decision_notes }}
                            </div>
                        @endif
                    </div>

                    <div class="d-flex flex-column align-items-end gap-2">
                        <div class="text-end">
                            <div style="font-size:.75rem;color:#6b7280;">Match Score</div>
                            <div style="font-size:1.1rem;font-weight:700;">{{ round($app->final_score * 100) }}%</div>
                            <div class="score-bar">
                                <div class="score-fill" style="width:{{ round($app->final_score * 100) }}%;"></div>
                            </div>
                        </div>

                        {{-- Action buttons based on status --}}
                        <div class="d-flex gap-1 flex-wrap justify-content-end">
                            @if($app->status === 'matched')
                                <form method="POST" action="{{ route('student.scholarships.apply', $app->scholarship) }}">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm text-white"
                                            style="background:var(--sm-navy);border-radius:8px;font-size:.78rem;font-weight:600;">
                                        <i class="bi bi-send me-1"></i>Apply Now
                                    </button>
                                </form>

                            @elseif(in_array($app->status, ['applied','documents_pending']))
                                <a href="{{ route('student.documents.index', $app) }}" class="btn btn-sm btn-outline-primary" style="font-size:.78rem;border-radius:8px;">
                                    <i class="bi bi-upload me-1"></i>Documents
                                </a>
                                <form method="POST" action="{{ route('student.applications.withdraw', $app) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            style="font-size:.78rem;border-radius:8px;"
                                            onclick="return confirm('Withdraw your application for {{ addslashes($app->scholarship->name) }}?')">
                                        Withdraw
                                    </button>
                                </form>

                            @elseif($app->status === 'approved')
                                <a href="{{ route('student.documents.index', $app) }}" class="btn btn-sm btn-outline-success" style="font-size:.78rem;border-radius:8px;">
                                    <i class="bi bi-folder2-open me-1"></i>View Documents
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                    No applications yet.
                    <a href="{{ route('student.scholarships.index') }}" class="d-block mt-2" style="color:var(--sm-accent);">Browse Scholarships →</a>
                </div>
            @endforelse
        </div>

        <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
        </div>
@endsection
