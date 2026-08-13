@extends('layouts.app')
@section('title', 'Notifications')

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

        .main-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .notif-row {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .notif-row:last-child {
            border-bottom: none;
        }

        .notif-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--sm-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notif-icon i {
            color: var(--sm-accent);
            font-size: 1.1rem;
        }

        .channel-badge {
            font-size: .7rem;
            font-weight: 600;
            padding: .2rem .6rem;
            border-radius: 20px;
        }

        .ch-email {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .ch-in_app {
            background: #f0fdf4;
            color: #15803d;
        }

        .ch-both {
            background: #faf5ff;
            color: #7c3aed;
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
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}</div>
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Notifications</h4>
                        <p class="text-muted mb-0">Send and manage notifications to students.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.notifications.deadlines') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-alarm me-1"></i>Send Deadline Reminders
                            </button>
                        </form>
                        <a href="{{ route('admin.notifications.create') }}" class="btn btn-sm text-white"
                            style="background:var(--sm-navy);">
                            <i class="bi bi-megaphone me-1"></i>New Notification
                        </a>
                    </div>
                </div>

                <div class="main-card">
                    <h6 class="fw-bold mb-3">Sent Notifications</h6>

                    @forelse($notifications as $notif)
                        <div class="notif-row">
                            <div class="notif-icon">
                                <i class="bi bi-{{ $notif->is_mass ? 'megaphone' : 'bell' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.9rem;">{{ $notif->subject }}</div>
                                        <div class="text-muted" style="font-size:.8rem;">
                                            {{ Str::limit($notif->body, 100) }}
                                        </div>
                                    </div>
                                    <span class="channel-badge ch-{{ $notif->channel }} ms-3 flex-shrink-0">
                                        {{ ucfirst(str_replace('_', ' ', $notif->channel)) }}
                                    </span>
                                </div>
                                <div class="d-flex gap-3 mt-1" style="font-size:.75rem;color:#6b7280;">
                                    <span><i class="bi bi-person me-1"></i>{{ $notif->sender->full_name }}</span>
                                    <span><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</span>
                                    @if($notif->is_mass)
                                        <span class="text-primary"><i class="bi bi-broadcast me-1"></i>Mass notification</span>
                                    @endif
                                    @if($notif->scholarship)
                                        <span><i class="bi bi-award me-1"></i>{{ $notif->scholarship->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                            No notifications sent yet.
                        </div>
                    @endforelse

                    @if($notifications->hasPages())
                        <div class="mt-3">{{ $notifications->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #e5e7eb;">
                © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
            </div>
        </div>
    </div>
@endsection
