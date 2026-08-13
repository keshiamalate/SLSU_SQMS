@extends('layouts.app')
@section('title', 'Manage Students')

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

        .student-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--sm-navy);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: .85rem;
            flex-shrink: 0;
        }

        .action-btn {
            background: none;
            border: none;
            padding: .25rem .5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: .875rem;
        }

        .action-btn:hover {
            background: #f3f4f6;
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Manage Students</h4>
                        <p class="text-muted mb-0">View and manage all registered student accounts.</p>
                    </div>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-sm text-white"
                        style="background:var(--sm-navy);">
                        <i class="bi bi-person-plus me-1"></i>Add Student
                    </a>
                </div>

                <div class="main-card">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.students.index') }}"
                        class="d-flex flex-wrap gap-2 align-items-center mb-4">
                        <div class="flex-grow-1" style="max-width:320px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Search by name or ID..." value="{{ request('search') }}" />
                            </div>
                        </div>
                        <select name="status" class="form-select form-select-sm" style="max-width:180px;">
                            <option value="">All Students</option>
                            <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Profile Complete
                            </option>
                            <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>Profile
                                Incomplete</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                        @endif
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table align-middle" style="font-size:.875rem;">
                            <thead style="border-bottom:2px solid #e5e7eb;">
                                <tr class="text-muted" style="font-size:.8rem;">
                                    <th>Student</th>
                                    <th>ID</th>
                                    <th>GPA</th>
                                    <th>Course</th>
                                    <th>Profile</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="student-avatar">
                                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $student->full_name }}</div>
                                                    <div class="text-muted" style="font-size:.75rem;">{{ $student->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $student->institutional_id }}</td>
                                        <td>{{ $student->studentProfile?->cumulative_gpa ?? '—' }}</td>
                                        <td>{{ $student->studentProfile?->course ?? '—' }}</td>
                                        <td>
                                            @if($student->studentProfile?->isComplete())
                                                <span class="badge text-bg-success">Complete</span>
                                            @else
                                                <span class="badge text-bg-warning text-dark">Incomplete</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->is_active)
                                                <span class="badge text-bg-success">Active</span>
                                            @else
                                                <span class="badge text-bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.students.show', $student) }}"
                                                class="action-btn text-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.students.toggle', $student) }}"
                                                class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="action-btn text-warning"
                                                    title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i
                                                        class="bi bi-{{ $student->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-people fs-3 d-block mb-2"></i>
                                            No students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->hasPages())
                        <div class="mt-3">{{ $students->links() }}</div>
                    @endif

                </div>
            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
