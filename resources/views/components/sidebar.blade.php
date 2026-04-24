@php
    $role = session('role');

    $items = $role === 'admin'
        ? [
            ['label' => 'Home', 'route' => route('admin.dashboard'), 'active' => ['admin.dashboard']],
            ['label' => 'Students', 'route' => route('admin.students'), 'active' => ['admin.students*']],
            ['label' => 'Teachers', 'route' => route('teachers.index'), 'active' => ['teachers.*']],
            ['label' => 'Branches', 'route' => route('branches.index'), 'active' => ['branches.*']],
            ['label' => 'Subjects', 'route' => route('subjects.index'), 'active' => ['subjects.*']],
            ['label' => 'Collections', 'route' => route('admin.collections.index'), 'active' => ['admin.collections.*']],
            ['label' => 'Notices', 'route' => route('admin.notices.index'), 'active' => ['admin.notices.*']],
        ]
        : [
            ['label' => 'Home', 'route' => route('teacher.dashboard'), 'active' => ['teacher.dashboard']],
            ['label' => 'Students', 'route' => route('students.index'), 'active' => ['students.*']],
            ['label' => 'Attendance', 'route' => route('attendance.index'), 'active' => ['attendance.*']],
            ['label' => 'Fees', 'route' => route('fees.index'), 'active' => ['fees.*']],
        ];
@endphp

<aside class="desktop-sidebar">
    <div class="glass-card sidebar-card">
        <div class="brand">
            <div class="brand-logo">
                <img src="{{ asset('logo.png') }}" alt="Alor Disha">
            </div>
            <div>
                <h2>Alor Disha</h2>
                <span>{{ ucfirst($role ?? 'user') }} panel</span>
            </div>
        </div>

        <ul class="menu-list">
            @foreach ($items as $item)
                <li>
                    <a href="{{ $item['route'] }}" class="menu-link {{ request()->routeIs($item['active']) ? 'is-active' : '' }}">
                        @if ($item['label'] === 'Home')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 21V12H15V21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($item['label'] === 'Teachers' || $item['label'] === 'Students')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 21V19C16 17.8954 15.1046 17 14 17H6C4.89543 17 4 17.8954 4 19V21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13C12.2091 13 14 11.2091 14 9C14 6.79086 12.2091 5 10 5C7.79086 5 6 6.79086 6 9C6 11.2091 7.79086 13 10 13Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 8V14" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 11H17" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($item['label'] === 'Attendance')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="18" rx="3" stroke-width="1.8"/><path d="M8 2V6M16 2V6M3 10H21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 14L11 16L15 12" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($item['label'] === 'Branches')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3V21M6 7H14C15.6569 7 17 8.34315 17 10C17 11.6569 15.6569 13 14 13H6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($item['label'] === 'Subjects' || $item['label'] === 'Fees')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 4H18C19.1046 4 20 4.89543 20 6V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V6C4 4.89543 4.89543 4 6 4Z" stroke-width="1.8"/><path d="M8 8H16M8 12H16M8 16H12" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @elseif ($item['label'] === 'Collections')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 4H16C17.1046 4 18 4.89543 18 6V18C18 19.1046 17.1046 20 16 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4Z" stroke-width="1.8"/><path d="M7 8H14M7 12H14" stroke-width="1.8" stroke-linecap="round"/><path d="M18 7H19.5C20.3284 7 21 7.67157 21 8.5V17.5C21 18.3284 20.3284 19 19.5 19H18" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @elseif ($item['label'] === 'Notices')
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 8C18 4.68629 15.3137 2 12 2C8.68629 2 6 4.68629 6 8V11.5858C6 12.1162 5.78929 12.6249 5.41421 13L4 14.4142V16H20V14.4142L18.5858 13C18.2107 12.6249 18 12.1162 18 11.5858V8Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endif

                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
