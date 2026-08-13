@extends('layouts.app')
@section('title', 'Review Application')

@push('styles')
<style>
    body { background:#f0f2f5; }
    .page-wrapper { display:flex; }
    .content-area { margin-left:240px; flex:1; min-height:100vh; }
    .topbar { height:60px; background:#fff; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; padding:0 1.5rem; position:sticky; top:0; z-index:50; }
    .info-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:1.5rem; margin-bottom:1.25rem; }
    .info-card h6 { font-weight:700; color:var(--sm-navy); padding-bottom:.75rem; border-bottom:1px solid #e5e7eb; margin-bottom:1rem; }
    .score-row { display:flex; justify-content:space-between; align-items:center; padding:.4rem 0; font-size:.875rem; }
    .score-bar { height:6px; border-radius:3px; background:#e5e7eb; width:120px; overflow:hidden; }
    .score-fill { height:100%; border-radius:3px; background:var(--sm-navy); }
    .btn-status { border-radius:8px; font-size:.875rem; font-weight:600; padding:.5rem 1rem; border:none; cursor:pointer; width:100%; margin-bottom:.5rem; }
    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--sm-navy);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: .875rem;
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">
    @include('layouts.sidebar_admin')

    <div class="content-area">
        <div class="topbar">
            <span style="font-weight:700;">SmartMatch</span>
            <div class="d-flex align-items-center gap-2">
                <div style="text-align:right;line-height:1.2;">
                    <div style="font-weight:600;font-size:.9rem;">{{ auth()->user()->full_name }}</div>
                    <div style="font-size:.75rem;color:#6b7280;">{{ auth()->user()->role->name }}</div>
                </div>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}</div>
            </div>
        </div>

        <div class="p-4">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
                    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex align-items-center gap-2 mb-4">
                <a href="{{ route('admin.applications.index') }}" class="text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
                <span class="text-muted">/</span>
                <h5 class="fw-bold mb-0">Review Application</h5>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">

                    {{-- Scholarship + Student --}}
                    <div class="info-card">
                        <h6><i class="bi bi-journal-check me-2"></i>Application Summary</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div style="font-size:.75rem;color:#6b7280;">STUDENT</div>
                                <div class="fw-bold">{{ $application->user->full_name }}</div>
                                <div class="text-muted" style="font-size:.8rem;">
                                    {{ $application->user->institutional_id }} &nbsp;•&nbsp; {{ $application->user->email }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="font-size:.75rem;color:#6b7280;">SCHOLARSHIP</div>
                                <div class="fw-bold">{{ $application->scholarship->name }}</div>
                                <div class="text-muted" style="font-size:.8rem;">
                                    {{ $application->scholarship->category->name }} &nbsp;•&nbsp;
                                    {{ $application->scholarship->formatted_allowance }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Matching Scores --}}
                    <div class="info-card">
                        <h6><i class="bi bi-bar-chart me-2"></i>Compatibility Scores</h6>
                        @foreach([
                            'Academic Compatibility (30%)' => $application->academic_score,
                            'Financial Need (30%)'         => $application->financial_score,
                            'Course Relevance (15%)'       => $application->course_score,
                            'Year Level Alignment (15%)'   => $application->year_level_score,
                            'Special Qualifications (10%)' => $application->special_qual_score,
                        ] as $label => $score)
                            <div class="score-row">
                                <span>{{ $label }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="score-bar">
                                        <div class="score-fill" style="width:{{ round(($score ?? 0) * 100) }}%;"></div>
                                    </div>
                                    <span class="fw-semibold" style="min-width:40px;text-align:right;">
                                        {{ round(($score ?? 0) * 100) }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        <hr />
                        <div class="score-row fw-bold">
                            <span>Final Weighted Score</span>
                            <span style="font-size:1.1rem;color:var(--sm-navy);">
                                {{ round($application->final_score * 100) }}%
                            </span>
                        </div>
                    </div>

                    {{-- Decision Notes --}}
                    @if($application->decision_notes)
                    <div class="info-card">
                        <h6><i class="bi bi-chat-left-text me-2"></i>Decision Notes</h6>
                        <p style="font-size:.875rem;color:#374151;">{{ $application->decision_notes }}</p>
                        @if($application->reviewer)
                            <div class="text-muted" style="font-size:.775rem;">
                                Reviewed by {{ $application->reviewer->full_name }}
                                on {{ $application->reviewed_at?->format('M d, Y h:i A') }}
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- Document Uploads --}}
                    <div class="info-card">
                        <h6><i class="bi bi-folder2-open me-2"></i>
                            Submitted Documents
                            <span class="badge text-bg-secondary ms-1">{{ $application->documents->count() }}</span>
                        </h6>

                        @forelse($application->documents as $doc)
                            <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 0;border-bottom:1px solid #f3f4f6;">
                                <div style="width:38px;height:38px;border-radius:8px;background:#f0f4ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-{{ str_contains($doc->mime_type, 'pdf') ? 'file-earmark-pdf' : 'file-earmark-image' }}"
                                    style="color:var(--sm-accent);font-size:1.1rem;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size:.875rem;">{{ $doc->original_filename }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">
                                        {{ number_format($doc->file_size_bytes / 1024, 1) }} KB
                                        &nbsp;•&nbsp; Uploaded {{ \Carbon\Carbon::parse($doc->uploaded_at)->diffForHumans() }}
                                    </div>
                                    @if($doc->rejection_reason)
                                        <div class="text-danger" style="font-size:.775rem;">
                                            Reason: {{ $doc->rejection_reason }}
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    @php
                                        $vc = match($doc->verification_status) {
                                            'verified' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'warning',
                                        };
                                    @endphp
                                    <span class="badge text-bg-{{ $vc }}">{{ ucfirst($doc->verification_status) }}</span>
                                    <a href="{{ route('admin.documents.admin.download', $doc) }}"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>

                            {{-- Verify / Reject form --}}
                            @if($doc->verification_status === 'pending')
                            <form method="POST"
                                action="{{ route('admin.documents.verify', $doc) }}"
                                class="d-flex gap-2 align-items-center mt-1 mb-2 ps-3">
                                @csrf @method('PATCH')
                                <input type="hidden" name="verification_status" id="vstatus_{{ $doc->id }}" value="verified" />
                                <input type="text" name="rejection_reason" id="vreason_{{ $doc->id }}"
                                    class="form-control form-control-sm d-none"
                                    placeholder="Reason for rejection..." />
                                <button type="submit"
                                        class="btn btn-sm btn-outline-success"
                                        onclick="document.getElementById('vstatus_{{ $doc->id }}').value='verified'">
                                    <i class="bi bi-check-lg me-1"></i>Verify
                                </button>
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="
                                            document.getElementById('vstatus_{{ $doc->id }}').value='rejected';
                                            document.getElementById('vreason_{{ $doc->id }}').classList.remove('d-none');
                                            return document.getElementById('vreason_{{ $doc->id }}').value.trim() !== '' || false;
                                        ">
                                    <i class="bi bi-x-lg me-1"></i>Reject
                                </button>
                            </form>
                            @endif
                        @empty
                            <div class="text-center text-muted py-3" style="font-size:.875rem;">
                                <i class="bi bi-folder2 d-block fs-4 mb-2"></i>
                                No documents submitted yet.
                            </div>
                        @endforelse
                    </div>

                </div>

                {{-- Right: Status update --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <h6><i class="bi bi-pencil-square me-2"></i>Update Status</h6>

                        <div class="mb-3">
                            <div style="font-size:.75rem;color:#6b7280;">CURRENT STATUS</div>
                            <span class="badge text-bg-primary mt-1">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </div>

                        @if($application->status === 'withdrawn')
                            <div class="alert alert-warning rounded-3" style="font-size:.875rem;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                This application was <strong>withdrawn by the student</strong> and cannot be updated.
                            </div>

                        @elseif($application->status === 'approved')
                            <div class="alert alert-success rounded-3" style="font-size:.875rem;">
                                <i class="bi bi-patch-check-fill me-2"></i>
                                This application has been <strong>approved</strong>.
                                You can still change it if needed.
                            </div>
                            @include('admin.applications._status_form', ['application' => $application])

                        @elseif($application->status === 'rejected')
                            <div class="alert alert-danger rounded-3" style="font-size:.875rem;">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                This application has been <strong>rejected</strong>.
                                You can still change it if needed.
                            </div>
                            @include('admin.applications._status_form', ['application' => $application])

                        @else
                            @include('admin.applications._status_form', ['application' => $application])
                        @endif
                    </div>

                    {{-- Student quick info --}}
                    @if($application->user->studentProfile)
                    @php $p = $application->user->studentProfile; @endphp
                    <div class="info-card">
                        <h6><i class="bi bi-person me-2"></i>Student Quick Info</h6>
                        <div style="font-size:.85rem;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">GPA</span>
                                <span class="fw-semibold">{{ $p->cumulative_gpa }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Course</span>
                                <span class="fw-semibold">{{ $p->course }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Year Level</span>
                                <span class="fw-semibold">Year {{ $p->year_level }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Income</span>
                                <span class="fw-semibold">₱{{ number_format($p->annual_family_income) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Bracket</span>
                                <span class="fw-semibold">{{ \App\Models\StudentProfile::bracketLabel($p->income_bracket) }}</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.students.show', $application->user) }}"
                               class="btn btn-sm btn-outline-secondary w-100">
                                View Full Profile
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
        </div>
    </div>
</div>
@endsection
