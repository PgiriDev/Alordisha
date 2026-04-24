@extends('layouts.app')

@section('content')

<style>
    .fee-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 25px;
        backdrop-filter: blur(18px);
    }

    .filter-row {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr auto;
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
        transform: translateY(-1px);
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

    .fee-table {
        width: 100%;
        border-collapse: collapse;
        color: #e2e8f0;
        font-size: 0.9rem;
    }

    .fee-table th {
        text-align: left;
        padding: 12px 14px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .fee-table td {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        vertical-align: middle;
    }

    .fee-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-paid {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
    }

    .btn-paid:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .btn-paid-small {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .btn-paid-small:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .btn-receipt {
        background: rgba(99, 102, 241, 0.2);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #c7d2fe;
    }

    .btn-receipt:hover {
        background: rgba(99, 102, 241, 0.35);
        color: white;
    }

    .btn-whatsapp {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #34d399;
    }

    .btn-whatsapp:hover {
        background: rgba(16, 185, 129, 0.35);
        color: white;
    }

    .summary-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 15px;
        color: #cbd5f5;
        font-size: 0.9rem;
    }

    .summary-box {
        padding: 10px 16px;
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .text-muted {
        color: #cbd5f0 !important;
    }

    .small {
        font-size: 0.9rem !important;
    }

    .form-label {
        color: #94a3b8 !important;
        font-weight: 500 !important;
    }

    @media (max-width: 900px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="page-header m-0">Fees Tracker</h2>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('fees.settings') }}" class="btn-glass">Set Amount & Templates</a>
            <a href="{{ route('fees.report') }}" class="btn-glass">Monthly Report</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="fee-card mb-4">
        <form method="GET" action="{{ route('fees.index') }}" class="filter-row">
            <div>
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-control form-glass">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @if($selectedBranchId == $branch->id) selected @endif>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
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
            </div>
        </form>

        @php
            $paidCount = 0;
            $dueCount = 0;
            $paidAmount = 0;
            $dueAmount = 0;
            foreach ($rows as $row) {
                if ($row['paid']) {
                    $paidCount++;
                    $paidAmount += $row['amount'] ?? 0;
                } else {
                    $dueCount++;
                    $dueAmount += $row['amount'] ?? 0;
                }
            }
        @endphp

        <div class="summary-row">
            <div class="summary-box">Paid: {{ $paidCount }} (₹{{ number_format($paidAmount) }})</div>
            <div class="summary-box">Due: {{ $dueCount }} (₹{{ number_format($dueAmount) }})</div>
        </div>
    </div>

    <div class="fee-card">
        <div class="table-responsive">
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Branch</th>
                        <th>Subject</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $student = $row['student'];
                            $amount = $row['amount'];
                            $subjectName = $row['subject_name'];
                            $branchName = $student->branch?->name ?? 'N/A';
                            $isMerged = isset($row['is_merged']) && $row['is_merged'];
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold text-white">{{ $student->name }}</div>
                                <div class="text-muted small">{{ $student->phone }}</div>
                            </td>
                            <td>{{ $branchName }}</td>
                            <td>
                                @if($isMerged)
                                    {{ $subjectName }}
                                @else
                                    {{ $subjectName }} - {{ $row['year_label'] ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($amount)
                                    {{ number_format($amount) }}
                                @else
                                    <span class="text-warning">Set fee</span>
                                @endif
                            </td>
                            <td>
                                @if($row['paid'])
                                    <span class="status-pill status-paid">Paid</span>
                                @else
                                    <span class="status-pill status-due">Due</span>
                                @endif
                            </td>
                            <td>
                                <div class="fee-actions">
                                    @if($isMerged)
                                        {{-- For merged students with multiple subjects, show single Mark Paid button for all --}}
                                        @if(!$row['paid'] && $amount)
                                            <form method="POST" action="{{ route('fees.mark-paid-multiple') }}">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="branch_id" value="{{ $student->branch_id }}">
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                @foreach($row['subjects'] as $idx => $subject)
                                                    @if($subject['amount'])
                                                        <input type="hidden" name="year_labels[]" value="{{ $subject['year_label'] }}">
                                                    @endif
                                                @endforeach
                                                <button class="btn-glass btn-paid" type="submit">Mark Paid</button>
                                            </form>
                                        @endif
                                        @if($row['paid'] && $row['payment'])
                                            <a class="btn-glass btn-receipt" href="{{ route('fees.receipt', $row['payment']->id) }}" target="_blank">Receipt</a>
                                        @endif
                                    @else
                                        {{-- Single subject student --}}
                                        @if(!$row['paid'] && $amount && $row['year_label'])
                                            <form method="POST" action="{{ route('fees.mark-paid') }}">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="branch_id" value="{{ $student->branch_id }}">
                                                <input type="hidden" name="year_label" value="{{ $row['year_label'] }}">
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                <button class="btn-glass btn-paid" type="submit">Mark Paid</button>
                                            </form>
                                        @endif

                                        @if($row['paid'])
                                            <a class="btn-glass btn-receipt" href="{{ route('fees.receipt', $row['payment']->id) }}" target="_blank">Receipt</a>
                                        @endif
                                    @endif

                                    @if($row['whatsapp'])
                                        @php
                                            $message = $row['paid'] ? $row['paid_message'] : $row['due_message'];
                                            // Use the WhatsApp web API format that preserves UTF-8 characters including emojis
                                            $encodedMessage = str_replace(['+', '%7E'], ['%20', '~'], urlencode($message));
                                        @endphp
                                        <a class="btn-glass btn-whatsapp" target="_blank" href="https://wa.me/{{ $row['whatsapp'] }}?text={{ $encodedMessage }}">
                                            WhatsApp
                                        </a>
                                    @else
                                        <span class="text-muted small">No number</span>
                                    @endif
                                </div>
                                @if(!$row['paid'] && count($row['due_months']) > 1)
                                    <div class="text-muted small mt-1">Due months: {{ implode(', ', $row['due_months']) }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
