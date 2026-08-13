@extends('layouts.student')
@section('title', 'Scholarship Opportunities')

@push('styles')
    <style>
        body {
            background: #f0f2f5;
        }

        .page-wrapper {
            display: flex;
        }

        .content-area {
            flex: 1;
            min-height: 100vh;
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

        /* Filter tabs */
        .filter-tabs {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .filter-tab {
            padding: .4rem 1rem;
            border-radius: 20px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: .85rem;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            transition: .15s;
        }

        .filter-tab:hover {
            border-color: var(--sm-accent);
            color: var(--sm-accent);
        }

        .filter-tab.active {
            background: var(--sm-navy);
            border-color: var(--sm-navy);
            color: #fff;
        }

        /* Scholarship cards */
        .scholarship-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .s-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            transition: .2s;
        }

        .s-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
            border-color: #c7d2fe;
        }

        .s-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .cat-badge {
            font-size: .7rem;
            font-weight: 600;
            padding: .25rem .65rem;
            border-radius: 20px;
        }

        .cat-government {
            background: #fff7ed;
            color: #c2410c;
        }

        .cat-institution {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .cat-need {
            background: #fef2f2;
            color: #dc2626;
        }

        .cat-special {
            background: #faf5ff;
            color: #7c3aed;
        }

        .cat-private {
            background: #f0f9ff;
            color: #0369a1;
        }

        .s-card h6 {
            font-weight: 700;
            color: #111;
            margin: 0;
            font-size: .95rem;
        }

        .s-card p {
            font-size: .8rem;
            color: #6b7280;
            margin: 0;
        }

        .s-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .s-meta-item {
            font-size: .78rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .s-meta-item i {
            color: #6b7280;
        }

        .eligibility-badge {
            font-size: .75rem;
            font-weight: 600;
            padding: .3rem .75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .elig-yes {
            background: #f0fdf4;
            color: #16a34a;
        }

        .elig-no {
            background: #fef2f2;
            color: #dc2626;
        }

        .elig-unknown {
            background: #f9fafb;
            color: #6b7280;
        }

        .beneficiary-banner {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: .875rem;
            color: #92400e;
        }
    </style>
@endpush

@section('content')
    <div class="p-3 p-md-4">

        {{-- Breadcrumb --}}
        <nav style="font-size:.8rem;" class="mb-2">
            <a href="{{ route('student.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
            <span class="text-muted mx-1">/</span>
            <span class="text-muted">Scholarships</span>
        </nav>

        <h4 class="fw-bold mb-1">Scholarship Opportunities</h4>
        <p class="text-muted mb-4">Explore available scholarships tailored for SLSU students. Review your
            eligibility based on GPA and family income before applying.</p>

        {{-- Existing scholarship warning banner --}}
        @if($hasExistingExclusive)
            <div class="beneficiary-banner">
                <i class="bi bi-info-circle me-2"></i>
                Our records indicate you are currently a beneficiary of
                <strong>{{ $hasExistingExclusive->scholarship_name }}</strong>.
                University policy prevents holding multiple major scholarships simultaneously.
                You may still view listings, but the "Apply" functionality is restricted.
            </div>
        @endif

        {{-- Search --}}
        <form method="GET" action="{{ route('student.scholarships.index') }}" class="mb-3">
            <div class="input-group" style="max-width:420px;">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0"
                    placeholder="Search scholarship name or provider..." value="{{ request('search') }}" />
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}" />
                @endif
            </div>
        </form>

        {{-- Category tabs --}}
        <div class="filter-tabs">
            <a href="{{ route('student.scholarships.index') }}"
                class="filter-tab {{ !request('category') ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('student.scholarships.index', ['category' => $cat->id]) }}"
                    class="filter-tab {{ request('category') == $cat->id ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Scholarship grid --}}
        @if($scholarships->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                No scholarships found.
            </div>
        @else
            <div class="scholarship-grid">
                @foreach($scholarships as $s)
                    @php
                        $catClass = match (true) {
                            str_contains($s->category->name, 'Government') => 'cat-government',
                            str_contains($s->category->name, 'Institution') => 'cat-institution',
                            str_contains($s->category->name, 'Need') => 'cat-need',
                            str_contains($s->category->name, 'Special') => 'cat-special',
                            default => 'cat-private',
                        };
                    @endphp
                    <div class="s-card">
                        <div class="s-card-header">
                            <span class="cat-badge {{ $catClass }}">{{ $s->category->name }}</span>
                        </div>

                        <div>
                            <h6>{{ $s->name }}</h6>
                            @if($s->description)
                                <p class="mt-1">{{ Str::limit($s->description, 90) }}</p>
                            @endif
                        </div>

                        <div class="s-meta">
                            <div class="s-meta-item">
                                <i class="bi bi-mortarboard"></i>
                                GPA Req: {{ $s->criteria?->min_gpa ? $s->criteria->min_gpa . ' or better' : 'None' }}
                            </div>
                            <div class="s-meta-item">
                                <i class="bi bi-wallet2"></i>
                                {{ $s->formatted_allowance }}
                            </div>
                            @if($s->application_close_at)
                                <div class="s-meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    Deadline: {{ \Carbon\Carbon::parse($s->application_close_at)->format('M d, Y') }}
                                </div>
                            @endif
                            @if($s->criteria?->max_annual_income)
                                <div class="s-meta-item">
                                    <i class="bi bi-people"></i>
                                    Income: ≤ ₱{{ number_format($s->criteria->max_annual_income) }}
                                </div>
                            @endif
                        </div>

                        {{-- Eligibility status --}}
                        @if($profile)
                            @if($s->is_eligible)
                                <div class="eligibility-badge elig-yes">
                                    <i class="bi bi-check-circle-fill"></i> You are Qualified
                                </div>
                            @else
                                <div class="eligibility-badge elig-no">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Not Qualified: {{ Str::limit($s->fail_reason, 50) }}
                                </div>
                            @endif
                        @else
                            <div class="eligibility-badge elig-unknown">
                                <i class="bi bi-question-circle"></i> Complete profile to check eligibility
                            </div>
                        @endif

                        {{-- Apply button --}}
                        @php
                            $appStatus = $user->applications
                                ->where('scholarship_id', $s->id)
                                ->first()?->status;
                        @endphp

                        <div style="border-top:1px solid #f3f4f6;padding-top:.75rem;margin-top:.25rem;">
                            @if($hasExistingExclusive)
                                <div class="text-center" style="font-size:.75rem;color:#d97706;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Beneficiary Restricted
                                </div>

                            @elseif($appStatus === 'approved')
                                <div class="text-center" style="font-size:.8rem;font-weight:600;color:#16a34a;">
                                    <i class="bi bi-patch-check-fill me-1"></i>Scholarship Approved
                                </div>

                            @elseif($appStatus === 'rejected')
                                <div class="text-center" style="font-size:.8rem;font-weight:600;color:#dc2626;">
                                    <i class="bi bi-x-circle-fill me-1"></i>Application Rejected
                                </div>

                            @elseif($appStatus === 'under_review')
                                <div class="text-center" style="font-size:.8rem;font-weight:600;color:#8b5cf6;">
                                    <i class="bi bi-hourglass-split me-1"></i>Under Review
                                </div>

                            @elseif($appStatus === 'documents_pending')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('student.documents.index', $user->applications->where('scholarship_id', $s->id)->first()) }}"
                                        class="btn btn-sm btn-warning w-100"
                                        style="font-size:.78rem;font-weight:600;border-radius:8px;">
                                        <i class="bi bi-upload me-1"></i>Upload Documents
                                    </a>
                                </div>

                            @elseif($appStatus === 'applied')
                                <div class="d-flex gap-2">
                                    <div class="flex-grow-1 text-center"
                                        style="font-size:.8rem;font-weight:600;color:#2563eb;padding:.4rem;">
                                        <i class="bi bi-send-check me-1"></i>Applied
                                    </div>
                                    <form method="POST"
                                        action="{{ route('student.applications.withdraw', $user->applications->where('scholarship_id', $s->id)->first()) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="font-size:.75rem;border-radius:8px;"
                                            onclick="return confirm('Withdraw your application?')">
                                            Withdraw
                                        </button>
                                    </form>
                                </div>

                            @elseif($s->is_eligible)
                                <form method="POST" action="{{ route('student.scholarships.apply', $s) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm w-100 text-white"
                                        style="background:var(--sm-navy);border-radius:8px;font-weight:600;font-size:.85rem;">
                                        <i class="bi bi-send me-1"></i>Apply Now
                                    </button>
                                </form>

                            @else
                                <div class="text-center text-muted" style="font-size:.75rem;">
                                    Not eligible for this scholarship
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
