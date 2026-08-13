@extends('layouts.app')
@section('title', 'Audit Log')

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

        .main-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .log-row {
            display: flex;
            gap: 1rem;
            padding: .75rem 0;
            border-bottom: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }

        .log-row:last-child {
            border-bottom: none;
        }

        .log-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .9rem;
        }

        .action-badge {
            font-size: .7rem;
            font-weight: 700;
            padding: .2rem .6rem;
            border-radius: 6px;
            font-family: monospace;
        }

        .act-auth {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .act-scholarship {
            background: #fff7ed;
            color: #c2410c;
        }

        .act-application {
            background: #f0fdf4;
            color: #15803d;
        }

        .act-document {
            background: #faf5ff;
            color: #7c3aed;
        }

        .act-notification {
            background: #fefce8;
            color: #a16207;
        }

        .act-settings {
            background: #f3f4f6;
            color: #374151;
        }

        .act-profile {
            background: #f0f9ff;
            color: #0369a1;
        }

        .act-consent {
            background: #fdf4ff;
            color: #86198f;
        }

        .act-matching {
            background: #f0fdf4;
            color: #166534;
        }

        .act-other {
            background: #f3f4f6;
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Audit Log</h4>
                        <p class="text-muted mb-0">Complete trail of all system events and user actions.</p>
                    </div>
                    <a href="{{ route('admin.settings.index') }}" class="text-muted text-decoration-none"
                        style="font-size:.875rem;">
                        <i class="bi bi-arrow-left me-1"></i>Back to Settings
                    </a>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.audit.index') }}" class="main-card mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;">Action Contains</label>
                            <input type="text" name="action" class="form-control form-control-sm"
                                placeholder="e.g. auth.login" value="{{ request('action') }}" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;">Action Type</label>
                            <select name="action" class="form-select form-select-sm">
                                <option value="">All Actions</option>
                                @foreach($actionGroups as $key => $label)
                                    <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;">From Date</label>
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                value="{{ request('date_from') }}" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:.8rem;font-weight:600;">To Date</label>
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                value="{{ request('date_to') }}" />
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-outline-secondary flex-grow-1">Filter</button>
                            @if(request()->hasAny(['action', 'date_from', 'date_to']))
                                <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="main-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">System Events</h6>
                        <span class="text-muted" style="font-size:.8rem;">
                            {{ $logs->total() }} total events
                        </span>
                    </div>

                    @forelse($logs as $log)
                        @php
                            $prefix = explode('.', $log->action)[0] ?? 'other';
                            $actionClass = match ($prefix) {
                                'auth' => 'act-auth',
                                'scholarship' => 'act-scholarship',
                                'application' => 'act-application',
                                'document' => 'act-document',
                                'notification' => 'act-notification',
                                'settings' => 'act-settings',
                                'profile' => 'act-profile',
                                'consent' => 'act-consent',
                                'matching' => 'act-matching',
                                'criteria' => 'act-scholarship',
                                default => 'act-other',
                            };
                            $iconMap = [
                                'auth' => 'bi-person-check',
                                'scholarship' => 'bi-award',
                                'application' => 'bi-journal-check',
                                'document' => 'bi-file-earmark',
                                'notification' => 'bi-bell',
                                'settings' => 'bi-gear',
                                'profile' => 'bi-person',
                                'consent' => 'bi-shield-check',
                                'matching' => 'bi-stars',
                            ];
                            $icon = $iconMap[$prefix] ?? 'bi-activity';
                        @endphp

                        <div class="log-row">
                            <div class="log-icon" style="background:#f3f4f6;">
                                <i class="bi {{ $icon }} text-muted"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="action-badge {{ $actionClass }}">{{ $log->action }}</span>
                                    @if($log->entity_type)
                                        <span class="text-muted" style="font-size:.75rem;">
                                            {{ $log->entity_type }}
                                            @if($log->entity_id) #{{ $log->entity_id }} @endif
                                        </span>
                                    @endif
                                </div>
                                @if($log->description)
                                    <div style="font-size:.825rem;color:#374151;">{{ $log->description }}</div>
                                @endif
                                @if($log->new_values)
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.7rem;"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#log-{{ $log->id }}">
                                            <i class="bi bi-code-slash me-1"></i>View Details
                                        </button>
                                        <div class="collapse mt-1" id="log-{{ $log->id }}">
                                            <code
                                                style="font-size:.72rem;background:#f3f4f6;padding:.4rem .75rem;border-radius:6px;display:block;word-break:break-all;white-space:pre-wrap;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</code>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end flex-shrink-0" style="min-width:160px;">
                                @if($log->user)
                                    <div class="fw-semibold" style="font-size:.8rem;">{{ $log->user->full_name }}</div>
                                    <div class="text-muted" style="font-size:.72rem;">{{ $log->user->role->name }}</div>
                                @else
                                    <div class="text-muted" style="font-size:.8rem;">System</div>
                                @endif
                                <div class="text-muted" style="font-size:.72rem;">
                                    {{ $log->created_at->format('M d, Y') }}<br>
                                    {{ $log->created_at->format('h:i:s A') }}
                                </div>
                                @if($log->ip_address)
                                    <div class="text-muted" style="font-size:.7rem;">
                                        <i class="bi bi-geo me-1"></i>{{ $log->ip_address }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                            No audit log entries found.
                        </div>
                    @endforelse

                    @if($logs->hasPages())
                        <div class="mt-3">{{ $logs->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
