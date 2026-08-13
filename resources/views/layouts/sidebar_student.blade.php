<nav class="sidebar d-flex flex-column py-3" style="position:fixed;top:0;left:0;height:100vh;z-index:100;">
    <div class="px-3 mb-4 d-flex align-items-center gap-2">
        <div
            style="width:36px;height:36px;background:#2563eb;border-radius:8px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-mortarboard-fill text-white"></i>
        </div>
        <span class="text-white fw-bold fs-5">SmartMatch</span>
    </div>
    <ul class="nav flex-column px-2 flex-grow-1">
        <li class="nav-item">
            <a href="{{ route('student.dashboard') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('student.scholarships.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('student.scholarships*') ? 'active' : '' }}">
                <i class="bi bi-book"></i> Scholarships
            </a>
        </li>
        <li class="nav-item">
            <a href="#"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('student.matching*') ? 'active' : '' }}"
                onclick="event.preventDefault(); document.getElementById('rerun-form').submit();">
                <i class="bi bi-stars"></i> Matching Results
            </a>
        </li>
        <form id="rerun-form" method="POST" action="{{ route('student.matching.rerun') }}" style="display:none;">
            @csrf
        </form>
        <li class="nav-item">
            <a href="{{ route('student.applications.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('student.applications*') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> My Applications
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('student.notifications.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Inbox
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="badge rounded-pill bg-danger ms-auto" style="font-size:.65rem;">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link d-flex align-items-center gap-2">
                <i class="bi bi-person"></i> Profile
            </a>
        </li>
    </ul>
    <div class="px-2 pb-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link d-flex align-items-center gap-2 w-100 border-0 bg-transparent">
                <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i>
                <span style="color:#ef4444;">Logout</span>
            </button>
        </form>
    </div>
</nav>
