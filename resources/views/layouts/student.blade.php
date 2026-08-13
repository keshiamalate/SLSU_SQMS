<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'SmartMatch') — SLSU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <style>
        :root {
            --sm-navy: #0d2b55;
            --sm-blue: #1a4a8a;
            --sm-accent: #2563eb;
            --sm-light: #f0f4ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
        }

        /* ── Desktop Sidebar ─────────────────────────────── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--sm-navy);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            padding: 1rem 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            border-radius: 8px;
            margin: 2px 8px;
            padding: .6rem .9rem;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }

        .sidebar .brand {
            padding: .5rem 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .sidebar .brand-icon {
            width: 34px;
            height: 34px;
            background: #2563eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Content area ────────────────────────────────── */
        .content-area {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Desktop topbar ──────────────────────────────── */
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

        /* ── Mobile topbar ───────────────────────────────── */
        .mobile-topbar {
            display: none;
            height: 56px;
            background: var(--sm-navy);
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            position: sticky;
            top: 0;
            z-index: 200;
        }

        .mobile-topbar .brand-text {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .mobile-topbar .hamburger {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.4rem;
            padding: .25rem;
            line-height: 1;
        }

        /* ── Mobile bottom navigation ────────────────────── */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            z-index: 200;
            padding: .4rem 0 .5rem;
        }

        .mobile-bottom-nav .nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .mobile-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .1rem;
            color: #9ca3af;
            text-decoration: none;
            font-size: .65rem;
            font-weight: 500;
            min-width: 52px;
            position: relative;
        }

        .mobile-bottom-nav a i {
            font-size: 1.3rem;
        }

        .mobile-bottom-nav a.active {
            color: var(--sm-navy);
        }

        .mobile-bottom-nav a .notif-dot {
            position: absolute;
            top: -2px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* ── Avatar ──────────────────────────────────────── */
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
            flex-shrink: 0;
        }

        /* ── Mobile: hide desktop sidebar, show mobile nav ─ */
        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }

            .content-area {
                margin-left: 0;
            }

            .topbar {
                display: none;
            }

            .mobile-topbar {
                display: flex;
            }

            .mobile-bottom-nav {
                display: block;
            }

            .page-content {
                padding-bottom: 75px;
            }

            /* space for bottom nav */
        }

        /* ── Offcanvas sidebar for mobile ────────────────── */
        .offcanvas-sidebar {
            width: 260px !important;
            background: var(--sm-navy) !important;
        }

        .offcanvas-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            border-radius: 8px;
            margin: 2px 8px;
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .65rem .9rem;
        }

        .offcanvas-sidebar .nav-link:hover,
        .offcanvas-sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }

        .offcanvas-sidebar .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
        }

        .offcanvas-sidebar .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- ── DESKTOP SIDEBAR ────────────────────────────────── --}}
    <nav class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-mortarboard-fill text-white" style="font-size:.95rem;"></i>
            </div>
            <span class="text-white fw-bold">SmartMatch</span>
        </div>
        @include('layouts.partials.student_nav')
        <div class="px-2 pb-2 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 bg-transparent" style="color:#ef4444;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- ── MOBILE TOP BAR ──────────────────────────────────── --}}
    <div class="mobile-topbar">
        <button class="hamburger" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
            <i class="bi bi-list"></i>
        </button>
        <span class="brand-text">SmartMatch</span>
        <a href="{{ route('student.notifications.index') }}" class="position-relative text-white text-decoration-none">
            <i class="bi bi-bell fs-5"></i>
            @if(isset($unreadCount) && $unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="font-size:.55rem;">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    {{-- ── MOBILE OFFCANVAS MENU ───────────────────────────── --}}
    <div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon"
                    style="width:30px;height:30px;background:#2563eb;border-radius:7px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-mortarboard-fill text-white" style="font-size:.8rem;"></i>
                </div>
                <span class="text-white fw-bold">SmartMatch</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 pt-2">
            <ul class="nav flex-column px-1">
                @include('layouts.partials.student_nav')
            </ul>
            <div class="px-2 mt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100 border-0 bg-transparent" style="color:#ef4444;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT ────────────────────────────────────── --}}
    <div class="content-area">

        {{-- Desktop topbar --}}
        <div class="topbar">
            <span style="font-weight:700;">SmartMatch</span>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('student.notifications.index') }}"
                    class="position-relative text-muted text-decoration-none">
                    <i class="bi bi-bell fs-5"></i>
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size:.6rem;">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>
                <div class="d-none d-sm-block" style="text-align:right;line-height:1.2;">
                    <div style="font-weight:600;font-size:.9rem;">{{ auth()->user()->full_name }}</div>
                    <div style="font-size:.75rem;color:#6b7280;">student</div>
                </div>
                <div class="avatar">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                </div>
            </div>
        </div>

        {{-- Page content --}}
        <div class="flex-grow-1 page-content">
            @yield('content')
        </div>

        {{-- Footer --}}
        <div class="text-center text-muted py-3" style="font-size:.72rem;border-top:1px solid #e5e7eb;">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
        </div>
    </div>

    {{-- ── MOBILE BOTTOM NAVIGATION ────────────────────────── --}}
    <nav class="mobile-bottom-nav">
        <div class="nav-items">
            <a href="{{ route('student.dashboard') }}"
                class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2{{ request()->routeIs('student.dashboard') ? '-fill' : '' }}"></i>
                Home
            </a>
            <a href="{{ route('student.scholarships.index') }}"
                class="{{ request()->routeIs('student.scholarships*') ? 'active' : '' }}">
                <i class="bi bi-book{{ request()->routeIs('student.scholarships*') ? '-fill' : '' }}"></i>
                Scholarships
            </a>
            <a href="{{ route('student.matching.index') }}"
                class="{{ request()->routeIs('student.matching*') ? 'active' : '' }}">
                <i class="bi bi-stars"></i>
                Matches
            </a>
            <a href="{{ route('student.applications.index') }}"
                class="{{ request()->routeIs('student.applications*') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i>
                My Apps
            </a>
            <a href="{{ route('student.notifications.index') }}"
                class="{{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
                <i class="bi bi-bell{{ request()->routeIs('student.notifications*') ? '-fill' : '' }}"></i>
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="notif-dot"></span>
                @endif
                Inbox
            </a>
            <a href="{{ route('student.profile.show') }}"
                class="{{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                <i class="bi bi-person"></i>
                Profile
            </a>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
