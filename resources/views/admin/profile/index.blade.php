@extends('layouts.app')
@section('title', 'Admin Profile')

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
            overflow-x: hidden;
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

        .profile-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .profile-card h6 {
            font-weight: 700;
            color: var(--sm-navy);
            padding-bottom: .75rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 500;
            font-size: .875rem;
            color: #374151;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
            font-size: .875rem;
        }

        .form-control:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .btn-save {
            background: var(--sm-navy);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .6rem 1.5rem;
            font-weight: 600;
            font-size: .875rem;
        }

        .btn-save:hover {
            background: var(--sm-accent);
            color: #fff;
        }

        .info-label {
            font-size: .72rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: .2rem;
        }

        .info-value {
            font-size: .9rem;
            font-weight: 500;
            color: #111;
        }

        .avatar-lg {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--sm-navy);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .tab-nav .nav-link {
            color: #6b7280;
            font-weight: 500;
            border: none;
            padding: .65rem 1.25rem;
            border-bottom: 2px solid transparent;
            font-size: .875rem;
        }

        .tab-nav .nav-link.active {
            color: var(--sm-navy);
            border-bottom-color: var(--sm-navy);
        }

        .activity-row {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            padding: .6rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-row:last-child {
            border-bottom: none;
        }

        .act-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .85rem;
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
                    <div style="font-weight:600;font-size:.9rem;">{{ $admin->full_name }}</div>
                    <div
                        style="width:36px;height:36px;border-radius:50%;background:var(--sm-navy);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:.875rem;">
                        {{ strtoupper(substr($admin->first_name, 0, 1) . substr($admin->last_name, 0, 1)) }}
                    </div>
                </div>
            </div>

            <div class="p-4">

                @foreach(['success', 'error'] as $msg)
                    @if(session($msg))
                        <div
                            class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show rounded-3 mb-3">
                            {{ session($msg) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endforeach

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li style="font-size:.875rem;">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                    <div class="avatar-lg">
                        {{ strtoupper(substr($admin->first_name, 0, 1) . substr($admin->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $admin->full_name }}</h4>
                        <div class="text-muted" style="font-size:.875rem;">
                            {{ $admin->institutional_id }} &nbsp;•&nbsp; {{ $admin->email }}
                        </div>
                        <span class="badge mt-1" style="font-size:.72rem;background:#eff6ff;color:#1d4ed8;">
                            {{ ucfirst(str_replace('_', ' ', $admin->role->name)) }}
                        </span>
                    </div>
                </div>

                {{-- Tabs --}}
                <ul class="nav tab-nav border-bottom mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                            <i class="bi bi-person me-1"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#security" data-bs-toggle="tab">
                            <i class="bi bi-shield-lock me-1"></i>Security
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#activity" data-bs-toggle="tab">
                            <i class="bi bi-clock-history me-1"></i>Recent Activity
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- Profile Tab --}}
                    <div class="tab-pane fade show active" id="profile">
                        <div class="row g-3">
                            <div class="col-lg-7">
                                <form method="POST" action="{{ route('admin.profile.update') }}">
                                    @csrf @method('PATCH')
                                    <div class="profile-card">
                                        <h6><i class="bi bi-person me-2"></i>Personal Information</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">First Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="first_name" class="form-control"
                                                    value="{{ old('first_name', $admin->first_name) }}" required />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Middle Name</label>
                                                <input type="text" name="middle_name" class="form-control"
                                                    value="{{ old('middle_name', $admin->middle_name) }}" />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Last Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="last_name" class="form-control"
                                                    value="{{ old('last_name', $admin->last_name) }}" required />
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Email Address <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email', $admin->email) }}" required />
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Admin ID</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $admin->institutional_id }}" disabled />
                                                <div class="form-text">Admin ID cannot be changed.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn-save">
                                        <i class="bi bi-check-lg me-1"></i>Save Changes
                                    </button>
                                </form>
                            </div>

                            <div class="col-lg-5">
                                <div class="profile-card">
                                    <h6><i class="bi bi-info-circle me-2"></i>Account Details</h6>
                                    <div class="mb-3">
                                        <div class="info-label">Role</div>
                                        <div class="info-value">
                                            {{ ucfirst(str_replace('_', ' ', $admin->role->name)) }}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">Account Status</div>
                                        <div class="info-value">
                                            @if($admin->is_active)
                                                <span class="text-success">
                                                    <i class="bi bi-check-circle me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">Registered</div>
                                        <div class="info-value">{{ $admin->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <div class="info-label">Last Login</div>
                                        <div class="info-value">
                                            {{ $admin->last_login_at
        ? $admin->last_login_at->format('M d, Y h:i A')
        : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Security Tab --}}
                    <div class="tab-pane fade" id="security">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <form method="POST" action="{{ route('admin.profile.password') }}">
                                    @csrf @method('PATCH')
                                    <div class="profile-card">
                                        <h6><i class="bi bi-key me-2"></i>Change Password</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Current Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="current_password"
                                                class="form-control @error('current_password') is-invalid @enderror" />
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Minimum 8 characters" />
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control" />
                                        </div>
                                        <div class="alert alert-warning rounded-3" style="font-size:.8rem;">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Admin accounts have access to sensitive student data. Use a strong unique
                                            password.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-4"
                                        style="border-radius:8px;font-weight:600;">
                                        <i class="bi bi-shield-check me-1"></i>Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Activity Tab --}}
                    <div class="tab-pane fade" id="activity">
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="profile-card">
                                    <h6><i class="bi bi-clock-history me-2"></i>Recent System Activity</h6>
                                    @forelse($recentActivity as $log)
                                        @php
                                            $prefix = explode('.', $log->action)[0] ?? 'other';
                                            $iconMap = [
                                                'auth' => ['bi-person-check', '#eff6ff', '#1d4ed8'],
                                                'scholarship' => ['bi-award', '#fff7ed', '#c2410c'],
                                                'application' => ['bi-journal-check', '#f0fdf4', '#15803d'],
                                                'notification' => ['bi-bell', '#fefce8', '#a16207'],
                                                'settings' => ['bi-gear', '#f3f4f6', '#374151'],
                                                'profile' => ['bi-person', '#f0f9ff', '#0369a1'],
                                                'document' => ['bi-file-earmark', '#faf5ff', '#7c3aed'],
                                            ];
                                            [$icon, $bg, $color] = $iconMap[$prefix] ?? ['bi-activity', '#f3f4f6', '#6b7280'];
                                        @endphp
                                        <div class="activity-row">
                                            <div class="act-icon" style="background:{{ $bg }};">
                                                <i class="bi {{ $icon }}" style="color:{{ $color }};"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div>
                                                    <code
                                                        style="background:#f3f4f6;padding:.1rem .4rem;border-radius:4px;font-size:.72rem;">
                                                            {{ $log->action }}
                                                        </code>
                                                </div>
                                                @if($log->description)
                                                    <div class="text-muted" style="font-size:.775rem;">
                                                        {{ $log->description }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-muted flex-shrink-0 text-end" style="font-size:.72rem;">
                                                {{ $log->created_at->format('M d') }}<br>
                                                {{ $log->created_at->format('h:i A') }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4" style="font-size:.875rem;">
                                            <i class="bi bi-clock-history fs-4 d-block mb-2"></i>
                                            No activity recorded yet.
                                        </div>
                                    @endforelse
                                    <div class="text-end mt-2">
                                        <a href="{{ route('admin.audit.index') }}"
                                            style="font-size:.8rem;color:var(--sm-accent);">
                                            View Full Audit Log →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
