<li class="nav-item">
    <a href="{{ route('student.dashboard') }}"
        class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2"></i> Dashboard
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('student.scholarships.index') }}"
        class="nav-link {{ request()->routeIs('student.scholarships*') ? 'active' : '' }}">
        <i class="bi bi-book"></i> Scholarships
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('student.matching.index') }}"
        class="nav-link {{ request()->routeIs('student.matching*') ? 'active' : '' }}">
        <i class="bi bi-stars"></i> Matching Results
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('student.applications.index') }}"
        class="nav-link {{ request()->routeIs('student.applications*') ? 'active' : '' }}">
        <i class="bi bi-journal-check"></i> My Applications
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('student.notifications.index') }}"
        class="nav-link {{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
        <i class="bi bi-bell"></i> Inbox
        @if(isset($unreadCount) && $unreadCount > 0)
            <span class="badge rounded-pill bg-danger ms-auto" style="font-size:.65rem;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('student.profile.show') }}"
        class="nav-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
        <i class="bi bi-person"></i> Profile
    </a>
</li>
