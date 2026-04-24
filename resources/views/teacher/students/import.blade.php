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
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    .page-header {
        font-family: 'Outfit', sans-serif;
        font-weight: 300;
        font-size: 2rem;
        color: white;
        margin-bottom: 0;
    }

    .form-label {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-glass {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all .3s ease;
    }

    .form-glass:focus {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        outline: none;
        color: white;
    }

    .form-glass option {
        background-color: #18181b;
        color: white;
    }

    .btn-submit {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        width: 100%;
        padding: 12px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        transform: translateY(-2px);
    }

    .note-box {
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 16px;
        padding: 20px;
        color: #e2e8f0;
        font-size: 0.95rem;
        margin-top: 30px;
    }

    .note-box code {
        color: #fbbf24;
        background: rgba(99, 102, 241, 0.25);
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 500;
    }

    .note-box .fw-semibold {
        color: #ffffff;
        font-size: 1.05rem;
    }

    .note-box .text-muted {
        color: #cbd5f0 !important;
    }

    pre.sample {
        background: rgba(2, 6, 23, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 16px;
        color: #e2e8f0;
        font-size: 0.85rem;
        overflow-x: auto;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .csv-table {
        width: 100%;
        border-collapse: collapse;
        color: #e2e8f0;
        font-size: 0.9rem;
        margin-top: 12px;
    }

    .csv-table thead th {
        text-align: left;
        padding: 10px 12px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-weight: 600;
        background: rgba(99, 102, 241, 0.1);
    }

    .csv-table tbody td {
        padding: 8px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .csv-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('students.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="ethereal-card">
            <h2 class="page-header mb-4">Import Students (CSV)</h2>

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('import_errors'))
                <div class="alert alert-warning mb-3">
                    <div class="fw-semibold mb-2">Some rows were skipped:</div>
                    <ul class="mb-0" style="max-height: 200px; overflow-y: auto; font-size: 0.9rem;">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('students.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- DROPDOWNS -->
                <div class="form-row">
                    <div>
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-control form-glass" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-control form-glass" required>
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Year Label</label>
                        <select name="year_label" class="form-control form-glass" required>
                            <option value="">-- Select Year --</option>
                            @foreach($yearLabels as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CSV FILE -->
                <div class="mb-3">
                    <label class="form-label">Upload CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="form-control form-glass" required>
                </div>

                <button type="submit" class="btn btn-submit">
                    <i class="fa-solid fa-file-import me-2"></i>Import Now
                </button>
            </form>

            <!-- INSTRUCTIONS -->
            <div class="note-box">
                <div class="fw-semibold mb-3">📋 CSV Columns Required</div>
                <div class="mb-3" style="background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px;">
                    <code>name</code>, <code>phone</code>, <code>whatsapp</code>
                </div>
                <div class="text-muted small mb-3">
                    <strong style="color: #e2e8f0;">Optional columns:</strong> <code>father_name</code>, <code>dob</code>, <code>class_level</code>, <code>address</code>, <code>institution</code>, <code>registration_number</code>
                </div>
                <div class="text-muted small" style="line-height: 1.8;">
                    <div>✓ Student matched by <code>name + phone</code> combination</div>
                    <div>✓ Use same <code>phone</code> and <code>whatsapp</code> if same number</div>
                    <div>✓ Duplicate students get same subject added to their profile</div>
                    <div>✓ All students will get the selected Branch + Subject + Year</div>
                </div>
            </div>

            <div class="note-box">
                <div class="fw-semibold mb-3">📝 Sample CSV Format</div>
                <table class="csv-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>WhatsApp</th>
                            <th>Father Name</th>
                            <th>DOB</th>
                            <th>Class</th>
                            <th>Address</th>
                            <th>Institution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rajesh Kumar</td>
                            <td>01712345678</td>
                            <td>01712345678</td>
                            <td>Ram Kumar</td>
                            <td>2012-03-15</td>
                            <td>Class 5</td>
                            <td>Mirpur, Dhaka</td>
                            <td>MVKC</td>
                        </tr>
                        <tr>
                            <td>Isha Patel</td>
                            <td>01787654321</td>
                            <td>01787654321</td>
                            <td>Vikram Patel</td>
                            <td>2013-05-22</td>
                            <td>Class 4</td>
                            <td>Dhanmondi, Dhaka</td>
                            <td>SSSP</td>
                        </tr>
                        <tr>
                            <td>Amin Khan</td>
                            <td>01798765432</td>
                            <td>01798765432</td>
                            <td>Hassan Khan</td>
                            <td>2012-07-10</td>
                            <td>Class 6</td>
                            <td>Gulshan, Dhaka</td>
                            <td>MVKC</td>
                        </tr>
                        <tr>
                            <td>Priya Sharma</td>
                            <td>01723456789</td>
                            <td>01723456789</td>
                            <td>Amit Sharma</td>
                            <td>2011-12-08</td>
                            <td>Class 7</td>
                            <td>Banani, Dhaka</td>
                            <td>SSSP</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
