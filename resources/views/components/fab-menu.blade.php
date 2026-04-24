@php
    $role = session('role');

    $fabItems = $role === 'admin'
        ? [
            ['label' => 'Add Teacher', 'href' => route('teachers.create')],
            ['label' => 'Students Overview', 'href' => route('admin.students')],
            ['label' => 'Add Branch', 'href' => route('branches.create')],
            ['label' => 'Add Subject', 'href' => route('subjects.create')],
            ['label' => 'Book Collections', 'href' => route('admin.collections.index')],
        ]
        : [
            ['label' => 'Add Student', 'href' => route('students.create')],
            ['label' => 'Take Attendance', 'href' => route('attendance.index')],
            ['label' => 'View Reports', 'href' => route('fees.report')],
        ];
@endphp

<div class="fab-wrapper" x-show="!moreOpen" :class="moreOpen ? 'is-hidden' : ''" x-cloak>
    <div
        class="fab-menu-list"
        x-show="fabOpen"
        x-transition:enter="fab-enter"
        x-transition:enter-start="fab-enter-start"
        x-transition:enter-end="fab-enter-end"
        x-transition:leave="fab-leave"
        x-transition:leave-start="fab-leave-start"
        x-transition:leave-end="fab-leave-end"
        @click.outside="closeFab()"
    >
        @foreach ($fabItems as $item)
            <a href="{{ $item['href'] }}" class="fab-menu-item" @click="closeFab()">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>

    <button
        type="button"
        class="fab-toggle"
        :class="fabOpen ? 'is-open' : ''"
        @click="toggleFab()"
        aria-label="Quick action menu"
    >
        <svg class="fab-icon fab-icon-plus" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke-width="2.2" stroke-linecap="round"/><path d="M5 12H19" stroke-width="2.2" stroke-linecap="round"/></svg>
        <svg class="fab-icon fab-icon-close" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6L18 18" stroke-width="2.2" stroke-linecap="round"/><path d="M18 6L6 18" stroke-width="2.2" stroke-linecap="round"/></svg>
    </button>
</div>
