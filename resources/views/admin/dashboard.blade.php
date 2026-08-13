@extends('layouts.app')
@section('title', 'Admin Dashboard')

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

        .section-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .quick-action {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: .2s;
        }

        .quick-action:hover {
            background: var(--sm-light);
            border-color: var(--sm-accent);
        }

        .quick-action i {
            font-size: 1.4rem;
            color: var(--sm-navy);
            display: block;
            margin-bottom: .4rem;
        }

        .quick-action span {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
        }

        .ai-banner {
            background: var(--sm-navy);
            color: #fff;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
                        <div style="font-weight:600;font-size:.9rem;">{{ $admin->full_name }}</div>
                        <div style="font-size:.75rem;color:#6b7280;">{{ $admin->role->name }}</div>
                    </div>
                    <div class="avatar">{{ strtoupper(substr($admin->first_name, 0, 1) . substr($admin->last_name, 0, 1)) }}</div>
                </div>
            </div>

            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Admin Dashboard</h4>
                        <p class="text-muted mb-0">Welcome back, Admin. Here is the overview of the scholarship system
                            status.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-bar-chart me-1"></i>View Reports</a>
                        <a href="{{ route('admin.scholarships.index') }}" class="btn btn-sm text-white" style="background:var(--sm-navy);"><i class="bi bi-award me-1"></i>Manage Scholarships</a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="label">Total Students</div>
                            <div class="value">{{ number_format($stats['total_students']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="label">Total Scholarships</div>
                            <div class="value">{{ $stats['active_scholarships'] }} <span
                                    style="font-size:1rem;color:#6b7280;">Active</span></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="label">Total Applications</div>
                            <div class="value">{{ number_format($stats['total_applications']) }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="label">Qualified Students</div>
                            <div class="value">{{ number_format($stats['qualified_students']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-lg-7">
                        <div class="section-card h-100">
                            <h6 class="fw-bold mb-1">Recent Applications</h6>
                            <small class="text-muted d-block mb-3">Last 5 student submissions across all programs.</small>
                            <table class="table table-borderless align-middle" style="font-size:.875rem;">
                                <thead style="border-bottom:1px solid #f3f4f6;">
                                    <tr class="text-muted" style="font-size:.8rem;">
                                        <th>Student</th>
                                        <th>Scholarship</th>
                                        <th>GPA</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentApplications as $app)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:32px;height:32px;border-radius:50%;background:var(--sm-navy);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:.75rem;flex-shrink:0;">
                                                    {{ strtoupper(substr($app->user->first_name,0,1).substr($app->user->last_name,0,1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $app->user->full_name }}</div>
                                                    <div class="text-muted" style="font-size:.75rem;">
                                                        ₱{{ $app->user->studentProfile ? number_format($app->user->studentProfile->annual_family_income) : '—' }} HH Income
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $app->scholarship->name }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">{{ $app->scholarship->category->name }}</div>
                                        </td>
                                        <td>{{ $app->user->studentProfile?->cumulative_gpa ?? '—' }}</td>
                                        <td>
                                            @php
                                                $sc = match($app->status) {
                                                    'matched'           => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                                                    'applied'           => ['bg'=>'#fff7ed','color'=>'#c2410c'],
                                                    'under_review'      => ['bg'=>'#fefce8','color'=>'#a16207'],
                                                    'documents_pending' => ['bg'=>'#f3f4f6','color'=>'#4b5563'],
                                                    'approved'          => ['bg'=>'#f0fdf4','color'=>'#15803d'],
                                                    'rejected'          => ['bg'=>'#fef2f2','color'=>'#dc2626'],
                                                    default             => ['bg'=>'#f3f4f6','color'=>'#4b5563'],
                                                };
                                            @endphp
                                            <span style="font-size:.72rem;font-weight:600;padding:.3rem .7rem;border-radius:20px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                                {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                            No applications yet.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if($recentApplications->count())
                                <div class="text-end mt-2">
                                    <a href="{{ route('admin.applications.index') }}"
                                    style="font-size:.8rem;color:var(--sm-accent);">See All →</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="section-card mb-3">
                            <h6 class="fw-bold mb-3">Quick Actions</h6>
                            <div class="row g-2">
                                <div class="col-6"><a href="{{ route('admin.students.create') }}" class="quick-action"><i class="bi bi-person-plus"></i><span>ADD STUDENT</span></a></div>
                                <div class="col-6"><a href="{{ route('admin.scholarships.create') }}" class="quick-action"><i class="bi bi-journal-plus"></i><span>ADD PROGRAM</span></a></div>
                                <div class="col-6"><a href="{{ route('admin.export.all') }}" class="quick-action"><i class="bi bi-file-earmark-arrow-down"></i><span>EXPORT DATA</span></a></div>
                                <div class="col-6"><a href="{{ route('admin.notifications.index') }}" class="quick-action"><i class="bi bi-megaphone"></i><span>NOTIFY STUDENTS</span></a></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ai-banner mt-3">
                    <div>
                        <div class="fw-bold mb-1">
                            <i class="bi bi-stars me-2"></i>
                            @if($mlActive)
                                AI Matching Active — Random Forest Model Online
                            @else
                                Weighted Matching Active — ML API Offline
                            @endif
                        </div>
                        <small style="opacity:.8;">
                            @if($mlActive)
                                Random Forest model deployed.
                                Accuracy: {{ $mlInfo['accuracy'] ?? '—' }} &nbsp;|&nbsp;
                                F1 Score: {{ $mlInfo['f1_score'] ?? '—' }} &nbsp;|&nbsp;
                                Trained on {{ number_format($mlInfo['training_records'] ?? 0) }} records.
                            @else
                                Rule-based weighted scoring is active.
                                {{ $matchingStats['total_matched'] }} total matched applications.
                                Start the Flask API to enable Random Forest scoring.
                            @endif
                        </small>
                    </div>
                    <a href="{{ route('admin.applications.index') }}"
                    class="btn btn-light btn-sm fw-semibold">
                        Audit Results
                    </a>
                </div>
            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
