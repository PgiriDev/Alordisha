@extends('layouts.app')

@section('content')

<style>
    /* --- GLASS THEME STYLES --- */

    /* Container Card */
    .ethereal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    /* Page Title */
    .page-title {
        font-family: 'Outfit', sans-serif;
        font-size: 2rem;
        color: white;
        font-weight: 300;
        margin-bottom: 30px;
    }

    /* Section Headings */
    .section-title {
        color: #e2e8f0;
        font-weight: 500;
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i { color: #6366f1; }

    /* Form Labels */
    .form-label {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 8px;
    }

    /* Glass Inputs & Selects */
    .form-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-glass:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: #6366f1;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
        color: white;
        outline: none;
    }

    /* Fix for Select Options (Dark bg for readability) */
    .form-glass option {
        background: #0f1115;
        color: white;
        padding: 10px;
    }

    /* Custom Date Input Icon Color */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
        opacity: 0.6;
    }
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }

    /* Glass Buttons */
    .btn-glass {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    /* Primary Action (Load Students) */
    .btn-primary-glass {
        background: rgba(99, 102, 241, 0.2);
        color: #818cf8;
        border-color: rgba(99, 102, 241, 0.4);
    }
    .btn-primary-glass:hover {
        background: #6366f1;
        color: white;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }

    /* Secondary Action (View Old) */
    .btn-secondary-glass {
        background: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
        border-color: rgba(255, 255, 255, 0.2);
    }
    .btn-secondary-glass:hover {
        background: white;
        color: black;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
    }

    /* Animations */
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }

    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- AJAX RESULT STYLING --- */
    /* Forces tables returned via AJAX to match the theme */
    #studentsList table, #oldAttendanceResult table {
        width: 100%;
        color: white;
        border-collapse: collapse;
        margin-top: 20px;
    }
    #studentsList th, #oldAttendanceResult th {
        text-align: left;
        padding: 15px;
        color: #94a3b8;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
    }
    #studentsList td, #oldAttendanceResult td {
        padding: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    #studentsList tr:hover, #oldAttendanceResult tr:hover {
        background: rgba(255,255,255,0.03);
    }
</style>

<div class="container-fluid">
    
    <h3 class="page-title fade-up">Attendance Management</h3>

    {{-- ================= TODAY ATTENDANCE SECTION ================= --}}
    <div class="ethereal-card fade-up delay-1">
        <h5 class="section-title">
            <i class="fa-solid fa-user-check"></i> Take Today's Attendance
        </h5>

        <form id="attendanceForm">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select form-glass" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" id="subject_id" class="form-select form-glass" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control form-glass" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" class="btn-glass btn-primary-glass" id="loadStudentsBtn">
                    <i class="fa-solid fa-users-viewfinder"></i> Load Student List
                </button>
            </div>
        </form>

        <div id="studentsList" class="mt-4">
            </div>
    </div>


    {{-- ================= VIEW PREVIOUS ATTENDANCE ================= --}}
    <div class="ethereal-card fade-up delay-2">
        <h5 class="section-title">
            <i class="fa-solid fa-clock-rotate-left"></i> View Previous Records
        </h5>

        <form id="oldAttendanceForm">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select form-glass" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select form-glass" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control form-glass" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" class="btn-glass btn-secondary-glass" id="viewOldBtn">
                    <i class="fa-regular fa-eye"></i> View Attendance
                </button>
            </div>
        </form>

        <div id="oldAttendanceResult" class="mt-4">
            </div>
    </div>

</div>

{{-- ================= AJAX ================= --}}
<script>
document.getElementById("loadStudentsBtn").addEventListener("click", function () {
    let btn = this;
    let originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
    
    let formData = new FormData(document.getElementById("attendanceForm"));

    fetch("{{ route('attendance.load') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById("studentsList").innerHTML = html;
        btn.innerHTML = originalText;
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        alert('Error loading students.');
    });
});


document.getElementById("viewOldBtn").addEventListener("click", function () {
    let btn = this;
    let originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';

    let formData = new FormData(document.getElementById("oldAttendanceForm"));

    fetch("{{ route('attendance.old') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById("oldAttendanceResult").innerHTML = html;
        btn.innerHTML = originalText;
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        alert('Error loading records.');
    });
});
</script>

@endsection