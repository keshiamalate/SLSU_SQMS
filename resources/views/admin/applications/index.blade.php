@extends('layouts.app')
@section('title', 'Applications')

@push('styles')
<style>
    body { background:#f0f2f5; }
    .page-wrapper { display:flex; }
    .content-area { margin-left:240px; flex:1; min-height:100vh; }
    .topbar { height:60px; background:#fff; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; padding:0 1.5rem; position:sticky; top:0; z-index:50; }
    .main-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:1.5rem; }
    .status-tab { padding:.4rem 1rem; border-radius:20px; font-size:.8rem; font-weight:600; text-decoration:none; border:1.5px solid #e5e7eb; color:#6b7280; background:#fff; transition:.15s; }
    .status-tab:hover { border-color:var(--sm-accent); color:var(--sm-accent); }
    .status-tab.active { background:var(--sm-navy); border-color:var(--sm-navy); color:#fff; }
    .status-pill { font-size:.72rem; font-weight:600; padding:.3rem .7rem; border-radius:20px; }
    .s-matched   { background:#eff6ff; color:#1d4ed8; }
    .s-applied   { background:#fff7ed; color:#c2410c; }
    .s-review    { background:#fefce8; color:#a16207; }
    .s-approved  { background:#f0fdf4; color:#15803d; }
    .s-rejected  { background:#fef2f2; color:#dc2626; }
    .s-pending   { background:#f3f4f6; color:#4b5563; }
    .student-avatar { width:34px; height:34px; border-radius:50%; background:var(--sm-navy); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:.8rem; flex-shrink:0; }
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

            <h4 class="fw-bold mb-1">Applications</h4>
            <p class="text-muted mb-4">Review and manage all student scholarship applications.</p>

            {{-- Status filter tabs --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('admin.applications.index') }}"
                   class="status-tab {{ ! request('status') ? 'active' : '' }}">
                    All <span class="ms-1">{{ $statusCounts['all'] }}</span>
                </a>
                @foreach([
                    'applied'           => 'Applied',
                    'under_review'      => 'Under Review',
                    'documents_pending' => 'Docs Pending',
                    'approved'          => 'Approved',
                    'rejected'          => 'Rejected',
                ] as $val => $label)
                    <a href="{{ route('admin.applications.index', ['status' => $val]) }}"
                       class="status-tab {{ request('status') === $val ? 'active' : '' }}">
                        {{ $label }} <span class="ms-1">{{ $statusCounts[$val] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="main-card">

                {{-- Search --}}
                <form method="GET" action="{{ route('admin.applications.index') }}" class="d-flex gap-2 mb-4">
                    <div class="input-group input-group-sm" style="max-width:300px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Search by student name or ID..."
                            value="{{ request('search') }}" />
                    </div>
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}" />
                    @endif
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                    @if(request()->hasAny(['search','status']))
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table align-middle" style="font-size:.875rem;">
                        <thead style="border-bottom:2px solid #e5e7eb;">
                            <tr class="text-muted" style="font-size:.8rem;">
                                <th>Student</th>
                                <th>Scholarship</th>
                                <th>GPA</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="student-avatar">
                                            {{ strtoupper(substr($app->user->first_name,0,1).substr($app->user->last_name,0,1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $app->user->full_name }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">
                                                {{ $app->user->institutional_id }}
                                                @if($app->user->studentProfile)
                                                    &nbsp;•&nbsp; ₱{{ number_format($app->user->studentProfile->annual_family_income) }} HH Income
                                                @endif
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
                                    <span class="fw-semibold">{{ round($app->final_score * 100) }}%</span>
                                    <div style="height:4px;border-radius:2px;background:#e5e7eb;margin-top:3px;width:60px;">
                                        <div style="height:100%;border-radius:2px;background:var(--sm-navy);width:{{ round($app->final_score * 100) }}%;"></div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $sc = match($app->status) {
                                            'matched'           => 's-matched',
                                            'applied'           => 's-applied',
                                            'under_review'      => 's-review',
                                            'documents_pending' => 's-pending',
                                            'approved'          => 's-approved',
                                            'rejected'          => 's-rejected',
                                            default             => 's-pending',
                                        };
                                    @endphp
                                    <span class="status-pill {{ $sc }}">
                                        {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.applications.show', $app) }}"
                                       class="btn btn-sm btn-outline-primary">Review</a>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        No applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($applications->hasPages())
                    <div class="mt-3">{{ $applications->links() }}</div>
                @endif

            </div>
        </div>

        <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
        </div>
    </div>
</div>
@endsection
