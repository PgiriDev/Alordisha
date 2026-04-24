@extends('layouts.app')

@section('content')

<style>
    /* --- PAGE-SPECIFIC STYLES (Matching Dashboard Theme) --- */
    
    /* GLASS CARD CONTAINER */
    .ethereal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    /* HEADER AREA */
    .page-header {
        font-family: 'Outfit', sans-serif;
        font-weight: 300;
        font-size: 2rem;
        color: var(--color-text);
        margin-bottom: 0;
    }

    /* GLASS BUTTONS */
    .btn-glass {
        background: color-mix(in srgb, var(--color-surface) 88%, transparent);
        border: 1px solid var(--color-border);
        color: var(--color-text);
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-glass:hover {
        background: var(--color-surface);
        color: var(--color-text);
        box-shadow: 0 0 15px color-mix(in srgb, var(--color-ring) 32%, transparent);
        transform: translateY(-2px);
    }

    .btn-glass-primary {
        background: color-mix(in srgb, var(--color-primary) 18%, var(--color-surface));
        border-color: color-mix(in srgb, var(--color-primary) 44%, var(--color-border));
        color: var(--color-text);
    }
    
    .btn-glass-primary:hover {
        background: color-mix(in srgb, var(--color-primary) 72%, var(--color-surface));
        color: #ffffff;
        box-shadow: 0 0 20px color-mix(in srgb, var(--color-primary) 44%, transparent);
    }

    /* MODERN TABLE STYLES */
    .table-container {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        color: var(--color-text);
    }

    .modern-table thead th {
        text-align: left;
        padding: 15px 20px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--color-text-soft);
        border-bottom: 1px solid color-mix(in srgb, var(--color-border) 70%, transparent);
        font-weight: 600;
    }

    .modern-table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        border-bottom: 1px solid color-mix(in srgb, var(--color-border) 56%, transparent);
        font-size: 0.95rem;
    }

    .modern-table tbody tr {
        transition: background 0.3s;
    }

    .modern-table tbody tr:hover {
        background: color-mix(in srgb, var(--color-surface-muted) 55%, transparent);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* IMAGES */
    .student-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid color-mix(in srgb, var(--color-border) 66%, transparent);
    }

    /* GLASS BADGES */
    .glass-badge {
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .badge-success {
        background: color-mix(in srgb, var(--color-success) 16%, var(--color-surface));
        color: color-mix(in srgb, var(--color-success) 85%, var(--color-text));
        border: 1px solid color-mix(in srgb, var(--color-success) 40%, var(--color-border));
    }

    .badge-secondary {
        background: color-mix(in srgb, var(--color-text-soft) 16%, var(--color-surface));
        color: var(--color-text-soft);
        border: 1px solid color-mix(in srgb, var(--color-text-soft) 36%, var(--color-border));
    }

    .badge-primary {
        background: color-mix(in srgb, var(--color-primary) 16%, var(--color-surface));
        color: color-mix(in srgb, var(--color-primary) 86%, var(--color-text));
        border: 1px solid color-mix(in srgb, var(--color-primary) 36%, var(--color-border));
    }

    .student-name-cell {
        font-weight: 600;
        color: var(--color-text);
    }

    .student-branch-cell {
        color: var(--color-text-soft);
    }

    .subject-year-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .subject-year-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        background: color-mix(in srgb, var(--color-primary) 14%, var(--color-surface));
        color: color-mix(in srgb, var(--color-primary) 82%, var(--color-text));
        border: 1px solid color-mix(in srgb, var(--color-primary) 30%, var(--color-border));
        white-space: nowrap;
    }

    .filters-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .filter-control {
        min-width: 190px;
        flex: 1 1 220px;
        background: color-mix(in srgb, var(--color-surface) 90%, transparent);
        border: 1px solid var(--color-border);
        color: var(--color-text);
        border-radius: 12px;
        padding: 10px 12px;
        outline: none;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .filter-control:focus {
        border-color: color-mix(in srgb, var(--color-primary) 45%, var(--color-border));
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 24%, transparent);
    }

    .filter-clear-btn {
        flex: 0 0 auto;
        border-radius: 12px;
        padding: 10px 14px;
    }

    .status-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .status-count-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid var(--color-border);
        background: color-mix(in srgb, var(--color-surface) 90%, transparent);
        color: var(--color-text);
    }

    .status-count-chip .count-value {
        min-width: 20px;
        text-align: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .status-count-chip.active .count-value {
        background: color-mix(in srgb, var(--color-success) 24%, var(--color-surface));
        color: color-mix(in srgb, var(--color-success) 90%, var(--color-text));
        border: 1px solid color-mix(in srgb, var(--color-success) 40%, var(--color-border));
    }

    .status-count-chip.inactive .count-value {
        background: color-mix(in srgb, #f87171 20%, var(--color-surface));
        color: color-mix(in srgb, #f87171 88%, var(--color-text));
        border: 1px solid color-mix(in srgb, #f87171 40%, var(--color-border));
    }

    .status-count-chip.total .count-value {
        background: color-mix(in srgb, var(--color-primary) 20%, var(--color-surface));
        color: color-mix(in srgb, var(--color-primary) 88%, var(--color-text));
        border: 1px solid color-mix(in srgb, var(--color-primary) 40%, var(--color-border));
    }

    /* ACTION ICON BUTTONS */
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: none;
        background: transparent;
    }

    .btn-edit { color: #60a5fa; background: rgba(96, 165, 250, 0.1); }
    .btn-edit:hover { background: #60a5fa; color: white; }

    .btn-delete { color: #f87171; background: rgba(248, 113, 113, 0.1); }
    .btn-delete:hover { background: #f87171; color: white; }

    /* ANIMATION */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 fade-up">
    <h3 class="page-header">Students List</h3>
    
    <a href="{{ route('students.create') }}" class="btn-glass btn-glass-primary">
        <i class="fa-solid fa-plus"></i> Add New Student
    </a>
</div>

<div class="ethereal-card">
    @php
        $branchOptions = [];
        $subjectYearOptions = [];

        foreach ($students as $studentForFilter) {
            $branchId = (int)($studentForFilter->branch_id ?? 0);
            $branchName = (string)($studentForFilter->branch?->name ?? '');

            if ($branchId > 0 && $branchName !== '') {
                $branchOptions[$branchId] = $branchName;
            }

            foreach ((array)($studentForFilter->subject_years ?? []) as $subjectYearItem) {
                $subjectId = (int)($subjectYearItem['subject_id'] ?? 0);
                $yearLabel = trim((string)($subjectYearItem['year_label'] ?? ''));

                if ($subjectId <= 0 || $yearLabel === '') {
                    continue;
                }

                $subjectName = (string)($subjectNameMap[$subjectId] ?? 'Unknown Subject');
                $key = $subjectId . '|' . strtolower($yearLabel);

                $subjectYearOptions[$key] = [
                    'subject_id' => $subjectId,
                    'year_label' => $yearLabel,
                    'subject_name' => $subjectName,
                ];
            }
        }

        asort($branchOptions);
        uasort($subjectYearOptions, function ($a, $b) {
            return strcmp(
                strtolower($a['subject_name'] . ' ' . $a['year_label']),
                strtolower($b['subject_name'] . ' ' . $b['year_label'])
            );
        });
    @endphp

    <div class="filters-bar">
        <input
            type="text"
            id="studentSearch"
            class="filter-control"
            placeholder="Search by name or phone number"
            autocomplete="off"
        >

        <select id="branchFilter" class="filter-control">
            <option value="">All Branches</option>
            @foreach($branchOptions as $branchId => $branchName)
                <option value="{{ $branchId }}">{{ $branchName }}</option>
            @endforeach
        </select>

        <select id="subjectYearFilter" class="filter-control">
            <option value="">All Subject (Year)</option>
            @foreach($subjectYearOptions as $subjectYearKey => $subjectYear)
                <option value="{{ $subjectYearKey }}">
                    {{ $subjectYear['subject_name'] }} - {{ $subjectYear['year_label'] }}
                </option>
            @endforeach
        </select>

        <button type="button" id="clearStudentFilters" class="btn-glass filter-clear-btn">
            <i class="fa-solid fa-rotate-left"></i> Clear
        </button>
    </div>

    <div class="status-summary" id="statusSummaryBar">
        <span class="status-count-chip total">
            Visible Students
            <span class="count-value" id="visibleCountValue">0</span>
        </span>
        <span class="status-count-chip active">
            Active
            <span class="count-value" id="activeCountValue">0</span>
        </span>
        <span class="status-count-chip inactive">
            Inactive
            <span class="count-value" id="inactiveCountValue">0</span>
        </span>
    </div>

    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th width="80">Photo</th>
                    <th>Full Name</th>
                    <th>Phone Number</th>
                    <th>Branch</th>
                    <th>Subject (Year)</th>
                    <th class="text-center">Attendance</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($students as $s)
                @php
                    $rowSubjectKeys = [];

                    foreach ((array)($s->subject_years ?? []) as $item) {
                        $rowSubjectId = (int)($item['subject_id'] ?? 0);
                        $rowYearLabel = trim((string)($item['year_label'] ?? ''));

                        if ($rowSubjectId > 0 && $rowYearLabel !== '') {
                            $rowSubjectKeys[] = $rowSubjectId . '|' . strtolower($rowYearLabel);
                        }
                    }
                @endphp
                <tr
                    data-student-row
                    data-name="{{ strtolower((string)($s->name ?? '')) }}"
                    data-phone="{{ preg_replace('/\s+/', '', strtolower((string)($s->phone ?? ''))) }}"
                    data-branch-id="{{ (int)($s->branch_id ?? 0) }}"
                    data-subject-year-keys="{{ implode(',', $rowSubjectKeys) }}"
                    data-status="{{ strtolower((string)($s->status ?? 'inactive')) }}"
                >
                    <td>
                        @if($s->photo_path)
                            <img src="{{ asset('storage/'.$s->photo_path) }}" class="student-avatar" alt="Student Photo">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ $s->name }}&background=6366f1&color=fff" class="student-avatar" alt="Avatar">
                        @endif
                    </td>

                    <td class="student-name-cell">
                        {{ $s->name }}
                    </td>

                    <td class="student-branch-cell">
                        {{ $s->phone ?: 'N/A' }}
                    </td>

                    <td class="student-branch-cell">
                        {{ $s->branch?->name ?? 'N/A' }}
                    </td>

                    <td>
                        @php
                            $subjectYears = (array)($s->subject_years ?? []);
                        @endphp

                        @if(count($subjectYears) > 0)
                            <div class="subject-year-wrap">
                                @foreach($subjectYears as $item)
                                    @php
                                        $subjectId = (int)($item['subject_id'] ?? 0);
                                        $subjectName = $subjectNameMap[$subjectId] ?? 'Unknown Subject';
                                        $yearLabel = trim((string)($item['year_label'] ?? ''));
                                    @endphp
                                    <span class="subject-year-pill">
                                        {{ $subjectName }}{{ $yearLabel !== '' ? ' - ' . $yearLabel : '' }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="student-branch-cell">N/A</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @php
                            $att = $s->attendance_percentage;
                            $attColor = $att >= 75 ? 'badge-success' : ($att >= 50 ? 'badge-primary' : 'badge-secondary');
                        @endphp
                        <span class="glass-badge {{ $attColor }}">
                            {{ $att }}%
                        </span>
                    </td>

                    <td class="text-center">
                        @if($s->status === 'active')
                            <span class="glass-badge badge-success">Active</span>
                        @else
                            <span class="glass-badge badge-secondary">Inactive</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="{{ route('students.edit', $s->id) }}" class="action-btn btn-edit me-1" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <form action="{{ route('students.destroy', $s->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this student permanently?')" class="action-btn btn-delete" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

                <tr id="noStudentsFoundRow" style="display: none;">
                    <td colspan="8" class="text-center student-branch-cell" style="padding: 28px 20px;">
                        No students found for this search/filter.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- DUPLICATE STUDENTS SECTION -->
@if(!empty($duplicates) && count($duplicates) > 0)
    <div class="container mt-5">
        <div class="ethereal-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <h3 class="page-header">
                    <i class="fa-solid fa-copy me-2"></i>Duplicate Students
                </h3>
                <span class="glass-badge badge-secondary">{{ count($duplicates) }} found</span>
            </div>

            <div class="text-muted small mb-4">
                These students have the same name and phone number. Select which one to keep and click "Merge".
            </div>

            @foreach($duplicates as $dupGroup)
                <div class="card mb-3" style="background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; padding: 20px;">
                    <div class="mb-3">
                        <div class="fw-semibold text-white">{{ $dupGroup['name'] }} ({{ $dupGroup['phone'] }})</div>
                        <div class="text-muted small">{{ $dupGroup['count'] }} duplicate records found</div>
                    </div>

                    <form action="{{ route('students.merge-duplicates') }}" method="POST" class="mb-2">
                        @csrf

                        <div class="mb-3">
                            <div class="small text-muted mb-2">Select student to keep (others will be merged):</div>
                            @foreach($dupGroup['students'] as $student)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="keep_id" value="{{ $student->id }}" id="keep_{{ $student->id }}" required>
                                    <label class="form-check-label" for="keep_{{ $student->id }}" style="cursor: pointer;">
                                        <span class="text-white">{{ $student->name }}</span>
                                        <span class="text-muted small ms-2">
                                            • Branch: {{ $student->branch?->name ?? 'N/A' }}
                                            • Subjects: {{ count((array)($student->subject_years ?? [])) }}
                                            • Created: {{ $student->created_at->format('d M Y') }}
                                        </span>
                                    </label>
                                </div>

                                <input type="hidden" name="merge_ids[]" value="{{ $student->id }}">
                            @endforeach
                        </div>

                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                            <i class="fa-solid fa-shuffle me-2"></i>Merge Selected
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script>
    (function () {
        const searchInput = document.getElementById('studentSearch');
        const branchFilter = document.getElementById('branchFilter');
        const subjectYearFilter = document.getElementById('subjectYearFilter');
        const clearButton = document.getElementById('clearStudentFilters');
        const noRowsMessage = document.getElementById('noStudentsFoundRow');
        const visibleCountValue = document.getElementById('visibleCountValue');
        const activeCountValue = document.getElementById('activeCountValue');
        const inactiveCountValue = document.getElementById('inactiveCountValue');
        const rows = Array.from(document.querySelectorAll('[data-student-row]'));

        if (!searchInput || !branchFilter || !subjectYearFilter || !clearButton || rows.length === 0) {
            return;
        }

        function applyStudentFilters() {
            const searchValue = (searchInput.value || '').trim().toLowerCase();
            const branchValue = branchFilter.value || '';
            const subjectYearValue = (subjectYearFilter.value || '').toLowerCase();

            let visibleCount = 0;
            let activeCount = 0;
            let inactiveCount = 0;

            rows.forEach((row) => {
                const studentName = row.dataset.name || '';
                const studentPhone = row.dataset.phone || '';
                const branchId = row.dataset.branchId || '';
                const subjectYearKeys = (row.dataset.subjectYearKeys || '').split(',').filter(Boolean);

                const matchedSearch =
                    searchValue === '' ||
                    studentName.includes(searchValue) ||
                    studentPhone.includes(searchValue.replace(/\s+/g, ''));

                const matchedBranch = branchValue === '' || branchId === branchValue;
                const matchedSubjectYear = subjectYearValue === '' || subjectYearKeys.includes(subjectYearValue);

                const shouldShow = matchedSearch && matchedBranch && matchedSubjectYear;
                row.style.display = shouldShow ? '' : 'none';

                if (shouldShow) {
                    visibleCount++;

                    const statusValue = (row.dataset.status || '').toLowerCase();
                    if (statusValue === 'active') {
                        activeCount++;
                    } else {
                        inactiveCount++;
                    }
                }
            });

            if (noRowsMessage) {
                noRowsMessage.style.display = visibleCount === 0 ? '' : 'none';
            }

            if (visibleCountValue) {
                visibleCountValue.textContent = String(visibleCount);
            }
            if (activeCountValue) {
                activeCountValue.textContent = String(activeCount);
            }
            if (inactiveCountValue) {
                inactiveCountValue.textContent = String(inactiveCount);
            }
        }

        searchInput.addEventListener('input', applyStudentFilters);
        branchFilter.addEventListener('change', applyStudentFilters);
        subjectYearFilter.addEventListener('change', applyStudentFilters);

        clearButton.addEventListener('click', function () {
            searchInput.value = '';
            branchFilter.value = '';
            subjectYearFilter.value = '';
            applyStudentFilters();
        });

        applyStudentFilters();
    })();
</script>

@endsection