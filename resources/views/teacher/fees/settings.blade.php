@extends('layouts.app')

@section('content')

<style>
    .settings-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
        backdrop-filter: blur(18px);
    }

    .form-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 0.9rem;
    }

    .form-glass option {
        background-color: #18181b;
        color: white;
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        padding: 10px 18px;
        border-radius: 999px;
        font-weight: 500;
        text-decoration: none;
        transition: 0.3s ease;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .fee-table {
        width: 100%;
        border-collapse: collapse;
        color: #e2e8f0;
        font-size: 0.9rem;
    }

    .fee-table th {
        text-align: left;
        padding: 10px 12px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .fee-table td {
        padding: 10px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="page-header m-0">Fees Settings</h2>
        <a href="{{ route('fees.index') }}" class="btn-glass">Back to Tracker</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="settings-card">
        <h5 class="text-white mb-3">Set Amount (Branch Wise)</h5>
        <form method="GET" action="{{ route('fees.settings') }}" class="mb-3">
            <label class="form-label">Select Branch</label>
            <select name="branch_id" class="form-control form-glass" onchange="this.form.submit()">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @if($selectedBranchId == $branch->id) selected @endif>{{ $branch->name }}</option>
                @endforeach
            </select>
        </form>

        <form method="POST" action="{{ route('fees.settings.save') }}">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
            <div class="table-responsive">
                <table class="fee-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearLabels as $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>
                                    <input type="number" min="0" name="amounts[{{ $label }}]" class="form-control form-glass" value="{{ $feeSettings[$label]->amount ?? '' }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn-glass mt-3">Save Amounts</button>
        </form>
    </div>

    <div class="settings-card">
        <h5 class="text-white mb-3">WhatsApp Message Templates</h5>
        <form method="POST" action="{{ route('fees.templates.save') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Paid Message</label>
                <textarea name="paid_template" rows="12" class="form-control form-glass">{{ $paidTemplate }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Due Message</label>
                <textarea name="due_template" rows="10" class="form-control form-glass">{{ $dueTemplate }}</textarea>
            </div>
            <div class="text-muted small mb-3">
                Placeholders: <strong>STUDENT_NAME</strong>, <strong>MONTH</strong>, <strong>MONTHS</strong>, <strong>YEAR</strong>, <strong>AMOUNT</strong>, <strong>DATE</strong>, <strong>BRANCH</strong>, <strong>RECEIPT_NO</strong>, <strong>UNIT</strong>
            </div>
            <button type="submit" class="btn-glass">Save Templates</button>
        </form>
    </div>

    <div class="settings-card">
        <h5 class="text-white mb-3">Receipt Logo & Header</h5>
        <form method="POST" action="{{ route('fees.logo.save') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Header Text</label>
                <input type="text" name="header_text" class="form-control form-glass" value="{{ $receiptSettings->header_text }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Logo (PNG/JPG, < 500KB)</label>
                @if($receiptSettings->logo_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $receiptSettings->logo_path) }}" alt="Current Logo" style="max-width: 150px; max-height: 80px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); padding: 8px; background: rgba(255,255,255,0.05);">
                        <p class="text-muted small mt-1">Current Logo</p>
                    </div>
                @endif
                <input type="file" name="logo" class="form-control form-glass">
            </div>
            <button type="submit" class="btn-glass">Save Receipt Settings</button>
        </form>
    </div>
</div>

@endsection
