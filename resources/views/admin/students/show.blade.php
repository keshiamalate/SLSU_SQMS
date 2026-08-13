@extends('layouts.app')
@section('title', 'Student Profile')

@push('styles')
    <style>
        body {
            background: #f0f2f5;
        }

        .page-wrapper {
            display: flex;
        }

        .content-area {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
        }

        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .info-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .info-card h6 {
            font-weight: 700;
            color: var(--sm-navy);
            padding-bottom: .75rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem 1.5rem;
        }

        .info-field .label {
            font-size: .75rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: .15rem;
        }

        .info-field .value {
            font-size: .925rem;
            font-weight: 500;
            color: #111;
        }

        .app-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .app-row:last-child {
            border-bottom: none;
        }

        .status-pill {
            font-size: .72rem;
            font-weight: 600;
            padding: .3rem .7rem;
            border-radius: 20px;
        }

        .s-matched {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .s-applied {
            background: #fff7ed;
            color: #c2410c;
        }

        .s-review {
            background: #fefce8;
            color: #a16207;
        }

        .s-approved {
            background: #f0fdf4;
            color: #15803d;
        }

        .s-rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .s-pending {
            background: #f3f4f6;
            color: #4b5563;
        }

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
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                    </div>
                </div>
            </div>

            <div class="p-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
                        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex align-items-center gap-2 mb-4">
                    <a href="{{ route('admin.students.index') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                    <span class="text-muted">/</span>
                    <h5 class="fw-bold mb-0">{{ $student->full_name }}</h5>
                    @if($student->is_active)
                        <span class="badge text-bg-success">Active</span>
                    @else
                        <span class="badge text-bg-danger">Inactive</span>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-lg-8">

                        {{-- Account Info --}}
                        <div class="info-card">
                            <h6><i class="bi bi-person me-2"></i>Account Information</h6>
                            <div class="info-row">
                                <div class="info-field">
                                    <div class="label">Full Name</div>
                                    <div class="value">{{ $student->full_name }}</div>
                                </div>
                                <div class="info-field">
                                    <div class="label">Student ID</div>
                                    <div class="value">{{ $student->institutional_id }}</div>
                                </div>
                                <div class="info-field">
                                    <div class="label">Email</div>
                                    <div class="value">{{ $student->email }}</div>
                                </div>
                                <div class="info-field">
                                    <div class="label">Consent Signed</div>
                                    <div class="value">
                                        @if($student->consentRecords->where('consented', 1)->count())
                                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                                        @else
                                            <span class="text-danger"><i class="bi bi-x-circle me-1"></i>No</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Academic Profile --}}
                        @if($student->studentProfile)
                            @php $p = $student->studentProfile; @endphp
                            <div class="info-card">
                                <h6><i class="bi bi-mortarboard me-2"></i>Academic Profile</h6>
                                <div class="info-row">
                                    <div class="info-field">
                                        <div class="label">Course</div>
                                        <div class="value">{{ $p->course }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Year Level</div>
                                        <div class="value">Year {{ $p->year_level }} — Semester {{ $p->semester }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Cumulative GPA</div>
                                        <div class="value">{{ $p->cumulative_gpa }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Enrollment Status</div>
                                        <div class="value">{{ ucfirst($p->enrollment_status) }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Failing Grade</div>
                                        <div class="value">{{ $p->has_failing_grade ? 'Yes' : 'No' }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Academic Honors</div>
                                        <div class="value">{{ $p->academic_honors ?? '—' }}</div>
                                    </div>
                                </div>

                                <hr class="my-3" />

                                <div class="info-row">
                                    <div class="info-field">
                                        <div class="label">Annual Family Income</div>
                                        <div class="value">₱{{ number_format($p->annual_family_income) }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Income Bracket</div>
                                        <div class="value">{{ $p->income_bracket }} —
                                            {{ \App\Models\StudentProfile::bracketLabel($p->income_bracket) }}
                                        </div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">4Ps Beneficiary</div>
                                        <div class="value">{{ $p->is_4ps_beneficiary ? 'Yes' : 'No' }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Dependents</div>
                                        <div class="value">{{ $p->number_of_dependents }}</div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Municipality</div>
                                        <div class="value">{{ $p->municipality_of_residence }}, {{ $p->province_of_residence }}
                                        </div>
                                    </div>
                                    <div class="info-field">
                                        <div class="label">Special Categories</div>
                                        <div class="value">
                                            @php
                                                $cats = array_filter([
                                                    $p->is_athlete ? 'Athlete' : null,
                                                    $p->is_student_leader ? 'Student Leader' : null,
                                                    $p->is_pwd ? 'PWD' : null,
                                                    $p->is_indigenous_people ? 'IP' : null,
                                                ]);
                                            @endphp
                                            {{ $cats ? implode(', ', $cats) : 'None' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="info-card text-center text-muted py-3">
                                <i class="bi bi-clipboard-x d-block fs-3 mb-2"></i>
                                Student has not completed the profile questionnaire yet.
                            </div>
                        @endif

                        {{-- Applications --}}
                        <div class="info-card">
                            <h6><i class="bi bi-journal-check me-2"></i>Scholarship Applications</h6>
                            @forelse($student->applications as $app)
                                <div class="app-row">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.9rem;">{{ $app->scholarship->name }}</div>
                                        <div class="text-muted" style="font-size:.775rem;">
                                            {{ $app->scholarship->category->name }} &nbsp;•&nbsp;
                                            Score: {{ round($app->final_score * 100) }}%
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $statusClass = match ($app->status) {
                                                'matched' => 's-matched',
                                                'applied' => 's-applied',
                                                'under_review' => 's-review',
                                                'documents_pending' => 's-pending',
                                                'approved' => 's-approved',
                                                'rejected' => 's-rejected',
                                                default => 's-pending',
                                            };
                                        @endphp
                                        <span class="status-pill {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                        </span>
                                        <a href="{{ route('admin.applications.show', $app) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-2" style="font-size:.875rem;">No applications yet.</p>
                            @endforelse
                        </div>

                    </div>

                    {{-- Right sidebar --}}
                    <div class="col-lg-4">
                        <div class="info-card">
                            <h6><i class="bi bi-gear me-2"></i>Account Actions</h6>
                            <form method="POST" action="{{ route('admin.students.toggle', $student) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="btn btn-sm w-100 {{ $student->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} mb-2">
                                    <i class="bi bi-{{ $student->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                                    {{ $student->is_active ? 'Deactivate Account' : 'Activate Account' }}
                                </button>
                            </form>
                            <div class="text-muted" style="font-size:.775rem;">
                                Registered: {{ $student->created_at->format('M d, Y') }}<br>
                                Last login:
                                {{ $student->last_login_at ? $student->last_login_at->format('M d, Y h:i A') : 'Never' }}
                            </div>
                        </div>

                        @if($student->existingScholarships->count())
                            <div class="info-card">
                                <h6><i class="bi bi-award me-2"></i>Declared Existing Scholarships</h6>
                                @foreach($student->existingScholarships as $es)
                                    <div style="font-size:.875rem;" class="mb-2">
                                        <div class="fw-semibold">{{ $es->scholarship_name }}</div>
                                        @if($es->granting_body)
                                            <div class="text-muted">{{ $es->granting_body }}</div>
                                        @endif
                                        @if($es->is_exclusive)
                                            <span class="badge text-bg-warning text-dark" style="font-size:.7rem;">Exclusive</span>
                                        @endif
                                    </div>
                                @endforeach
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
