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
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.students.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Manage Students
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.scholarships.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.scholarships*') || request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Scholarships &amp; Reports
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.applications.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> Manage Applications
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.notifications.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i> Notifications
            </a>
        </li>
        <a href="{{ route('admin.settings.index') }}"
            class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.audit*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>
    </ul>

    {{-- Admin profile at bottom --}}
    <div class="px-2 mt-auto">
        <div style="border-top:1px solid rgba(255,255,255,0.1);padding-top:.75rem;margin-bottom:.5rem;">
            <a href="{{ route('admin.profile.show') }}"
               class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                <div style="width:28px;height:28px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->first_name,0,1).substr(auth()->user()->last_name,0,1)) }}
                </div>
                <div style="line-height:1.2;min-width:0;">
                    <div style="font-size:.8rem;color:#fff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->first_name }}
                    </div>
                    <div style="font-size:.68rem;color:rgba(255,255,255,0.5);">View Profile</div>
                </div>
            </a>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link d-flex align-items-center gap-2 w-100 border-0 bg-transparent">
                <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i>
                <span style="color:#ef4444;">Logout</span>
            </button>
        </form>
    </div>
</nav>
