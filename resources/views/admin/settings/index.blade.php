@extends('layouts.app')
@section('title', 'Settings')

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

        .settings-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .settings-card h6 {
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

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
            font-size: .875rem;
        }

        .form-control:focus,
        .form-select:focus {
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

        .setting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .6rem 0;
            border-bottom: 1px solid #f3f4f6;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .setting-row:last-child {
            border-bottom: none;
        }

        .setting-label {
            font-size: .875rem;
            font-weight: 500;
            color: #111;
        }

        .setting-desc {
            font-size: .775rem;
            color: #6b7280;
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Settings</h4>
                        <p class="text-muted mb-0">Manage system configuration and security settings.</p>
                    </div>
                    <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-journal-text me-1"></i>View Audit Log
                    </a>
                </div>

                <div class="row g-3">
                    <div class="col-lg-8">

                        {{-- System Settings --}}
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf @method('PATCH')

                            <div class="settings-card">
                                <h6><i class="bi bi-gear me-2"></i>General Settings</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Consent Form Version</label>
                                        <input type="text" name="consent_version" class="form-control"
                                            value="{{ $settings['consent_version']->value ?? '1.0' }}" />
                                        <div class="form-text">Increment this to force all students to re-sign consent.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Session Timeout (minutes)</label>
                                        <input type="number" name="session_timeout_minutes" class="form-control" min="5"
                                            max="480" value="{{ $settings['session_timeout_minutes']->value ?? 30 }}" />
                                        <div class="form-text">Admin session idle timeout.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Max Upload Size (MB)</label>
                                        <input type="number" name="max_upload_size_mb" class="form-control" min="1" max="50"
                                            value="{{ $settings['max_upload_size_mb']->value ?? 5 }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Allowed Upload Types</label>
                                        <input type="text" name="allowed_upload_types" class="form-control"
                                            placeholder="pdf,jpg,jpeg,png"
                                            value="{{ $settings['allowed_upload_types']->value ?? 'pdf,jpg,jpeg,png' }}" />
                                        <div class="form-text">Comma-separated file extensions.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h6><i class="bi bi-robot me-2"></i>AI / ML Settings</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Minimum RF Accuracy</label>
                                        <input type="number" name="min_rf_accuracy" class="form-control" step="0.01" min="0"
                                            max="1" value="{{ $settings['min_rf_accuracy']->value ?? 0.85 }}" />
                                        <div class="form-text">Minimum accuracy before ML model can be deployed (0–1).</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Minimum RF F1 Score</label>
                                        <input type="number" name="min_rf_f1" class="form-control" step="0.01" min="0"
                                            max="1" value="{{ $settings['min_rf_f1']->value ?? 0.80 }}" />
                                        <div class="form-text">Minimum F1 score before ML model can be deployed (0–1).</div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-save mb-4">
                                <i class="bi bi-check-lg me-1"></i>Save Settings
                            </button>
                        </form>

                        {{-- Change Password --}}
                        <form method="POST" action="{{ route('admin.settings.password') }}">
                            @csrf @method('PATCH')
                            <div class="settings-card">
                                <h6><i class="bi bi-shield-lock me-2"></i>Change Password</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" name="current_password"
                                            class="form-control @error('current_password') is-invalid @enderror" />
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror" />
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-4"
                                        style="border-radius:8px;font-weight:600;">
                                        <i class="bi bi-key me-1"></i>Change Password
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                    {{-- Right column --}}
                    <div class="col-lg-4">

                        {{-- System info --}}
                        <div class="settings-card">
                            <h6><i class="bi bi-info-circle me-2"></i>System Information</h6>
                            <div class="setting-row">
                                <div>
                                    <div class="setting-label">Application</div>
                                    <div class="setting-desc">SmartMatch v1.0</div>
                                </div>
                            </div>
                            <div class="setting-row">
                                <div>
                                    <div class="setting-label">Laravel Version</div>
                                    <div class="setting-desc">{{ app()->version() }}</div>
                                </div>
                            </div>
                            <div class="setting-row">
                                <div>
                                    <div class="setting-label">PHP Version</div>
                                    <div class="setting-desc">{{ PHP_VERSION }}</div>
                                </div>
                            </div>
                            <div class="setting-row">
                                <div>
                                    <div class="setting-label">Environment</div>
                                    <div class="setting-desc">{{ app()->environment() }}</div>
                                </div>
                            </div>
                            <div class="setting-row">
                                <div>
                                    <div class="setting-label">Timezone</div>
                                    <div class="setting-desc">{{ config('app.timezone') }}</div>
                                </div>
                            </div>
                            <div class="setting-row">
                                <div>
                                    <div class="setting-label">Current Date</div>
                                    <div class="setting-desc">{{ now()->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick links --}}
                        <div class="settings-card">
                            <h6><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('admin.audit.index') }}"
                                    class="btn btn-sm btn-outline-secondary text-start">
                                    <i class="bi bi-journal-text me-2"></i>View Audit Log
                                </a>
                                <a href="{{ route('admin.analytics.index') }}"
                                    class="btn btn-sm btn-outline-secondary text-start">
                                    <i class="bi bi-bar-chart me-2"></i>View Analytics
                                </a>
                                <a href="{{ route('admin.notifications.create') }}"
                                    class="btn btn-sm btn-outline-secondary text-start">
                                    <i class="bi bi-megaphone me-2"></i>Send Notification
                                </a>
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
