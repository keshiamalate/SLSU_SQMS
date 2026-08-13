@extends('layouts.app')
@section('title', 'Scholarships & Reports')

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

        .main-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .filter-bar {
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .badge-category {
            font-size: .75rem;
            padding: .3rem .75rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .cat-academic {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .cat-financial {
            background: #f0fdf4;
            color: #15803d;
        }

        .cat-government {
            background: #fff7ed;
            color: #c2410c;
        }

        .cat-special {
            background: #faf5ff;
            color: #7c3aed;
        }

        .cat-need {
            background: #fef2f2;
            color: #dc2626;
        }

        .cat-private {
            background: #f0f9ff;
            color: #0369a1;
        }

        .action-btn {
            background: none;
            border: none;
            padding: .25rem .4rem;
            border-radius: 6px;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #f3f4f6;
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

                @foreach(['success', 'error'] as $msg)
                    @if(session($msg))
                        <div
                            class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show rounded-3 mb-3">
                            {{ session($msg) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endforeach

                <h4 class="fw-bold mb-1">Scholarships &amp; Reports</h4>
                <p class="text-muted mb-4">Configure scholarship qualification parameters and monitor university-wide
                    application statistics.</p>

                {{-- Tabs --}}
                <ul class="nav tab-nav border-bottom mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.scholarships.index') }}">Scholarship Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.analytics.index') }}">Analytics &amp; Reports</a>
                    </li>
                </ul>

                <div class="main-card">

                    {{-- Filter bar --}}
                    <form method="GET" action="{{ route('admin.scholarships.index') }}">
                        <div class="filter-bar d-flex flex-wrap gap-2 align-items-center">
                            <div class="flex-grow-1">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                        placeholder="Search by scholarship name..." value="{{ request('search') }}" />
                                </div>
                            </div>
                            <div style="min-width:160px;">
                                <select name="category" class="form-select form-select-sm">
                                    <option value="">Category: All</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            @if(request()->hasAny(['search', 'category']))
                                <a href="{{ route('admin.scholarships.index') }}"
                                    class="btn btn-sm btn-outline-danger">Clear</a>
                            @endif
                            <div class="ms-auto d-flex gap-2">
                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download me-1"></i>Export CSV
                                </a>
                                <a href="{{ route('admin.scholarships.create') }}" class="btn btn-sm text-white"
                                    style="background:var(--sm-navy);">
                                    <i class="bi bi-plus-lg me-1"></i>Add Scholarship
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <h6 class="fw-bold mb-1">Active Scholarship Programs</h6>
                    <p class="text-muted mb-3" style="font-size:.8rem;">
                        Showing {{ $scholarships->firstItem() }}–{{ $scholarships->lastItem() }} of
                        {{ $scholarships->total() }} scholarships
                    </p>

                    <div class="table-responsive">
                        <table class="table align-middle" style="font-size:.875rem;">
                            <thead style="border-bottom:2px solid #e5e7eb;">
                                <tr class="text-muted" style="font-size:.8rem;">
                                    <th>Scholarship Name</th>
                                    <th>GPA Req. <span class="text-muted" style="font-size:.7rem;">(≥ to qualify)</span>
                                    </th>
                                    <th>Income Limit</th>
                                    <th>Allowance</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scholarships as $s)
                                    <tr>
                                        <td class="fw-semibold">{{ $s->name }}</td>
                                        <td>{{ $s->criteria?->min_gpa ?? '—' }}</td>
                                        <td>
                                            @if($s->criteria?->max_annual_income)
                                                ₱{{ number_format($s->criteria->max_annual_income) }}
                                            @else
                                                No Limit
                                            @endif
                                        </td>
                                        <td style="font-size:.8rem;">{{ $s->formatted_allowance }}</td>
                                        <td>
                                            @php
                                                $catClass = match (true) {
                                                    str_contains($s->category->name, 'Government') => 'cat-government',
                                                    str_contains($s->category->name, 'Institution') => 'cat-academic',
                                                    str_contains($s->category->name, 'Need') => 'cat-need',
                                                    str_contains($s->category->name, 'Special') => 'cat-special',
                                                    default => 'cat-private',
                                                };
                                            @endphp
                                            <span class="badge-category {{ $catClass }}">{{ $s->category->name }}</span>
                                        </td>
                                        <td>
                                            @if($s->is_active)
                                                <span class="badge text-bg-success">Active</span>
                                            @else
                                                <span class="badge text-bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.scholarships.edit', $s) }}" class="action-btn text-primary"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.scholarships.toggle', $s) }}"
                                                class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="action-btn text-warning"
                                                    title="{{ $s->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="bi bi-{{ $s->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.scholarships.destroy', $s) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Delete {{ addslashes($s->name) }}? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="action-btn text-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                            No scholarships found. <a href="{{ route('admin.scholarships.create') }}">Add one
                                                now.</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($scholarships->hasPages())
                        <div class="mt-3">{{ $scholarships->links() }}</div>
                    @endif

                </div>
            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
