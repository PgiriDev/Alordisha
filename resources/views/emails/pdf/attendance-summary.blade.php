<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 18px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #173a2a;
            background: #f5fff9;
        }

        .sheet {
            border: 1px solid #b9e6cb;
            border-radius: 10px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #3bbf7c, #79dba4);
            color: #effff5;
            padding: 14px 16px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .sub {
            margin: 0;
            font-size: 11px;
            opacity: 0.95;
        }

        .logo-cell {
            width: 160px;
            text-align: right;
            vertical-align: middle;
        }

        .logo {
            width: 144px;
            height: 144px;
            border-radius: 0;
            object-fit: contain;
            background: transparent;
            border: none;
            padding: 0;
            box-sizing: border-box;
        }

        .meta {
            padding: 12px 16px 8px;
            line-height: 1.65;
            background: rgba(220, 247, 232, 0.55);
            border-bottom: 1px solid #c6ead5;
        }

        .meta strong {
            color: #0f5132;
        }

        .stats {
            margin: 12px 16px;
            border: 1px solid #b9e6cb;
            background: #ecfbf2;
            border-radius: 8px;
            padding: 10px 12px;
            line-height: 1.6;
        }

        .stats strong {
            color: #0f5132;
        }

        table {
            width: calc(100% - 32px);
            margin: 0 16px 16px;
            border-collapse: collapse;
            background: #ffffff;
        }

        th,
        td {
            border: 1px solid #cdecd9;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #dbf6e7;
            color: #0f5132;
            font-weight: 700;
        }

        td {
            color: #1f4f39;
        }

        .present {
            color: #0f7d46;
            font-weight: 700;
        }

        .absent {
            color: #a33636;
            font-weight: 700;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('Mail logo.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('alordish stamp.png');
        }

        $logoDataUri = null;
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : ($ext === 'gif' ? 'image/gif' : 'image/png');
            $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
        }
    @endphp

    <div class="sheet">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>
                        <p class="brand-title">Alor Disha - Attendance Summary</p>
                        <p class="sub">Official attendance attachment report</p>
                    </td>
                    <td class="logo-cell">
                        @if ($logoDataUri)
                            <img class="logo" src="{{ $logoDataUri }}" alt="Alor Disha Logo">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="meta">
            <strong>Teacher:</strong> {{ $summary['teacher_name'] }}<br>
            <strong>Branch:</strong> {{ $summary['branch_name'] }}<br>
            <strong>Subject:</strong> {{ $summary['subject_name'] }}<br>
            <strong>Date:</strong> {{ $summary['date'] }}<br>
            <strong>Submitted At:</strong> {{ $summary['submitted_at'] }}
        </div>

        <div class="stats">
            <strong>Total Students:</strong> {{ $summary['total_students'] }} |
            <strong>Present:</strong> {{ $summary['present_count'] }} |
            <strong>Absent:</strong> {{ $summary['absent_count'] }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 62%;">Student Name</th>
                    <th style="width: 30%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach (($summary['attendance_rows'] ?? []) as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row['student_name'] ?? '-' }}</td>
                        <td class="{{ ($row['status'] ?? '') === 'Present' ? 'present' : 'absent' }}">
                            {{ $row['status'] ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
