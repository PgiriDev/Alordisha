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
        font-family: 'DM Serif Display', serif;
        font-weight: 400;
        font-size: 2rem;
        color: white;
        margin-bottom: 0.5rem;
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
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .alert {
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #86efac;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.2);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fcd34d;
    }

    .alert ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .alert ul li {
        padding: 4px 0;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.students') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Students
            </a>
        </div>

        <div class="ethereal-card">
            <h2 class="page-header mb-1">Bulk Import Students</h2>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 25px;">Upload a CSV file to import multiple students. Just provide: name, phone, whatsapp, father_name, branch, subject, subject_year</p>

            @if(session('success'))
                <div class="alert alert-success mb-3">
                    <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <i class="fa-solid fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                </div>
            @endif

            @if(session('import_errors'))
                <div class="alert alert-warning mb-3">
                    <div class="fw-semibold mb-2"><i class="fa-solid fa-exclamation-triangle me-2"></i>Some rows were skipped:</div>
                    <ul style="max-height: 200px; overflow-y: auto; font-size: 0.9rem;">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.students.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- FORM FIELDS -->
                <div class="form-row">
                    <div>
                        <label class="form-label">Select Teacher <span style="color: #ef4444;">*</span></label>
                        <select name="teacher_id" class="form-control form-glass" required>
                            <option value="">-- Choose Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->phone ?? 'No phone' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Upload CSV File <span style="color: #ef4444;">*</span></label>
                        <input type="file" name="csv_file" accept=".csv,.txt" class="form-control form-glass" required>
                        <small style="color: #94a3b8; font-size: 0.75rem; display: block; margin-top: 4px;">Max 5MB. CSV or TXT format.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">
                    <i class="fa-solid fa-file-import me-2"></i>Import Students
                </button>
            </form>

            <!-- INSTRUCTIONS -->
            <div class="note-box">
                <div class="fw-semibold mb-3">📋 CSV Columns Required</div>
                <div style="background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <strong style="color: #fbbf24;">All Mandatory:</strong> 
                    <code>name</code>, <code>phone</code>, <code>whatsapp</code>, <code>father_name</code>, <code>branch</code>, <code>subject</code>, <code>subject_year</code>
                </div>
                <div style="line-height: 1.8; color: #cbd5f0;">
                    <div>✓ <strong>name</strong> - Student's full name</div>
                    <div>✓ <strong>phone</strong> - Mobile number</div>
                    <div>✓ <strong>whatsapp</strong> - WhatsApp number</div>
                    <div>✓ <strong>father_name</strong> - Father's name</div>
                    <div>✓ <strong>branch</strong> - Branch name (must exist in system, e.g., "Mumbai", "Delhi")</div>
                    <div>✓ <strong>subject</strong> - Subject name (must exist in system)</div>
                    <div>✓ <strong>subject_year</strong> - Year label (e.g., "PP-1", "1ST", "KISHALAY-1")</div>
                </div>
            </div>

            <div class="note-box">
                <div class="fw-semibold mb-3">📝 Sample CSV Format</div>
                <table class="csv-table">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>phone</th>
                            <th>whatsapp</th>
                            <th>father_name</th>
                            <th>branch</th>
                            <th>subject</th>
                            <th>subject_year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rajesh Kumar</td>
                            <td>01712345678</td>
                            <td>01712345678</td>
                            <td>Ram Kumar</td>
                            <td>Mumbai</td>
                            <td>English</td>
                            <td>1ST</td>
                        </tr>
                        <tr>
                            <td>Isha Patel</td>
                            <td>01787654321</td>
                            <td>01787654321</td>
                            <td>Vikram Patel</td>
                            <td>Delhi</td>
                            <td>Mathematics</td>
                            <td>2ND</td>
                        </tr>
                        <tr>
                            <td>Amin Khan</td>
                            <td>01798765432</td>
                            <td>01798765432</td>
                            <td>Hassan Khan</td>
                            <td>Mumbai</td>
                            <td>Science</td>
                            <td>KISHALAY-1</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="note-box" style="background: rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.3);">
                <div class="fw-semibold mb-3">ℹ️ How Duplicates Are Handled</div>
                <div style="line-height: 1.8; color: #cbd5f0;">
                    <div>• If a student with the <strong>same name + phone</strong> already exists:</div>
                    <div style="margin-left: 20px; margin-top: 5px;">
                        - Their subjects will be <strong>merged</strong> (no duplicate subjects)<br>
                        - Empty fields will be filled with data from the CSV
                    </div>
                    <div style="margin-top: 15px;">• <strong>Automatic:</strong> Branch and subject names are resolved from your system</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
