@extends('layouts.student')
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

        .main-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
        }

        .notif-row {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .notif-row:last-child {
            border-bottom: none;
        }

        .notif-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notif-row.unread {
            background: #f8faff;
            margin: 0 -1.5rem;
            padding: 1rem 1.5rem;
            border-left: 3px solid var(--sm-accent);
        }
    </style>
@endpush

@section('content')
    <div class="p-3 p-md-4">
        <h4 class="fw-bold mb-1">Notifications</h4>
        <p class="text-muted mb-4">Your scholarship alerts and announcements from the scholarship office.</p>

        <div class="main-card">
            @forelse($notifications as $notif)
                @php $isRead = $notif->isReadBy($user); @endphp
                <div class="notif-row {{ !$isRead ? 'unread' : '' }}">
                    <div class="notif-icon" style="background:{{ $notif->scholarship ? '#eff6ff' : '#f0fdf4' }};">
                        <i class="bi bi-{{ $notif->scholarship ? 'award' : 'megaphone' }}"
                            style="color:{{ $notif->scholarship ? '#2563eb' : '#16a34a' }};font-size:1.1rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div class="fw-semibold {{ !$isRead ? '' : 'text-muted' }}" style="font-size:.925rem;">
                                {{ $notif->subject }}
                            </div>
                            <div class="text-muted flex-shrink-0 ms-3" style="font-size:.75rem;">
                                {{ $notif->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-muted mt-1" style="font-size:.85rem;line-height:1.6;">
                            {{ $notif->body }}
                        </div>
                        @if($notif->scholarship)
                            <div class="mt-1" style="font-size:.775rem;color:var(--sm-accent);">
                                <i class="bi bi-award me-1"></i>{{ $notif->scholarship->name }}
                            </div>
                        @endif
                    </div>
                    @if(!$isRead)
                        <div class="flex-shrink-0">
                            <span
                                style="width:8px;height:8px;border-radius:50%;background:var(--sm-accent);display:inline-block;"></span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                    No notifications yet.
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
@endsection
