@extends('layouts.app')
@section('title', 'Analytics & Reports')

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
            font-size: 1.75rem;
            font-weight: 700;
            color: #111;
        }

        .chart-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .chart-card h6 {
            font-weight: 700;
            color: var(--sm-navy);
            margin-bottom: 1.25rem;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: .6rem;
            font-size: .825rem;
        }

        .bar-row .bar-label {
            min-width: 140px;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bar-row .bar-track {
            flex: 1;
            height: 10px;
            border-radius: 5px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .bar-row .bar-fill {
            height: 100%;
            border-radius: 5px;
        }

        .bar-row .bar-count {
            min-width: 30px;
            text-align: right;
            color: #6b7280;
            font-weight: 600;
        }

        .donut-legend {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .825rem;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .tab-nav .nav-link {
            color: #6b7280;
            font-weight: 500;
            border: none;
            padding: .75rem 1.25rem;
            border-bottom: 2px solid transparent;
        }

        .tab-nav .nav-link.active {
            color: var(--sm-navy);
            border-bottom-color: var(--sm-navy);
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
                <div class="d-flex gap-2 float-end">
                    <a href="{{ route('admin.analytics.export.students') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i>Export Students CSV
                    </a>
                    <a href="{{ route('admin.analytics.export.applications') }}" class="btn btn-sm text-white"
                        style="background:var(--sm-navy);">
                        <i class="bi bi-download me-1"></i>Export Applications CSV
                    </a>
                </div>

                <h4 class="fw-bold mb-1">Analytics &amp; Reports</h4>
                <p class="text-muted mb-0">Scholarship distribution, application trends, and student statistics.</p>

                {{-- Tabs --}}
                <ul class="nav tab-nav border-bottom mt-4 mb-4">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.scholarships.index') }}">Scholarship
                            Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.analytics.index') }}">Analytics &amp; Reports</a>
                    </li>
                </ul>

                {{-- Summary stats --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-2">
                        <div class="stat-card text-center">
                            <div class="label">Total Students</div>
                            <div class="value">{{ number_format($summary['total_students']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card text-center">
                            <div class="label">Profiles Complete</div>
                            <div class="value">{{ number_format($summary['profile_complete']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card text-center">
                            <div class="label">Total Applications</div>
                            <div class="value">{{ number_format($summary['total_applications']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card text-center">
                            <div class="label">Approved</div>
                            <div class="value" style="color:#16a34a;">{{ number_format($summary['approved']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card text-center">
                            <div class="label">Active Scholarships</div>
                            <div class="value">{{ $summary['active_scholarships'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="stat-card text-center">
                            <div class="label">Avg Match Score</div>
                            <div class="value">{{ $summary['avg_score'] }}%</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">

                    {{-- Applications per Scholarship --}}
                    <div class="col-lg-6">
                        <div class="chart-card h-100">
                            <h6><i class="bi bi-bar-chart me-2"></i>Applications per Scholarship</h6>
                            @forelse($perScholarship as $item)
                                @php $max = $perScholarship->max('total') ?: 1; @endphp
                                <div class="bar-row">
                                    <div class="bar-label" title="{{ $item['name'] }}">{{ Str::limit($item['name'], 22) }}</div>
                                    <div class="bar-track">
                                        <div class="bar-fill"
                                            style="width:{{ ($item['total'] / $max) * 100 }}%;background:var(--sm-navy);"></div>
                                    </div>
                                    <div class="bar-count">{{ $item['total'] }}</div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3" style="font-size:.875rem;">No data yet.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Application Status Breakdown --}}
                    <div class="col-lg-3">
                        <div class="chart-card h-100">
                            <h6><i class="bi bi-pie-chart me-2"></i>Application Status</h6>
                            @php
                                $statusColors = [
                                    'matched' => '#2563eb',
                                    'applied' => '#f59e0b',
                                    'under_review' => '#8b5cf6',
                                    'documents_pending' => '#6b7280',
                                    'approved' => '#16a34a',
                                    'rejected' => '#dc2626',
                                    'withdrawn' => '#9ca3af',
                                ];
                                $total = array_sum($statusBreakdown) ?: 1;
                            @endphp
                            <div class="donut-legend">
                                @foreach($statusColors as $status => $color)
                                    @if(isset($statusBreakdown[$status]))
                                        <div class="legend-item">
                                            <div class="legend-dot" style="background:{{ $color }};"></div>
                                            <div class="flex-grow-1">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                                            <div class="fw-semibold">{{ $statusBreakdown[$status] }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">
                                                ({{ round(($statusBreakdown[$status] / $total) * 100) }}%)
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @if(empty($statusBreakdown))
                                    <p class="text-muted text-center" style="font-size:.875rem;">No data yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Match Label Distribution --}}
                    <div class="col-lg-3">
                        <div class="chart-card h-100">
                            <h6><i class="bi bi-stars me-2"></i>Match Distribution</h6>
                            @php
                                $matchColors = [
                                    'top_match' => '#16a34a',
                                    'good_match' => '#2563eb',
                                    'possible_match' => '#9ca3af',
                                ];
                                $matchTotal = array_sum($matchLabels) ?: 1;
                            @endphp
                            <div class="donut-legend">
                                @foreach($matchColors as $label => $color)
                                    @php $count = $matchLabels[$label] ?? 0; @endphp
                                    <div class="legend-item">
                                        <div class="legend-dot" style="background:{{ $color }};"></div>
                                        <div class="flex-grow-1">{{ ucfirst(str_replace('_', ' ', $label)) }}</div>
                                        <div class="fw-semibold">{{ $count }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            ({{ round(($count / $matchTotal) * 100) }}%)
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-3" />

                            <h6 style="font-size:.85rem;"><i class="bi bi-wallet2 me-2"></i>Income Brackets</h6>
                            <div class="donut-legend">
                                @php
                                    $bracketColors = ['A' => '#dc2626', 'B' => '#f59e0b', 'C' => '#2563eb', 'D' => '#8b5cf6', 'E' => '#16a34a'];
                                    $bracketTotal = array_sum($incomeBrackets) ?: 1;
                                @endphp
                                @foreach($bracketColors as $bracket => $color)
                                    @php $count = $incomeBrackets[$bracket] ?? 0; @endphp
                                    <div class="legend-item">
                                        <div class="legend-dot" style="background:{{ $color }};"></div>
                                        <div class="flex-grow-1">
                                            {{ $bracket }} — {{ \App\Models\StudentProfile::bracketLabel($bracket) }}
                                        </div>
                                        <div class="fw-semibold">{{ $count }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Monthly trend --}}
                <div class="chart-card mb-3">
                    <h6><i class="bi bi-graph-up me-2"></i>Application Trend — Last 6 Months</h6>
                    @if(!empty($monthlyTrend))
                        @php $maxTrend = max($monthlyTrend) ?: 1; @endphp
                        <div class="d-flex align-items-end gap-3" style="height:120px;">
                            @foreach($monthlyTrend as $month => $count)
                                <div class="d-flex flex-column align-items-center flex-grow-1">
                                    <div style="font-size:.7rem;color:#6b7280;margin-bottom:.25rem;">{{ $count }}</div>
                                    <div
                                        style="width:100%;background:var(--sm-navy);border-radius:4px 4px 0 0;height:{{ ($count / $maxTrend) * 100 }}px;min-height:4px;">
                                    </div>
                                    <div style="font-size:.7rem;color:#6b7280;margin-top:.25rem;">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3" style="font-size:.875rem;">No application data in the last 6
                            months.</p>
                    @endif
                </div>

                {{-- Per category --}}
                <div class="chart-card">
                    <h6><i class="bi bi-tag me-2"></i>Applications by Category</h6>
                    @forelse($perCategory as $category => $count)
                        @php $maxCat = $perCategory->max() ?: 1; @endphp
                        <div class="bar-row">
                            <div class="bar-label">{{ $category }}</div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ ($count / $maxCat) * 100 }}%;background:#8b5cf6;"></div>
                            </div>
                            <div class="bar-count">{{ $count }}</div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3" style="font-size:.875rem;">No data yet.</p>
                    @endforelse
                </div>

            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
