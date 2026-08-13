@extends('layouts.student')
@section('title', 'Student Dashboard')

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

        .stat-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
        }

        .stat-card .label {
            font-size: .8rem;
            color: #6b7280;
        }

        .stat-card .value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #111;
        }

        .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .cta-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            height: 100%;
        }

        .cta-card .icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--sm-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .btn-cta {
            background: var(--sm-navy);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .55rem 1.1rem;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-cta:hover {
            background: var(--sm-accent);
            color: #fff;
        }

        .alert-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
        }

        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .alert-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush

@section('content')
    <div class="p-3 p-md-4">
        @foreach (['success', 'info', 'error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show rounded-3 mb-3">
                    {{ session($msg) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold mb-1">Welcome back, <span style="color:var(--sm-accent);">{{ $user->first_name }}!</span>
                </h4>
                <p class="text-muted mb-0">Your path to financial support starts here.</p>
            </div>
            <span class="badge rounded-pill text-bg-secondary">
                <i class="bi bi-calendar3 me-1"></i>Semester 1, A.Y. {{ date('Y') }}-{{ date('Y') + 1 }}
            </span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Academic Standing</div>
                        <div class="value">{{ $profile?->cumulative_gpa ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#6b7280;">Current Cumulative GPA</div>
                    </div>
                    <div class="icon-box" style="background:#eff6ff;"><i class="bi bi-mortarboard"
                            style="color:#2563eb;"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Education Level</div>
                        <div class="value">
                            @if($profile){{ $profile->year_level }}{{
                                match ($profile->year_level) {
                                    1 => 'st',
                                    2 => 'nd',
                                    3 => 'rd',
                                    default => 'th'
                                }
                                                                                                                                                                                                                                }} Year
                            @else
                                —
                            @endif
                        </div>
                        <div style="font-size:.75rem;color:#6b7280;">{{ $profile?->course ?? 'Not set' }}</div>
                    </div>
                    <div class="icon-box" style="background:#f0fdf4;"><i class="bi bi-book" style="color:#16a34a;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label">Financial Bracket</div>
                        <div class="value">{{ $profile?->income_bracket ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#6b7280;">Based on family annual income</div>
                    </div>
                    <div class="icon-box" style="background:#fff7ed;"><i class="bi bi-wallet2" style="color:#ea580c;"></i>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mb-3" style="color:var(--sm-navy);">| How can we help today?</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="cta-card">
                    <div class="icon-wrap"><i class="bi bi-book-half"></i></div>
                    <h6 class="fw-bold">Explore Scholarship Listings</h6>
                    <p class="text-muted" style="font-size:.875rem;">Browse the complete catalog of SLSU
                        scholarships and financial aid programs.</p>
                    <a href="{{ route('student.scholarships.index') }}" class="btn-cta">
                        View All Scholarships <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cta-card">
                    <div class="icon-wrap"><i class="bi bi-stars"></i></div>
                    <h6 class="fw-bold">Check Your Matching Results</h6>
                    <p class="text-muted" style="font-size:.875rem;">SmartMatch AI has analyzed your profile. See
                        which scholarships you qualify for.</p>
                    <form method="POST" action="{{ route('student.matching.rerun') }}">
                        @csrf
                        <button type="submit" class="btn-cta">
                            <i class="bi bi-arrow-repeat me-1"></i>Check My Matches
                        </button>
                    </form>
                </div>
            </div>
            {{-- Match Results Summary --}}
            @if($matches->count() > 0)
                <div class="col-12 mt-2">
                    <div class="alert-card">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-stars me-2" style="color:var(--sm-accent);"></i>
                            Your Matched Scholarships
                            <span class="badge text-bg-primary ms-2">{{ $matches->count() }} found</span>
                        </h6>

                        @foreach($matches->take(5) as $match)
                            @php
                                $labelClass = match ($match->match_label) {
                                    'top_match' => 'success',
                                    'good_match' => 'primary',
                                    'possible_match' => 'secondary',
                                    default => 'secondary',
                                };
                                $labelText = match ($match->match_label) {
                                    'top_match' => 'Top Match',
                                    'good_match' => 'Good Match',
                                    'possible_match' => 'Possible Match',
                                    default => 'Match',
                                };
                            @endphp
                            <div class="alert-item">
                                <div>
                                    <div class="title">{{ $match->scholarship->name }}</div>
                                    <div class="meta">
                                        {{ $match->scholarship->category->name }} &nbsp;•&nbsp;
                                        {{ $match->scholarship->formatted_allowance }}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:.8rem;font-weight:600;color:#374151;">
                                        {{ round($match->final_score * 100) }}%
                                    </span>
                                    <span class="badge text-bg-{{ $labelClass }}">{{ $labelText }}</span>
                                </div>
                            </div>
                        @endforeach

                        @if($matches->count() > 5)
                            <div class="text-center mt-2">
                                <a href="#" style="font-size:.875rem;color:var(--sm-accent);">
                                    View all {{ $matches->count() }} matches →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
