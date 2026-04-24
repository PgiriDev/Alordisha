@extends('layouts.app')

@section('content')

<style>
    .ethereal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .page-header {
        font-family: 'DM Serif Display', serif;
        font-weight: 400;
        font-size: 2rem;
        color: white;
        margin-bottom: 0.5rem;
    }

    .duplicate-item {
        background: rgba(99, 102, 241, 0.08);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .student-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 15px;
    }

    .info-box {
        background: rgba(0,0,0,0.3);
        padding: 12px;
        border-radius: 8px;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .info-value {
        color: #e2e8f0;
        font-size: 0.95rem;
    }

    .subject-badge {
        display: inline-block;
        background: rgba(99, 102, 241, 0.3);
        border: 1px solid rgba(99, 102, 241, 0.5);
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.85rem;
        margin-right: 8px;
        margin-bottom: 5px;
        color: #c7d2fe;
    }

    .subjects-merged {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .btn-approve:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
    }

    .btn-cancel {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .btn-cancel:hover {
        background: rgba(239, 68, 68, 0.3);
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-label {
        color: #e2e8f0;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .summary-box {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        color: #86efac;
        font-size: 0.95rem;
    }

    .form-glass {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 0.95rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        width: 100%;
        padding: 12px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 12px;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .student-info {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.students.import') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Import
            </a>
        </div>

        <div class="ethereal-card">
            <h2 class="page-header mb-1">Confirm Duplicate Merges</h2>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 25px;">{{ count($duplicates) }} duplicate(s) found. Review and confirm merging:</p>

            <!-- SUMMARY -->
            <div class="summary-box">
                <i class="fa-solid fa-info-circle me-2"></i>
                <strong>{{ count($duplicates) }}</strong> existing student(s) found with same name + phone.
                <strong>{{ count($newStudents) }}</strong> new student(s) will be created.
                Existing subjects will be merged.
            </div>

            <form id="mergeForm" action="{{ route('admin.students.import.confirm') }}" method="POST">
                @csrf
                <input type="hidden" name="teacher_id" value="{{ $teacher_id }}">

                <!-- DUPLICATES REVIEW -->
                @foreach($duplicates as $dup)
                    <div class="duplicate-item">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h5 style="color: white; margin: 0;">{{ $dup['existing']->name }}</h5>
                                <small style="color: #94a3b8;">{{ $dup['existing']->phone }}</small>
                            </div>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" name="merge_decisions[{{ $loop->index }}]" value="merge" 
                                       id="merge_{{ $loop->index }}" checked>
                                <label class="checkbox-label" for="merge_{{ $loop->index }}">
                                    Merge with import data
                                </label>
                            </div>
                        </div>

                        <div class="student-info">
                            <div>
                                <div class="info-label">Existing Subjects</div>
                                <div class="info-box">
                                    @php
                                        $existingSubjects = (array)($dup['existing']->subject_years ?? []);
                                    @endphp
                                    @if(count($existingSubjects) > 0)
                                        @foreach($existingSubjects as $subj)
                                            <span class="subject-badge">
                                                Subject #{{ $subj['subject_id'] ?? '?' }} - {{ $subj['year_label'] ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span style="color: #cbd5f0; font-size: 0.9rem;">No subjects assigned yet</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="info-label">Import Subjects (NEW)</div>
                                <div class="info-box">
                                    @php
                                        $importSubjects = (array)($dup['import']['subject_years'] ?? []);
                                    @endphp
                                    @if(count($importSubjects) > 0)
                                        @foreach($importSubjects as $subj)
                                            <span class="subject-badge" style="background: rgba(34, 197, 94, 0.3); border-color: rgba(34, 197, 94, 0.5);">
                                                Subject #{{ $subj['subject_id'] ?? '?' }} - {{ $subj['year_label'] ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span style="color: #cbd5f0; font-size: 0.9rem;">No subjects in import</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="subjects-merged">
                            <div class="info-label" style="margin-bottom: 10px;">Result After Merge</div>
                            <div class="info-box">
                                @php
                                    $allSubjects = collect($existingSubjects);
                                    foreach ($importSubjects as $newSubj) {
                                        $exists = $allSubjects->contains(function ($existing) use ($newSubj) {
                                            return ($existing['subject_id'] ?? null) == $newSubj['subject_id']
                                                && ($existing['year_label'] ?? '') === $newSubj['year_label'];
                                        });
                                        if (!$exists) {
                                            $allSubjects->push($newSubj);
                                        }
                                    }
                                @endphp
                                @foreach($allSubjects as $subj)
                                    <span class="subject-badge" style="background: rgba(16, 185, 129, 0.3); border-color: rgba(16, 185, 129, 0.5); color: #86efac;">
                                        <i class="fa-solid fa-check me-1"></i>Subject #{{ $subj['subject_id'] ?? '?' }} - {{ $subj['year_label'] ?? 'N/A' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Hidden data for this entry -->
                        <input type="hidden" name="entry_{{ $loop->index }}" value="{{ json_encode($dup['import']) }}">
                    </div>
                @endforeach

                <!-- NEW STUDENTS -->
                @if(count($newStudents) > 0)
                    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                        <h5 style="color: #86efac; margin-bottom: 15px;">
                            <i class="fa-solid fa-plus-circle me-2"></i>New Students to Create ({{ count($newStudents) }})
                        </h5>
                        @foreach($newStudents as $key => $student)
                            <div style="padding: 10px; border-bottom: 1px solid rgba(16, 185, 129, 0.1);">
                                <strong style="color: #e2e8f0;">{{ $student['fields']['name'] ?? 'N/A' }}</strong><br>
                                <small style="color: #94a3b8;">{{ $student['fields']['phone'] ?? 'N/A' }}</small>
                            </div>
                            <input type="hidden" name="new_students[{{ $key }}]" value="{{ json_encode($student) }}">
                        @endforeach
                    </div>
                @endif

                <!-- ACTION BUTTONS -->
                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn-approve" style="flex: 1;">
                        <i class="fa-solid fa-check me-2"></i>Confirm & Import
                    </button>
                    <a href="{{ route('admin.students.import') }}" class="btn-cancel" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
