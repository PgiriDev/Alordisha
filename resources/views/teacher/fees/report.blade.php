@extends('layouts.app')

@section('content')

<style>
    .report-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
        backdrop-filter: blur(18px);
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

    .status-pill {
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-paid {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-due {
        background: rgba(248, 113, 113, 0.2);
        color: #f87171;
        border: 1px solid rgba(248, 113, 113, 0.3);
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

    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 15px;
        align-items: end;
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

    @media (max-width: 900px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="page-header m-0">Monthly Report</h2>
        <a href="{{ route('fees.index') }}" class="btn-glass">Back to Tracker</a>
    </div>

    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    <div class="report-card">
        <form method="GET" action="{{ route('fees.report') }}" class="filter-row">
            <div>
                <label class="form-label">Month</label>
                <select name="month" class="form-control form-glass">
                    @foreach($monthNames as $m => $label)
                        <option value="{{ $m }}" @if($month == $m) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Year</label>
                <select name="year" class="form-control form-glass">
                    @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" @if($year == $y) selected @endif>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="btn-glass">Apply</button>
                <a href="{{ route('fees.report.pdf', ['month' => $month, 'year' => $year]) }}" class="btn-glass ms-2">Download PDF</a>
            </div>
        </form>
    </div>

    @foreach($summary as $branchSummary)
        <div class="report-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-white">{{ $branchSummary['branch']->name }}</h5>
                <div class="text-muted small">Paid: {{ number_format($branchSummary['paid_total']) }} | Due: {{ number_format($branchSummary['due_total']) }}</div>
            </div>

            <div class="table-responsive">
                <table class="fee-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchSummary['rows'] as $row)
                            @php
                                $isMerged = isset($row['is_merged']) && $row['is_merged'];
                            @endphp
                            <tr>
                                <td>{{ $row['student']->name }}</td>
                                <td>
                                    @if($isMerged)
                                        {{ $row['subject_name'] }}
                                    @else
                                        {{ $row['subject_name'] }} - {{ $row['year_label'] ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>{{ number_format($row['amount']) }}</td>
                                <td>
                                    @if($row['paid'])
                                        <span class="status-pill status-paid">Paid</span>
                                    @else
                                        <span class="status-pill status-due">Due</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="report-card">
        <h5 class="text-white mb-2">Overall Summary ({{ $monthName }} {{ $year }})</h5>
        <div class="text-muted">Total Paid: {{ number_format($totalPaid) }} | Total Due: {{ number_format($totalDue) }}</div>
    </div>
</div>

@endsection
