@php
    $role = session('role');

    if ($role === 'admin') {
        $primaryLinks = [
            [
                'label' => 'Home',
                'href' => route('admin.dashboard'),
                'active' => ['admin.dashboard'],
                'icon' => 'home',
            ],
            [
                'label' => 'Teachers',
                'href' => route('teachers.index'),
                'active' => ['teachers.*'],
                'icon' => 'users',
            ],
            [
                'label' => 'Students',
                'href' => route('admin.students'),
                'active' => ['admin.students*'],
                'icon' => 'users',
            ],
            [
                'label' => 'Notice',
                'href' => route('admin.notices.index'),
                'active' => ['admin.notices.*'],
                'icon' => 'bell',
            ],
            [
                'label' => 'Collections',
                'href' => route('admin.collections.index'),
                'active' => ['admin.collections.*'],
                'icon' => 'book',
            ],
        ];
    } else {
        $primaryLinks = [
            [
                'label' => 'Home',
                'href' => route('teacher.dashboard'),
                'active' => ['teacher.dashboard'],
                'icon' => 'home',
            ],
            [
                'label' => 'Students',
                'href' => route('students.index'),
                'active' => ['students.*'],
                'icon' => 'users',
            ],
            [
                'label' => 'Attendance',
                'href' => route('attendance.index'),
                'active' => ['attendance.*'],
                'icon' => 'calendar',
            ],
            [
                'label' => 'Notice',
                'href' => route('teacher.dashboard'),
                'active' => ['teacher.dashboard'],
                'icon' => 'bell',
            ],
        ];
    }

    $moreLinks = $role === 'admin'
        ? [
            ['label' => 'Dashboard', 'href' => route('admin.dashboard'), 'active' => ['admin.dashboard']],
            ['label' => 'Students', 'href' => route('admin.students'), 'active' => ['admin.students*']],
            ['label' => 'Teachers', 'href' => route('teachers.index'), 'active' => ['teachers.*']],
            ['label' => 'Branches', 'href' => route('branches.index'), 'active' => ['branches.*']],
            ['label' => 'Subjects', 'href' => route('subjects.index'), 'active' => ['subjects.*']],
            ['label' => 'Collections', 'href' => route('admin.collections.index'), 'active' => ['admin.collections.*']],
            ['label' => 'Notices', 'href' => route('admin.notices.index'), 'active' => ['admin.notices.*']],
        ]
        : [
            ['label' => 'Dashboard', 'href' => route('teacher.dashboard'), 'active' => ['teacher.dashboard']],
            ['label' => 'Students', 'href' => route('students.index'), 'active' => ['students.*']],
            ['label' => 'Attendance', 'href' => route('attendance.index'), 'active' => ['attendance.*']],
            ['label' => 'Fees Tracker', 'href' => route('fees.index'), 'active' => ['fees.*']],
            ['label' => 'Import Students', 'href' => route('students.import'), 'active' => ['students.import*']],
        ];
@endphp

<nav class="mobile-bottom-nav" :class="moreOpen ? 'is-more-open' : ''" aria-label="Mobile Navigation">
    <ul class="mobile-nav-list">
        @foreach ($primaryLinks as $link)
            <li>
                <a href="{{ $link['href'] }}" class="mobile-link" :class="{ 'is-active': !moreOpen && {{ request()->routeIs($link['active']) ? 'true' : 'false' }} }" aria-label="{{ $link['label'] }}">
                    @if ($link['icon'] === 'home')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 21V12H15V21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif ($link['icon'] === 'users')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 21V19C16 17.8954 15.1046 17 14 17H6C4.89543 17 4 17.8954 4 19V21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13C12.2091 13 14 11.2091 14 9C14 6.79086 12.2091 5 10 5C7.79086 5 6 6.79086 6 9C6 11.2091 7.79086 13 10 13Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 8V14" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 11H17" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif ($link['icon'] === 'calendar')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="18" rx="3" stroke-width="1.8"/><path d="M8 2V6M16 2V6M3 10H21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 14L11 16L15 12" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif ($link['icon'] === 'flag')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3V21M6 7H14C15.6569 7 17 8.34315 17 10C17 11.6569 15.6569 13 14 13H6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif ($link['icon'] === 'bell')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 8C18 5.23858 15.7614 3 13 3H11C8.23858 3 6 5.23858 6 8V11.5858C6 12.1162 5.78929 12.6249 5.41421 13L4 14.4142V16H20V14.4142L18.5858 13C18.2107 12.6249 18 12.1162 18 11.5858V8Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 19C9.45841 20.1652 10.5826 21 12 21C13.4174 21 14.5416 20.1652 15 19" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif ($link['icon'] === 'book')
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 4H16C17.1046 4 18 4.89543 18 6V18C18 19.1046 17.1046 20 16 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4Z" stroke-width="1.8"/><path d="M7 8H14M7 12H14" stroke-width="1.8" stroke-linecap="round"/><path d="M18 7H19.5C20.3284 7 21 7.67157 21 8.5V17.5C21 18.3284 20.3284 19 19.5 19H18" stroke-width="1.8" stroke-linecap="round"/></svg>
                    @endif
                    <span>{{ $link['label'] }}</span>
                </a>
            </li>
        @endforeach

        <li>
            <button type="button" class="mobile-link mobile-link-btn" :class="moreOpen ? 'is-active' : ''" @click="toggleMore()" aria-label="More">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="2"/><circle cx="5" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                <span>More</span>
            </button>
        </li>
    </ul>
</nav>

<div
    class="mobile-more-drawer"
    x-show="moreOpen"
    x-transition:enter="drawer-enter"
    x-transition:enter-start="drawer-enter-start"
    x-transition:enter-end="drawer-enter-end"
    x-transition:leave="drawer-leave"
    x-transition:leave-start="drawer-leave-start"
    x-transition:leave-end="drawer-leave-end"
    @click.outside="closeMore()"
    x-cloak
>
    <div class="mobile-more-header">
        <h3>Quick Menu</h3>
        <button type="button" class="icon-btn" @click="closeMore()" aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>

    <ul class="mobile-more-list">
        @foreach ($moreLinks as $link)
            <li>
                <a href="{{ $link['href'] }}" class="mobile-more-link {{ request()->routeIs($link['active']) ? 'is-active' : '' }}" @click="closeMore()">
                    {{ $link['label'] }}
                </a>
            </li>
        @endforeach

        <li>
            <form action="{{ route('logout') }}" method="POST" @submit="closeMore()">
                @csrf
                <button type="submit" class="mobile-more-link mobile-more-logout">Logout</button>
            </form>
        </li>
    </ul>
</div>
