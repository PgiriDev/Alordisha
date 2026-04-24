<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; }
        h2 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; font-size: 12px; }
        th { background: #f3f4f6; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; }
        .summary { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Monthly Report</h2>
    <div>Month: {{ $monthName }} {{ $year }}</div>

    @foreach($summary as $branchSummary)
        <h3>{{ $branchSummary['branch']->name }}</h3>
        <table>
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
                        <td>{{ $row['paid'] ? 'Paid' : 'Due' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="summary">Branch Paid: {{ number_format($branchSummary['paid_total']) }} | Branch Due: {{ number_format($branchSummary['due_total']) }}</div>
    @endforeach

    <div class="summary">Total Paid: {{ number_format($totalPaid) }} | Total Due: {{ number_format($totalDue) }}</div>
</body>
</html>
