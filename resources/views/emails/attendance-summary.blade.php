<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f2fff7;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            color: #0f2f22;
        }

        .shell {
            width: 100%;
            box-sizing: border-box;
            padding: 28px 12px;
            background:
                radial-gradient(circle at 8% 0%, rgba(168, 241, 199, 0.5) 0%, rgba(242, 255, 247, 0) 48%),
                radial-gradient(circle at 100% 100%, rgba(137, 223, 177, 0.34) 0%, rgba(242, 255, 247, 0) 45%),
                #f2fff7;
        }

        .card {
            max-width: 720px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.62);
            border: 1px solid rgba(120, 201, 155, 0.44);
            box-shadow: 0 22px 44px rgba(16, 75, 50, 0.18);
            backdrop-filter: blur(12px);
        }

        .hero {
            padding: 22px 24px 14px;
            text-align: center;
            background: linear-gradient(130deg, #0f5132, #178f52);
        }

        .logo-wrap {
            width: 216px;
            height: 150px;
            margin: 0 auto 4px;
            border-radius: 0;
            background: transparent;
            border: none;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-sizing: border-box;
            overflow: hidden;
        }

        .logo {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 0;
            transform: translateX(16px) translateY(-28px) scale(1.34);
            transform-origin: center top;
        }

        .hero-tag {
            margin: 2px 0 0;
            font-size: 12px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #ddffe9;
            font-weight: 700;
            line-height: 1.1;
        }

        .content {
            padding: 24px 22px 12px;
        }

        .badge {
            display: inline-block;
            margin-bottom: 14px;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: #0e6b3f;
            background: rgba(165, 236, 194, 0.45);
            border: 1px solid rgba(70, 175, 113, 0.34);
        }

        .title {
            margin: 0 0 10px;
            font-size: 28px;
            line-height: 1.25;
            color: #0d3625;
        }

        .text {
            margin: 0 0 14px;
            font-size: 15px;
            line-height: 1.7;
            color: #21533d;
        }

        .glass-box {
            margin-top: 14px;
            border-radius: 14px;
            border: 1px solid rgba(120, 201, 155, 0.42);
            background: rgba(240, 255, 247, 0.5);
            backdrop-filter: blur(9px);
            overflow: hidden;
        }

        .detail-row {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(120, 201, 155, 0.32);
            font-size: 14px;
            line-height: 1.5;
            color: #1f5a40;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 700;
            color: #12442f;
            margin-right: 6px;
        }

        .chips {
            margin-top: 14px;
        }

        .chip {
            display: inline-block;
            margin: 0 8px 8px 0;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .chip-total {
            color: #0f3f2c;
            background: rgba(214, 245, 228, 0.9);
            border-color: rgba(120, 201, 155, 0.5);
        }

        .chip-present {
            color: #0f6b3d;
            background: rgba(184, 245, 210, 0.9);
            border-color: rgba(74, 201, 128, 0.5);
        }

        .chip-absent {
            color: #8c3b3b;
            background: rgba(255, 224, 224, 0.9);
            border-color: rgba(240, 163, 163, 0.6);
        }

        .names {
            margin-top: 12px;
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid rgba(120, 201, 155, 0.34);
            background: rgba(240, 255, 247, 0.45);
            color: #1f5a40;
            font-size: 14px;
            line-height: 1.7;
        }

        .names strong {
            color: #12442f;
        }

        .name-list {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        .name-list li {
            margin: 0 0 4px;
        }

        .footer {
            margin-top: 20px;
            padding: 14px 22px 20px;
            border-top: 1px solid rgba(120, 201, 155, 0.28);
            color: #4e7a64;
            font-size: 12px;
            line-height: 1.75;
            text-align: center;
        }

        @media only screen and (max-width: 640px) {
            .shell {
                padding: 16px 8px;
            }

            .hero {
                padding: 20px 14px 16px;
            }

            .logo-wrap {
                width: 180px;
                height: 126px;
                margin: 0 auto 2px;
                border-radius: 0;
            }

            .logo {
                transform: translateX(12px) translateY(-22px) scale(1.34);
            }

            .content {
                padding: 18px 14px 10px;
            }

            .title {
                font-size: 22px;
            }

            .text,
            .detail-row,
            .names {
                font-size: 14px;
            }

            .footer {
                padding: 12px 14px 16px;
            }
        }

        @media (prefers-color-scheme: dark) {
            body,
            .shell {
                background: #06180f;
                color: #d6f8e4;
            }

            .shell {
                background:
                    radial-gradient(circle at 14% 0%, rgba(33, 114, 75, 0.52) 0%, rgba(6, 24, 15, 0) 50%),
                    radial-gradient(circle at 100% 100%, rgba(22, 85, 58, 0.48) 0%, rgba(6, 24, 15, 0) 45%),
                    #06180f;
            }

            .card {
                background: rgba(9, 38, 24, 0.64);
                border-color: rgba(72, 171, 121, 0.48);
                box-shadow: 0 24px 50px rgba(0, 0, 0, 0.55);
            }

            .hero {
                background: linear-gradient(130deg, #0a2f1f, #0f6a3c);
            }

            .hero-tag {
                color: #ecfff4;
            }

            .badge {
                color: #c6f2d8;
                background: rgba(18, 118, 69, 0.44);
                border-color: rgba(100, 210, 155, 0.38);
            }

            .title {
                color: #e8fff2;
            }

            .text,
            .detail-row,
            .names {
                color: #bcebd1;
            }

            .label,
            .names strong {
                color: #e2ffe8;
            }

            .glass-box,
            .names {
                background: rgba(8, 43, 27, 0.56);
                border-color: rgba(72, 171, 121, 0.42);
            }

            .detail-row {
                border-color: rgba(72, 171, 121, 0.3);
            }

            .chip-total {
                color: #d5fae6;
                background: rgba(34, 89, 63, 0.7);
                border-color: rgba(110, 190, 150, 0.45);
            }

            .chip-present {
                color: #d1ffe5;
                background: rgba(19, 120, 73, 0.7);
                border-color: rgba(94, 212, 158, 0.5);
            }

            .chip-absent {
                color: #ffd5d5;
                background: rgba(122, 50, 50, 0.65);
                border-color: rgba(238, 147, 147, 0.45);
            }

            .footer {
                color: #8ac8a7;
                border-color: rgba(72, 171, 121, 0.28);
            }
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
        $logoUrl = file_exists($logoPath) ? $message->embed($logoPath) : ($logoSrc ?? asset('logo.png'));
    @endphp

    <div class="shell">
        <div class="card">
            <div class="hero">
                <div class="logo-wrap">
                    <img class="logo" src="{{ $logoUrl }}" alt="Alor Disha" width="88" height="88" loading="eager" decoding="sync">
                </div>
                <p class="hero-tag">Attendance Report</p>
            </div>

            <div class="content">
                <span class="badge">Attendance Submitted</span>
                <h2 class="title">Daily Attendance Summary</h2>
                <p class="text">Your attendance entry has been recorded successfully in Alor Disha.</p>

                <div class="glass-box">
                    <div class="detail-row"><span class="label">Teacher:</span>{{ $summary['teacher_name'] }}</div>
                    <div class="detail-row"><span class="label">Branch:</span>{{ $summary['branch_name'] }}</div>
                    <div class="detail-row"><span class="label">Subject:</span>{{ $summary['subject_name'] }}</div>
                    <div class="detail-row"><span class="label">Date:</span>{{ $summary['date'] }}</div>
                    <div class="detail-row"><span class="label">Submitted At:</span>{{ $summary['submitted_at'] }}</div>
                </div>

                <div class="chips">
                    <span class="chip chip-total">Total: {{ $summary['total_students'] }}</span>
                    <span class="chip chip-present">Present: {{ $summary['present_count'] }}</span>
                    <span class="chip chip-absent">Absent: {{ $summary['absent_count'] }}</span>
                </div>

                <div class="names">
                    <strong>Present Students:</strong>
                    @if (!empty($summary['present_names']))
                        <ul class="name-list">
                            @foreach ($summary['present_names'] as $name)
                                <li>{{ $name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div>None</div>
                    @endif
                </div>

                <div class="names">
                    <strong>Absent Students:</strong>
                    @if (!empty($summary['absent_names']))
                        <ul class="name-list">
                            @foreach ($summary['absent_names'] as $name)
                                <li>{{ $name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div>None</div>
                    @endif
                </div>

                @if (($summary['total_students'] ?? 0) > 50)
                    <p class="text" style="margin-top:12px;">Full attendance sheet is attached as a PDF because student count is more than 50.</p>
                @endif
            </div>

            <div class="footer">
                <div><strong>Sridharnagar, South 24 Parganas, West Bengal, 743371</strong></div>
                <div>&copy; {{ date('Y') }} Alor Disha. All rights reserved.</div>
            </div>
        </div>
    </div>
</body>
</html>
