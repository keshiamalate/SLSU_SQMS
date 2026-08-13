@extends('layouts.student')
@section('title', 'Matching Results')

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

        /* Profile baseline card */
        .baseline-card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .baseline-card .baseline-item .label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
            font-weight: 600;
        }

        .baseline-card .baseline-item .value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111;
        }

        /* Result cards */
        .result-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .result-card.top {
            border-left: 4px solid #16a34a;
        }

        .result-card.good {
            border-left: 4px solid #2563eb;
        }

        .result-card.possible {
            border-left: 4px solid #9ca3af;
        }

        .result-card .r-info {
            flex: 1;
        }

        .result-card .r-info h6 {
            font-weight: 700;
            margin-bottom: .25rem;
            color: #111;
        }

        .result-card .r-info .r-meta {
            font-size: .8rem;
            color: #6b7280;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .result-card .r-info .r-meta span {
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .result-card .r-score {
            text-align: center;
            min-width: 100px;
        }

        .result-card .r-score .pct {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111;
        }

        .result-card .r-score .bar {
            height: 6px;
            border-radius: 3px;
            background: #e5e7eb;
            margin: .4rem 0;
            overflow: hidden;
        }

        .result-card .r-score .bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        .result-card .r-score .bar-fill.top {
            background: #16a34a;
        }

        .result-card .r-score .bar-fill.good {
            background: #2563eb;
        }

        .result-card .r-score .bar-fill.possible {
            background: #9ca3af;
        }

        .result-card .r-reason {
            font-size: .8rem;
            color: #6b7280;
            max-width: 260px;
            font-style: italic;
        }

        .match-label-badge {
            font-size: .72rem;
            font-weight: 700;
            padding: .25rem .7rem;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: .4rem;
        }

        .label-top {
            background: #f0fdf4;
            color: #16a34a;
        }

        .label-good {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .label-possible {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Section header */
        .section-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.5rem 0 .75rem;
        }

        .section-header .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .section-header h6 {
            font-weight: 700;
            margin: 0;
            color: #111;
        }

        .section-header .count {
            font-size: .8rem;
            color: #6b7280;
            margin-left: .25rem;
        }

        /* How it works box */
        .how-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem;
        }

        .how-box h6 {
            font-weight: 700;
            margin-bottom: .75rem;
            font-size: .9rem;
        }

        .how-item {
            display: flex;
            gap: .75rem;
            margin-bottom: .75rem;
            font-size: .825rem;
            color: #374151;
        }

        .how-item i {
            color: var(--sm-accent);
            margin-top: .1rem;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-start mb-1 flex-wrap gap-2">
            <div>
                <span class="badge text-bg-primary mb-2" style="font-size:.75rem;">
                    <i class="bi bi-stars me-1"></i>AI-Powered Analysis
                </span>
                <h4 class="fw-bold mb-1">Scholarship Matching</h4>
                <p class="text-muted mb-4">
                    We've analyzed {{ $matches->count() }} scholarship(s) against your academic profile.
                    Below are the opportunities most compatible with your current status.
                </p>
            </div>
            <div class="ms-auto">
                <form method="POST" action="{{ route('student.matching.rerun') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm text-white"
                        style="background:var(--sm-navy);border-radius:8px;font-weight:600;white-space:nowrap;">
                        <i class="bi bi-arrow-repeat me-1"></i>Re-run Matching
                    </button>
                </form>
            </div>
        </div>

        {{-- Profile baseline --}}
        @if($profile)
            <div class="baseline-card">
                <div><i class="bi bi-stars fs-4" style="color:var(--sm-accent);"></i></div>
                <div class="baseline-item">
                    <div class="label">Current GPA</div>
                    <div class="value">{{ $profile->cumulative_gpa }}</div>
                </div>
                <div class="baseline-item">
                    <div class="label">Household Income</div>
                    <div class="value">₱{{ number_format($profile->annual_family_income) }}/yr</div>
                </div>
                <div class="baseline-item">
                    <div class="label">Residency</div>
                    <div class="value">{{ $profile->municipality_of_residence }}</div>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('student.questionnaire.show', ['step' => 1]) }}"
                        style="font-size:.8rem;color:var(--sm-accent);">
                        Update Data →
                    </a>
                </div>
            </div>
        @endif

        @if($matches->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                No matches found yet. Make sure scholarships are configured with eligibility criteria.
            </div>
        @else
            <div class="row g-4">
                <div class="col-lg-8">

                    {{-- Matching results legend --}}
                    <div class="d-flex gap-3 mb-3" style="font-size:.8rem;">
                        <span><span
                                style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#16a34a;margin-right:4px;"></span>Top
                            Match (≥80%)</span>
                        <span><span
                                style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#2563eb;margin-right:4px;"></span>Good
                            Match (60–79%)</span>
                        <span><span
                                style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#9ca3af;margin-right:4px;"></span>Possible
                            (40–59%)</span>
                    </div>

                    {{-- Top Matches --}}
                    @if($topMatches->count())
                        <div class="section-header">
                            <div class="dot" style="background:#16a34a;"></div>
                            <h6>Top Matches <span class="count">({{ $topMatches->count() }})</span></h6>
                        </div>
                        @foreach($topMatches as $m)
                            @include('student.matching._result_card', ['match' => $m, 'type' => 'top'])
                        @endforeach
                    @endif

                    {{-- Good Matches --}}
                    @if($goodMatches->count())
                        <div class="section-header">
                            <div class="dot" style="background:#2563eb;"></div>
                            <h6>Good Matches <span class="count">({{ $goodMatches->count() }})</span></h6>
                        </div>
                        @foreach($goodMatches as $m)
                            @include('student.matching._result_card', ['match' => $m, 'type' => 'good'])
                        @endforeach
                    @endif

                    {{-- Possible Matches --}}
                    @if($possibleMatches->count())
                        <div class="section-header">
                            <div class="dot" style="background:#9ca3af;"></div>
                            <h6>Possible Matches <span class="count">({{ $possibleMatches->count() }})</span></h6>
                        </div>
                        @foreach($possibleMatches as $m)
                            @include('student.matching._result_card', ['match' => $m, 'type' => 'possible'])
                        @endforeach
                    @endif

                </div>

                {{-- How AI matching works --}}
                <div class="col-lg-4">
                    <div class="how-box">
                        <h6><i class="bi bi-stars me-2" style="color:var(--sm-accent);"></i>How AI Matching Works</h6>
                        <div class="how-item">
                            <i class="bi bi-mortarboard mt-1"></i>
                            <div><strong>Academic Ranking (30%)</strong><br>GPA benchmarked against scholarship-specific
                                thresholds.</div>
                        </div>
                        <div class="how-item">
                            <i class="bi bi-wallet2 mt-1"></i>
                            <div><strong>Financial Capacity (30%)</strong><br>Income-to-dependent ratio calculations.
                            </div>
                        </div>
                        <div class="how-item">
                            <i class="bi bi-book mt-1"></i>
                            <div><strong>Course Relevance (15%)</strong><br>Alignment of your program with scholarship
                                field.</div>
                        </div>
                        <div class="how-item">
                            <i class="bi bi-calendar3 mt-1"></i>
                            <div><strong>Year Level (15%)</strong><br>Eligibility based on your current academic year.
                            </div>
                        </div>
                        <div class="how-item">
                            <i class="bi bi-award mt-1"></i>
                            <div><strong>Special Qualifications (10%)</strong><br>Athlete, IP, PWD, student leader,
                                residency.</div>
                        </div>
                    </div>

                    <div class="how-box mt-3">
                        <h6><i class="bi bi-lightbulb me-2" style="color:#d97706;"></i>Improve Your Match Score</h6>
                        <p style="font-size:.825rem;color:#6b7280;">Completing your profile accurately uncovers more
                            scholarships you may qualify for.</p>
                        <ul style="font-size:.825rem;color:#374151;padding-left:1.25rem;">
                            <li>Add extracurricular activities</li>
                            <li>Verify your residency details</li>
                            <li>Update latest semester GPA</li>
                        </ul>
                        <a href="{{ route('student.dashboard') }}"
                            style="font-size:.825rem;color:var(--sm-accent);font-weight:600;">
                            Go to Dashboard →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;margin-top:1rem;">
        © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
    </div>
@endsection
