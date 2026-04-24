@extends('layouts.app')

@section('title', 'Transfer Students')

@section('content')
    @php
        $sourceTeacherPayload = $sourceTeachers->map(function ($teacher) {
            return [
                'id' => (int) $teacher->id,
                'name' => (string) $teacher->name,
                'phone' => (string) ($teacher->phone ?? ''),
                'status' => (string) ($teacher->status ?? 'inactive'),
                'students' => $teacher->students->map(function ($student) {
                    return [
                        'id' => (int) $student->id,
                        'name' => (string) $student->name,
                        'phone' => (string) ($student->phone ?? ''),
                    ];
                })->values()->all(),
            ];
        })->values();
    @endphp

    <style>
        .transfer-card {
            border-radius: 18px;
        }

        .transfer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: .75rem;
        }

        .transfer-control {
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--glass-border) 62%, transparent);
            background: color-mix(in srgb, var(--color-surface) 90%, transparent);
            color: var(--color-text);
        }

        .transfer-control:focus {
            border-color: color-mix(in srgb, var(--color-primary) 55%, transparent);
            box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--color-primary) 25%, transparent);
        }

        .transfer-search-wrap {
            position: relative;
        }

        .transfer-search-icon {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-soft);
            font-size: .86rem;
            pointer-events: none;
        }

        .transfer-search {
            padding-left: 2.1rem;
        }

        .transfer-list {
            max-height: 420px;
            overflow-y: auto;
            border: 1px solid color-mix(in srgb, var(--glass-border) 62%, transparent);
            border-radius: 14px;
            background: color-mix(in srgb, var(--color-surface) 90%, transparent);
        }

        .transfer-item {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .62rem .78rem;
            border-bottom: 1px solid color-mix(in srgb, var(--glass-border) 48%, transparent);
        }

        .transfer-item:last-child {
            border-bottom: none;
        }

        .transfer-item .form-check-input {
            margin-top: 0;
            accent-color: var(--color-primary);
        }

        .transfer-item-name {
            font-weight: 600;
            color: var(--color-text);
            line-height: 1.2;
        }

        .transfer-item-sub {
            color: var(--color-text-soft);
            font-size: .82rem;
            line-height: 1.2;
        }

        .transfer-empty {
            color: var(--color-text-soft);
            text-align: center;
            padding: 1.1rem;
        }

        .transfer-meta-pill {
            border: 1px solid color-mix(in srgb, var(--glass-border) 65%, transparent);
            background: color-mix(in srgb, var(--color-surface) 90%, transparent);
            border-radius: 999px;
            padding: .24rem .68rem;
            font-size: .82rem;
            font-weight: 600;
            color: var(--color-text-soft);
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .transfer-meta-pill strong {
            color: var(--color-text);
        }

        @media (max-width: 768px) {
            .transfer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="dashboard-grid">
        <article class="glass-card recent-card transfer-card" style="grid-column: 1 / -1;">
            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h3 class="section-title mb-1">Transfer Students</h3>
                    <p class="mb-0" style="color: var(--color-text-soft);">Select source and target teachers, then transfer students safely.</p>
                </div>

                <a href="{{ route('admin.students') }}" class="btn btn-outline-light">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Students
                </a>
            </div>

            <form id="studentTransferForm" method="POST" action="{{ route('admin.students.transfer') }}">
                @csrf

                <div class="transfer-grid mb-3">
                    <div>
                        <label for="transferFromTeacher" class="form-label mb-1">Select Teacher (From)</label>
                        <select id="transferFromTeacher" class="form-select transfer-control" required>
                            <option value="">Choose source teacher</option>
                            @foreach ($sourceTeachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}{{ $teacher->phone ? ' (' . $teacher->phone . ')' : '' }}
                                    @if(($teacher->status ?? '') !== 'active')
                                        [Inactive]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="transferToTeacher" class="form-label mb-1">Select Teacher (To)</label>
                        <select id="transferToTeacher" name="target_teacher_id" class="form-select transfer-control" required>
                            <option value="">Choose target teacher</option>
                            @foreach ($targetTeachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}{{ $teacher->phone ? ' (' . $teacher->phone . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="transferStudentSearch" class="form-label mb-1">Search Student</label>
                    <div class="transfer-search-wrap">
                        <span class="transfer-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="transferStudentSearch" class="form-control transfer-control transfer-search" placeholder="Search by name or phone" autocomplete="off">
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span class="transfer-meta-pill">Loaded: <strong id="transferLoadedCount">0</strong></span>
                        <span class="transfer-meta-pill">Selected: <strong id="transferSelectedCount">0</strong></span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" id="transferSelectAllBtn" class="btn btn-sm btn-outline-light" disabled>Select All</button>
                        <button type="button" id="transferClearAllBtn" class="btn btn-sm btn-outline-light" disabled>Clear</button>
                    </div>
                </div>

                <div id="transferStudentsList" class="transfer-list mb-3">
                    <div class="transfer-empty">Select a source teacher to load students.</div>
                </div>

                <div>
                    <label for="transferReason" class="form-label mb-1">Reason (optional)</label>
                    <input type="text" id="transferReason" name="reason" class="form-control transfer-control" maxlength="255" placeholder="Reason for transfer">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.students') }}" class="btn btn-outline-light">Cancel</a>
                    <button type="submit" id="transferSubmitBtn" class="btn btn-primary" disabled>
                        <i class="fa-solid fa-right-left me-2"></i>Transfer Students
                    </button>
                </div>
            </form>
        </article>
    </section>

    <script type="application/json" id="sourceTeacherPayload">{!! $sourceTeacherPayload->toJson() !!}</script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fromTeacher = document.getElementById('transferFromTeacher');
            const toTeacher = document.getElementById('transferToTeacher');
            const studentSearch = document.getElementById('transferStudentSearch');
            const studentsList = document.getElementById('transferStudentsList');
            const loadedCountEl = document.getElementById('transferLoadedCount');
            const selectedCountEl = document.getElementById('transferSelectedCount');
            const selectAllBtn = document.getElementById('transferSelectAllBtn');
            const clearAllBtn = document.getElementById('transferClearAllBtn');
            const submitBtn = document.getElementById('transferSubmitBtn');
            const form = document.getElementById('studentTransferForm');

            const payloadEl = document.getElementById('sourceTeacherPayload');
            const sourceTeachers = payloadEl ? JSON.parse(payloadEl.textContent || '[]') : [];
            let loadedStudents = [];
            let filteredStudents = [];

            const refreshCounts = function () {
                const selected = Array.from(document.querySelectorAll('.transfer-student-check:checked')).length;
                selectedCountEl.textContent = String(selected);
                loadedCountEl.textContent = String(filteredStudents.length);
                submitBtn.disabled = selected === 0;
                selectAllBtn.disabled = filteredStudents.length === 0;
                clearAllBtn.disabled = selected === 0;
            };

            const renderStudents = function () {
                const query = (studentSearch.value || '').trim().toLowerCase();
                filteredStudents = loadedStudents.filter(function (student) {
                    if (query === '') {
                        return true;
                    }
                    return (student.name + ' ' + (student.phone || '')).toLowerCase().includes(query);
                });

                if (filteredStudents.length === 0) {
                    studentsList.innerHTML = '<div class="transfer-empty">No students found.</div>';
                    refreshCounts();
                    return;
                }

                studentsList.innerHTML = filteredStudents.map(function (student) {
                    const phone = student.phone ? student.phone : 'No phone';
                    return '<label class="transfer-item">'
                        + '<input type="checkbox" class="form-check-input transfer-student-check" name="student_ids[]" value="' + student.id + '">'
                        + '<span>'
                        + '<span class="transfer-item-name">' + student.name + '</span><br>'
                        + '<span class="transfer-item-sub">' + phone + '</span>'
                        + '</span>'
                        + '</label>';
                }).join('');

                Array.from(document.querySelectorAll('.transfer-student-check')).forEach(function (checkbox) {
                    checkbox.addEventListener('change', refreshCounts);
                });

                refreshCounts();
            };

            const loadStudents = function () {
                const fromId = Number(fromTeacher.value || 0);
                const source = sourceTeachers.find(function (teacher) {
                    return Number(teacher.id) === fromId;
                });

                loadedStudents = source ? (source.students || []) : [];
                studentSearch.value = '';

                Array.from(toTeacher.options).forEach(function (option) {
                    if (!option.value) {
                        return;
                    }
                    option.disabled = Number(option.value) === fromId;
                });

                if (Number(toTeacher.value || 0) === fromId) {
                    toTeacher.value = '';
                }

                if (loadedStudents.length === 0) {
                    studentsList.innerHTML = '<div class="transfer-empty">No students under selected teacher.</div>';
                    filteredStudents = [];
                    refreshCounts();
                    return;
                }

                renderStudents();
            };

            fromTeacher.addEventListener('change', loadStudents);
            studentSearch.addEventListener('input', renderStudents);

            selectAllBtn.addEventListener('click', function () {
                Array.from(document.querySelectorAll('.transfer-student-check')).forEach(function (checkbox) {
                    checkbox.checked = true;
                });
                refreshCounts();
            });

            clearAllBtn.addEventListener('click', function () {
                Array.from(document.querySelectorAll('.transfer-student-check')).forEach(function (checkbox) {
                    checkbox.checked = false;
                });
                refreshCounts();
            });

            form.addEventListener('submit', function (event) {
                const fromId = Number(fromTeacher.value || 0);
                const toId = Number(toTeacher.value || 0);
                const selectedCount = Array.from(document.querySelectorAll('.transfer-student-check:checked')).length;

                if (!fromId) {
                    event.preventDefault();
                    alert('Please select source teacher.');
                    return;
                }

                if (!toId) {
                    event.preventDefault();
                    alert('Please select target teacher.');
                    return;
                }

                if (fromId === toId) {
                    event.preventDefault();
                    alert('From and To teacher cannot be same.');
                    return;
                }

                if (selectedCount === 0) {
                    event.preventDefault();
                    alert('Please select at least one student.');
                    return;
                }

                if (!confirm('Transfer selected student(s) to the target teacher?')) {
                    event.preventDefault();
                }
            });

            refreshCounts();
        });
    </script>
@endsection
