@extends('layouts.app')

@section('title', 'Admin Students')

@section('content')
    <style>
        .students-summary-card {
            border-radius: 18px;
        }

        .students-search-form {
            max-width: 420px;
            width: 100%;
        }

        .students-filter-grid {
            display: grid;
            grid-template-columns: minmax(220px, 1.5fr) repeat(3, minmax(180px, 1fr)) auto;
            gap: .6rem;
            align-items: center;
        }

        .students-filter-control {
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--glass-border) 62%, transparent);
            background: color-mix(in srgb, var(--color-surface) 90%, transparent);
            color: var(--color-text);
            padding-inline: .85rem;
        }

        .students-filter-control:focus {
            border-color: color-mix(in srgb, var(--color-primary) 55%, transparent);
            box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--color-primary) 25%, transparent);
            background: color-mix(in srgb, var(--color-surface) 94%, transparent);
            color: var(--color-text);
        }

        .students-clear-btn {
            min-height: 42px;
            border-radius: 12px;
            padding-inline: .95rem;
            font-weight: 600;
        }

        .students-summary-row {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
            margin-top: .75rem;
            margin-bottom: .6rem;
        }

        .summary-pill {
            border: 1px solid color-mix(in srgb, var(--glass-border) 65%, transparent);
            background: color-mix(in srgb, var(--color-surface) 90%, transparent);
            border-radius: 999px;
            padding: .25rem .66rem;
            font-size: .82rem;
            font-weight: 600;
            color: var(--color-text-soft);
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .summary-pill-value {
            border-radius: 999px;
            padding: .02rem .42rem;
            border: 1px solid color-mix(in srgb, var(--glass-border) 62%, transparent);
            color: var(--color-text);
            font-weight: 700;
            min-width: 1.6rem;
            text-align: center;
        }

        .students-search-input {
            height: 2.55rem;
            border-radius: 999px;
            padding-inline: .95rem;
        }

        .students-search-btn {
            width: 2.55rem;
            height: 2.55rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .inactive-alert-list {
            margin: 0;
            padding-left: 1.1rem;
            color: var(--color-text-soft);
        }

        .teacher-students-card {
            border-radius: 18px;
        }

        .teacher-contact {
            color: var(--color-text-soft);
            margin-bottom: 0;
            word-break: break-word;
        }

        .students-table {
            min-width: 980px;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 0;
            --bs-table-bg: transparent;
            --bs-table-color: var(--color-text);
            --bs-table-border-color: color-mix(in srgb, var(--glass-border) 55%, transparent);
        }

        .students-table > :not(caption) > * > * {
            background-color: transparent !important;
            color: var(--color-text) !important;
            border-bottom-color: color-mix(in srgb, var(--glass-border) 55%, transparent) !important;
        }

        .students-table th,
        .students-table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .students-table td[data-label="Subject&Year"] {
            white-space: normal;
        }

        .students-table thead th {
            color: var(--color-text-soft) !important;
            background: color-mix(in srgb, var(--color-surface-muted) 58%, transparent) !important;
        }

        .students-table thead th:nth-child(6),
        .students-table tbody td:nth-child(6) {
            text-align: center;
        }

        .students-table thead th:nth-child(7),
        .students-table tbody td:nth-child(7) {
            text-align: right;
        }

        .student-photo {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--color-border);
        }

        .student-name {
            font-weight: 700;
            white-space: normal;
            line-height: 1.25;
            word-break: break-word;
        }

        .student-number {
            min-width: 130px;
            color: var(--color-text-soft);
            font-weight: 600;
        }

        .subject-year-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            min-width: 0;
            max-width: 100%;
            white-space: normal;
        }

        .subject-year-text {
            color: var(--color-text-soft);
            font-weight: 500;
            white-space: normal;
        }

        .subject-year-chip {
            border: 1px solid var(--color-border);
            border-radius: 999px;
            padding: .2rem .55rem;
            font-size: .76rem;
            line-height: 1.2;
            background: color-mix(in srgb, #f59e0b 14%, var(--color-surface));
            color: color-mix(in srgb, #fbbf24 84%, var(--color-text));
            border-color: color-mix(in srgb, #fbbf24 32%, var(--color-border));
        }

        .status-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100%;
        }

        .status-indicator {
            width: .72rem;
            height: .72rem;
            border-radius: 50%;
            display: inline-block;
        }

        .status-indicator.active {
            background: #22c55e;
            box-shadow: 0 0 0.45rem #22c55e, 0 0 1rem color-mix(in srgb, #22c55e 75%, transparent);
        }

        .status-indicator.inactive {
            background: #ef4444;
            box-shadow: 0 0 0.45rem #ef4444, 0 0 1rem color-mix(in srgb, #ef4444 75%, transparent);
        }

        .student-view-btn {
            border-radius: 999px;
            height: 2.1rem;
            padding: 0 .95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border-width: 1px;
        }

        .teacher-students-card .table-responsive {
            border: 1px solid color-mix(in srgb, var(--glass-border) 62%, transparent);
            border-radius: 14px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .mobile-students-list {
            display: none;
        }

        .mobile-student-card {
            border: 1px solid color-mix(in srgb, var(--glass-border) 65%, transparent);
            border-radius: 16px;
            margin-bottom: .8rem;
            background:
                linear-gradient(165deg, color-mix(in srgb, var(--color-surface) 92%, transparent) 0%, color-mix(in srgb, var(--color-surface-muted) 34%, transparent) 100%);
            box-shadow: 0 12px 26px -20px color-mix(in srgb, var(--color-ring) 48%, transparent);
            overflow: hidden;
        }

        .mobile-student-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .78rem .78rem .62rem;
            border-bottom: 1px solid color-mix(in srgb, var(--glass-border) 52%, transparent);
            background: color-mix(in srgb, var(--color-surface-muted) 38%, transparent);
        }

        .mobile-student-identity {
            display: flex;
            align-items: center;
            gap: .65rem;
            min-width: 0;
        }

        .mobile-student-name {
            font-weight: 700;
            line-height: 1.2;
            word-break: break-word;
            font-size: 1rem;
        }

        .mobile-student-number {
            font-size: .87rem;
            color: var(--color-text-soft);
            font-weight: 600;
            margin-top: .1rem;
        }

        .mobile-status-wrap {
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 50%;
            border: 1px solid color-mix(in srgb, var(--glass-border) 70%, transparent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--color-surface) 82%, transparent);
            flex: 0 0 auto;
        }

        .mobile-meta-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .55rem;
            padding: .72rem .78rem .65rem;
        }

        .mobile-meta-item {
            font-size: .86rem;
            color: var(--color-text-soft);
            line-height: 1.35;
            border: 1px solid color-mix(in srgb, var(--glass-border) 48%, transparent);
            border-radius: 12px;
            padding: .42rem .58rem;
            background: color-mix(in srgb, var(--color-surface) 88%, transparent);
        }

        .mobile-meta-label {
            font-weight: 700;
            color: var(--color-text);
            margin-right: .3rem;
        }

        .mobile-student-actions {
            margin-top: 0;
            padding: 0 .78rem .78rem;
        }

        .mobile-subject-wrap {
            min-width: 0;
            max-width: 100%;
        }

        .hidden-by-filter {
            display: none !important;
        }

        @media (max-width: 767.98px) {
            .students-filter-grid {
                grid-template-columns: 1fr;
            }

            .desktop-students-table {
                display: none;
            }

            .mobile-students-list {
                display: block;
            }

            .student-photo {
                width: 42px;
                height: 42px;
            }

            .students-search-form {
                max-width: 100%;
            }

            .student-view-btn {
                width: 100%;
                height: 2.2rem;
            }
        }
    </style>

    <section class="dashboard-grid">
        <article class="glass-card recent-card students-summary-card" style="grid-column: 1 / -1;">
            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
            @endif

            @if ($inactiveTeacherAlerts->isNotEmpty())
                <div class="alert alert-warning mb-3">
                    <strong>Inactive teacher alert:</strong>
                    <ul class="inactive-alert-list mt-2">
                        @foreach ($inactiveTeacherAlerts as $inactiveTeacher)
                            <li>{{ $inactiveTeacher->name }} has {{ $inactiveTeacher->students_count }} assigned student(s). Transfer from this page.</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $teacherFilterOptions = $teachers->pluck('name', 'id')->all();
                $branchFilterOptions = [];
                $subjectYearFilterOptions = [];

                foreach ($teachers as $filterTeacher) {
                    foreach ($filterTeacher->students as $filterStudent) {
                        $branchName = trim((string) ($filterStudent->branch->name ?? ''));
                        if ($branchName !== '') {
                            $branchFilterOptions[strtolower($branchName)] = $branchName;
                        }

                        foreach ((array) ($filterStudent->subject_years ?? []) as $entry) {
                            $subjectId = (int) ($entry['subject_id'] ?? 0);
                            $yearLabel = trim((string) ($entry['year_label'] ?? ''));

                            if ($subjectId <= 0 || $yearLabel === '') {
                                continue;
                            }

                            $subjectName = trim((string) ($subjectMap[$subjectId] ?? ('Subject #' . $subjectId)));
                            $subjectKey = strtolower($subjectName . '|' . $yearLabel);

                            $subjectYearFilterOptions[$subjectKey] = [
                                'subject_name' => $subjectName,
                                'year_label' => $yearLabel,
                            ];
                        }
                    }
                }

                asort($teacherFilterOptions);
                asort($branchFilterOptions);
                uasort($subjectYearFilterOptions, function ($a, $b) {
                    return strcmp(
                        strtolower($a['subject_name'] . ' ' . $a['year_label']),
                        strtolower($b['subject_name'] . ' ' . $b['year_label'])
                    );
                });
            @endphp

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h3 class="section-title mb-1">Students by Teacher</h3>
                    <p class="mb-0" style="color: var(--color-text-soft);">Track teacher-wise students, status, and profiles.</p>
                </div>

                <div class="d-flex gap-2 align-items-center" style="flex-wrap: wrap;">
                    <a href="{{ route('admin.students.import') }}" class="btn btn-outline-success" title="Bulk import students from CSV">
                        <i class="fa-solid fa-file-import me-2"></i>Import
                    </a>
                    <a href="{{ route('admin.students.transfer.form') }}" class="btn btn-outline-primary" title="Transfer students between teachers">
                        <i class="fa-solid fa-right-left me-2"></i>Transfer
                    </a>
                </div>
            </div>

            <div class="students-filter-grid mb-2">
                <input
                    type="text"
                    id="adminStudentSearch"
                    class="form-control students-filter-control"
                    value="{{ $search }}"
                    placeholder="Search name, phone, registration..."
                    autocomplete="off"
                >

                <select id="adminTeacherFilter" class="form-select students-filter-control">
                    <option value="">All Teachers</option>
                    @foreach ($teacherFilterOptions as $teacherId => $teacherName)
                        <option value="{{ $teacherId }}">{{ $teacherName }}</option>
                    @endforeach
                </select>

                <select id="adminBranchFilter" class="form-select students-filter-control">
                    <option value="">All Branches</option>
                    @foreach ($branchFilterOptions as $branchKey => $branchName)
                        <option value="{{ $branchKey }}">{{ $branchName }}</option>
                    @endforeach
                </select>

                <select id="adminSubjectYearFilter" class="form-select students-filter-control">
                    <option value="">All Subject (Year)</option>
                    @foreach ($subjectYearFilterOptions as $subjectKey => $subjectItem)
                        <option value="{{ $subjectKey }}">{{ $subjectItem['subject_name'] }} - {{ $subjectItem['year_label'] }}</option>
                    @endforeach
                </select>

                <button type="button" id="adminClearFilters" class="btn btn-outline-light students-clear-btn">
                    <i class="fa-solid fa-rotate-left me-1"></i>Clear
                </button>
            </div>

            <div class="students-summary-row">
                <span class="summary-pill">Visible Students <span class="summary-pill-value" id="adminVisibleStudentsCount">{{ $totalStudents }}</span></span>
                <span class="summary-pill">Active <span class="summary-pill-value" id="adminVisibleActiveCount">{{ $totalActive }}</span></span>
                <span class="summary-pill">Inactive <span class="summary-pill-value" id="adminVisibleInactiveCount">{{ $totalInactive }}</span></span>
                <span class="summary-pill">Visible Teachers <span class="summary-pill-value" id="adminVisibleTeachersCount">{{ $teachers->count() }}</span></span>
            </div>

            <div id="adminNoFilterResult" class="alert alert-info mb-3 hidden-by-filter">No students match the current filters.</div>


        </article>

        @forelse($teachers as $teacher)
            @php
                $teacherStudents = $teacher->students;
                $teacherTotal = $teacherStudents->count();
                $teacherActive = $teacherStudents->where('status', 'active')->count();
                $teacherInactive = $teacherStudents->where('status', 'inactive')->count();
            @endphp

            <article class="glass-card recent-card teacher-students-card" style="grid-column: 1 / -1;" data-teacher-card data-teacher-id="{{ $teacher->id }}" data-teacher-name="{{ strtolower(trim((string) $teacher->name)) }}">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <h3 class="section-title mb-0">{{ $teacher->name }}</h3>
                        <span class="teacher-contact fw-semibold">{{ $teacher->phone ? '+91 ' . $teacher->phone : 'No phone' }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="pill">Total: <span data-teacher-visible-total>{{ $teacherTotal }}</span></span>
                        <span class="pill">Active: <span data-teacher-visible-active>{{ $teacherActive }}</span></span>
                        <span class="pill">Inactive: <span data-teacher-visible-inactive>{{ $teacherInactive }}</span></span>
                    </div>
                </div>

                <div class="table-responsive mt-2 desktop-students-table">
                    <table class="table modern-table align-middle students-table">
                        <colgroup>
                            <col style="width: 70px;">
                            <col style="width: 180px;">
                            <col style="width: 150px;">
                            <col style="width: 140px;">
                            <col style="width: 260px;">
                            <col style="width: 90px;">
                            <col style="width: 90px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th width="70">Photo</th>
                                <th>Name</th>
                                <th>Number</th>
                                <th>Branch</th>
                                <th>Subject&Year</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teacherStudents as $student)
                                @php
                                    $photo = $student->photo_path ? asset('storage/' . $student->photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=1f3b6e&color=fff';
                                    $subjectYears = collect((array) ($student->subject_years ?? []))
                                        ->map(function ($entry) use ($subjectMap) {
                                            $subjectName = $subjectMap[$entry['subject_id'] ?? null] ?? ('Subject #' . ($entry['subject_id'] ?? 'N/A'));
                                            $yearLabel = trim((string) ($entry['year_label'] ?? ''));

                                            return $yearLabel !== ''
                                                ? $subjectName . ' ' . $yearLabel
                                                : $subjectName;
                                        })
                                        ->values();
                                            $subjectYearText = $subjectYears->implode(', ');
                                @endphp
                                @php
                                    $subjectYearKeys = collect((array) ($student->subject_years ?? []))
                                        ->map(function ($entry) use ($subjectMap) {
                                            $subjectId = (int) ($entry['subject_id'] ?? 0);
                                            $yearLabel = trim((string) ($entry['year_label'] ?? ''));

                                            if ($subjectId <= 0 || $yearLabel === '') {
                                                return null;
                                            }

                                            $subjectName = trim((string) ($subjectMap[$subjectId] ?? ('Subject #' . $subjectId)));
                                            return strtolower($subjectName . '|' . $yearLabel);
                                        })
                                        ->filter()
                                        ->values()
                                        ->all();

                                    $studentSearchBlob = strtolower(trim(implode(' ', [
                                        (string) ($student->name ?? ''),
                                        (string) ($student->phone ?? ''),
                                        (string) ($student->registration_number ?? ''),
                                        (string) ($student->father_name ?? ''),
                                        (string) ($student->institution ?? ''),
                                    ])));

                                    $studentBranchKey = strtolower(trim((string) ($student->branch->name ?? '')));
                                @endphp
                                <tr
                                    class="admin-student-row"
                                    data-student-id="{{ $student->id }}"
                                    data-teacher-id="{{ $teacher->id }}"
                                    data-teacher-name="{{ strtolower(trim((string) $teacher->name)) }}"
                                    data-search="{{ $studentSearchBlob }}"
                                    data-branch="{{ $studentBranchKey }}"
                                    data-subject-keys="{{ implode(',', $subjectYearKeys) }}"
                                    data-status="{{ strtolower((string) ($student->status ?? 'inactive')) }}"
                                >
                                    <td data-label="Photo">
                                        <img src="{{ $photo }}" alt="{{ $student->name }}" class="student-photo">
                                    </td>
                                    <td data-label="Name">
                                        <div class="student-name">{{ $student->name }}</div>
                                    </td>
                                    <td data-label="Number">
                                        <div class="student-number">{{ $student->phone ?? 'No phone' }}</div>
                                    </td>
                                    <td data-label="Branch">{{ $student->branch->name ?? 'N/A' }}</td>
                                    <td data-label="Subject&Year">
                                        @if($subjectYears->isNotEmpty())
                                            <div class="subject-year-wrap">
                                                @foreach($subjectYears as $subjectYearItem)
                                                    <span class="subject-year-chip">{{ $subjectYearItem }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="subject-year-text">N/A</span>
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-cell">
                                            @if($student->status === 'active')
                                                <span class="status-indicator active" title="Active" aria-label="Active"></span>
                                            @else
                                                <span class="status-indicator inactive" title="Inactive" aria-label="Inactive"></span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-end" data-label="Action">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary student-view-btn">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center" style="color: var(--color-text-soft);">No students under this teacher.</td>
                                </tr>
                            @endforelse
                            @if($teacherStudents->isNotEmpty())
                                <tr class="teacher-empty-filter-row hidden-by-filter">
                                    <td colspan="7" class="text-center" style="color: var(--color-text-soft);">No students for the current filters.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mobile-students-list mt-2">
                    @forelse($teacherStudents as $student)
                        @php
                            $photo = $student->photo_path ? asset('storage/' . $student->photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=1f3b6e&color=fff';
                            $subjectYears = collect((array) ($student->subject_years ?? []))
                                ->map(function ($entry) use ($subjectMap) {
                                    $subjectName = $subjectMap[$entry['subject_id'] ?? null] ?? ('Subject #' . ($entry['subject_id'] ?? 'N/A'));
                                    $yearLabel = trim((string) ($entry['year_label'] ?? ''));

                                    return $yearLabel !== ''
                                        ? $subjectName . ' ' . $yearLabel
                                        : $subjectName;
                                })
                                ->values();
                                    $subjectYearText = $subjectYears->implode(', ');
                        @endphp

                        @php
                            $subjectYearKeys = collect((array) ($student->subject_years ?? []))
                                ->map(function ($entry) use ($subjectMap) {
                                    $subjectId = (int) ($entry['subject_id'] ?? 0);
                                    $yearLabel = trim((string) ($entry['year_label'] ?? ''));

                                    if ($subjectId <= 0 || $yearLabel === '') {
                                        return null;
                                    }

                                    $subjectName = trim((string) ($subjectMap[$subjectId] ?? ('Subject #' . $subjectId)));
                                    return strtolower($subjectName . '|' . $yearLabel);
                                })
                                ->filter()
                                ->values()
                                ->all();

                            $studentSearchBlob = strtolower(trim(implode(' ', [
                                (string) ($student->name ?? ''),
                                (string) ($student->phone ?? ''),
                                (string) ($student->registration_number ?? ''),
                                (string) ($student->father_name ?? ''),
                                (string) ($student->institution ?? ''),
                            ])));

                            $studentBranchKey = strtolower(trim((string) ($student->branch->name ?? '')));
                        @endphp

                        <div
                            class="mobile-student-card admin-student-card"
                            data-student-id="{{ $student->id }}"
                            data-teacher-id="{{ $teacher->id }}"
                            data-teacher-name="{{ strtolower(trim((string) $teacher->name)) }}"
                            data-search="{{ $studentSearchBlob }}"
                            data-branch="{{ $studentBranchKey }}"
                            data-subject-keys="{{ implode(',', $subjectYearKeys) }}"
                            data-status="{{ strtolower((string) ($student->status ?? 'inactive')) }}"
                        >
                            <div class="mobile-student-top">
                                <div class="mobile-student-identity">
                                    <img src="{{ $photo }}" alt="{{ $student->name }}" class="student-photo">
                                    <div>
                                        <div class="mobile-student-name">{{ $student->name }}</div>
                                        <div class="mobile-student-number">{{ $student->phone ?? 'No phone' }}</div>
                                    </div>
                                </div>

                                <span class="mobile-status-wrap" aria-hidden="true">
                                    @if($student->status === 'active')
                                        <span class="status-indicator active" title="Active" aria-label="Active"></span>
                                    @else
                                        <span class="status-indicator inactive" title="Inactive" aria-label="Inactive"></span>
                                    @endif
                                </span>
                            </div>

                            <div class="mobile-meta-grid">
                                <div class="mobile-meta-item">
                                    <span class="mobile-meta-label">Branch:</span>{{ $student->branch->name ?? 'N/A' }}
                                </div>

                                <div class="mobile-meta-item">
                                    <span class="mobile-meta-label">Subject&Year:</span>
                                    @if($subjectYears->isNotEmpty())
                                        <span class="subject-year-wrap mobile-subject-wrap">
                                            @foreach($subjectYears as $subjectYearItem)
                                                <span class="subject-year-chip">{{ $subjectYearItem }}</span>
                                            @endforeach
                                        </span>
                                    @else
                                        <span class="subject-year-text">N/A</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mobile-student-actions">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary student-view-btn">View Details</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center" style="color: var(--color-text-soft);">No students under this teacher.</div>
                    @endforelse
                    @if($teacherStudents->isNotEmpty())
                        <div class="text-center teacher-empty-filter-card hidden-by-filter" style="color: var(--color-text-soft);">No students for the current filters.</div>
                    @endif
                </div>
            </article>
        @empty
            <article class="glass-card recent-card" style="grid-column: 1 / -1;">
                <h3 class="section-title mb-2">No Teachers Found</h3>
                <p class="mb-0" style="color: var(--color-text-soft);">No teacher/student data available for this filter.</p>
            </article>
        @endforelse
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('adminStudentSearch');
            const teacherFilter = document.getElementById('adminTeacherFilter');
            const branchFilter = document.getElementById('adminBranchFilter');
            const subjectYearFilter = document.getElementById('adminSubjectYearFilter');
            const clearFiltersBtn = document.getElementById('adminClearFilters');
            const noResultAlert = document.getElementById('adminNoFilterResult');
            const teacherCards = Array.from(document.querySelectorAll('[data-teacher-card]'));
            const desktopRows = Array.from(document.querySelectorAll('.admin-student-row'));
            const mobileCards = Array.from(document.querySelectorAll('.admin-student-card'));
            const visibleStudentsCountEl = document.getElementById('adminVisibleStudentsCount');
            const visibleActiveCountEl = document.getElementById('adminVisibleActiveCount');
            const visibleInactiveCountEl = document.getElementById('adminVisibleInactiveCount');
            const visibleTeachersCountEl = document.getElementById('adminVisibleTeachersCount');

            const getStudentDataFromElement = function (element) {
                const subjectKeys = (element.dataset.subjectKeys || '')
                    .split(',')
                    .map(function (x) { return x.trim().toLowerCase(); })
                    .filter(Boolean);

                return {
                    teacherId: String(element.dataset.teacherId || ''),
                    teacherName: String(element.dataset.teacherName || '').toLowerCase(),
                    searchBlob: String(element.dataset.search || '').toLowerCase(),
                    branch: String(element.dataset.branch || '').toLowerCase(),
                    subjectKeys: subjectKeys,
                };
            };

            const matchesFilters = function (studentData, currentFilters) {
                const matchesSearch = currentFilters.search === '' || studentData.searchBlob.includes(currentFilters.search);
                const matchesTeacher = currentFilters.teacherId === '' || studentData.teacherId === currentFilters.teacherId;
                const matchesBranch = currentFilters.branch === '' || studentData.branch === currentFilters.branch;
                const matchesSubject = currentFilters.subjectKey === '' || studentData.subjectKeys.includes(currentFilters.subjectKey);
                const matchesTeacherName = currentFilters.teacherName === '' || studentData.teacherName.includes(currentFilters.teacherName);

                return matchesSearch && matchesTeacher && matchesBranch && matchesSubject && matchesTeacherName;
            };

            const updateFilterResults = function () {
                const currentFilters = {
                    search: (searchInput ? searchInput.value : '').trim().toLowerCase(),
                    teacherId: (teacherFilter ? teacherFilter.value : '').trim(),
                    teacherName: '',
                    branch: (branchFilter ? branchFilter.value : '').trim().toLowerCase(),
                    subjectKey: (subjectYearFilter ? subjectYearFilter.value : '').trim().toLowerCase(),
                };

                if (teacherFilter && teacherFilter.selectedIndex > 0) {
                    currentFilters.teacherName = String(teacherFilter.options[teacherFilter.selectedIndex].text || '').trim().toLowerCase();
                }

                const visibilityByStudentId = {};
                desktopRows.forEach(function (row) {
                    const studentData = getStudentDataFromElement(row);
                    const visible = matchesFilters(studentData, currentFilters);
                    const studentId = String(row.dataset.studentId || '');

                    row.classList.toggle('hidden-by-filter', !visible);
                    visibilityByStudentId[studentId] = visible;
                });

                mobileCards.forEach(function (card) {
                    const studentId = String(card.dataset.studentId || '');
                    const visible = Object.prototype.hasOwnProperty.call(visibilityByStudentId, studentId)
                        ? visibilityByStudentId[studentId]
                        : matchesFilters(getStudentDataFromElement(card), currentFilters);

                    card.classList.toggle('hidden-by-filter', !visible);
                });

                let visibleStudents = 0;
                let visibleActive = 0;
                let visibleInactive = 0;
                let visibleTeachers = 0;

                teacherCards.forEach(function (card) {
                    const teacherId = String(card.dataset.teacherId || '');
                    const teacherRows = desktopRows.filter(function (row) {
                        return String(row.dataset.teacherId || '') === teacherId;
                    });

                    const visibleRows = teacherRows.filter(function (row) {
                        return !row.classList.contains('hidden-by-filter');
                    });

                    const teacherVisibleTotalEl = card.querySelector('[data-teacher-visible-total]');
                    const teacherVisibleActiveEl = card.querySelector('[data-teacher-visible-active]');
                    const teacherVisibleInactiveEl = card.querySelector('[data-teacher-visible-inactive]');
                    const emptyTableRow = card.querySelector('.teacher-empty-filter-row');
                    const emptyMobileCard = card.querySelector('.teacher-empty-filter-card');

                    let teacherActive = 0;
                    let teacherInactive = 0;

                    visibleRows.forEach(function (row) {
                        const status = String(row.dataset.status || '').toLowerCase();
                        if (status === 'active') {
                            teacherActive++;
                        } else {
                            teacherInactive++;
                        }
                    });

                    const teacherVisible = visibleRows.length > 0;
                    card.classList.toggle('hidden-by-filter', !teacherVisible);

                    if (emptyTableRow) {
                        emptyTableRow.classList.toggle('hidden-by-filter', teacherVisible);
                    }
                    if (emptyMobileCard) {
                        emptyMobileCard.classList.toggle('hidden-by-filter', teacherVisible);
                    }

                    if (teacherVisibleTotalEl) {
                        teacherVisibleTotalEl.textContent = String(visibleRows.length);
                    }
                    if (teacherVisibleActiveEl) {
                        teacherVisibleActiveEl.textContent = String(teacherActive);
                    }
                    if (teacherVisibleInactiveEl) {
                        teacherVisibleInactiveEl.textContent = String(teacherInactive);
                    }

                    if (teacherVisible) {
                        visibleTeachers++;
                    }

                    visibleStudents += visibleRows.length;
                    visibleActive += teacherActive;
                    visibleInactive += teacherInactive;
                });

                if (visibleStudentsCountEl) {
                    visibleStudentsCountEl.textContent = String(visibleStudents);
                }
                if (visibleActiveCountEl) {
                    visibleActiveCountEl.textContent = String(visibleActive);
                }
                if (visibleInactiveCountEl) {
                    visibleInactiveCountEl.textContent = String(visibleInactive);
                }
                if (visibleTeachersCountEl) {
                    visibleTeachersCountEl.textContent = String(visibleTeachers);
                }

                if (noResultAlert) {
                    noResultAlert.classList.toggle('hidden-by-filter', visibleStudents > 0);
                }
            };

            if (searchInput) {
                searchInput.addEventListener('input', updateFilterResults);
            }
            if (teacherFilter) {
                teacherFilter.addEventListener('change', updateFilterResults);
            }
            if (branchFilter) {
                branchFilter.addEventListener('change', updateFilterResults);
            }
            if (subjectYearFilter) {
                subjectYearFilter.addEventListener('change', updateFilterResults);
            }
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    if (teacherFilter) {
                        teacherFilter.value = '';
                    }
                    if (branchFilter) {
                        branchFilter.value = '';
                    }
                    if (subjectYearFilter) {
                        subjectYearFilter.value = '';
                    }
                    updateFilterResults();
                });
            }

            updateFilterResults();
        });
    </script>
@endsection
