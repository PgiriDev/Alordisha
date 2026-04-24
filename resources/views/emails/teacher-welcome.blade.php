<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Alor Disha</title>
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
            max-width: 680px;
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
            background: linear-gradient(130deg, #3bbf7c, #79dba4);
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

        .tips {
            margin: 14px 0 0;
            padding-left: 18px;
            color: #1f5a40;
            font-size: 14px;
            line-height: 1.7;
        }

        .cta {
            display: inline-block;
            margin-top: 16px;
            padding: 12px 18px;
            border-radius: 11px;
            background: linear-gradient(135deg, #11934f, #0d6c3a);
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
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
            .tips {
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
                background: linear-gradient(130deg, #2c9f65, #56c888);
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
            .tips {
                color: #bcebd1;
            }

            .label {
                color: #e2ffe8;
            }

            .glass-box {
                background: rgba(8, 43, 27, 0.56);
                border-color: rgba(72, 171, 121, 0.42);
            }

            .detail-row {
                border-color: rgba(72, 171, 121, 0.3);
            }

            .cta {
                background: linear-gradient(135deg, #34c67a, #199457);
                color: #052311;
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
                <p class="hero-tag">Alor Disha Team</p>
            </div>

            <div class="content">
                <span class="badge">Teacher Account Created</span>
                <h2 class="title">Welcome, {{ $teacher->name }}</h2>
                <p class="text">Your teacher account was created successfully by {{ $adminName }}.</p>

                <div class="glass-box">
                    <div class="detail-row"><span class="label">Name:</span>{{ $teacher->name }}</div>
                    <div class="detail-row"><span class="label">Email:</span>{{ $teacher->email ?? 'N/A' }}</div>
                    <div class="detail-row"><span class="label">Role:</span>Teacher</div>
                </div>

                <p class="text" style="margin-top:14px;">Please sign in and start managing students, classes, and attendance.</p>
                <ul class="tips">
                    <li>Complete your profile information</li>
                    <li>Review assigned branches and subjects</li>
                    <li>Start daily attendance updates</li>
                </ul>

                <a class="cta" href="{{ url('/login') }}">Login to Dashboard</a>
            </div>

            <div class="footer">
                <div><strong>Sridharnagar, South 24 Parganas, West Bengal, 743371</strong></div>
                <div>&copy; {{ date('Y') }} Alor Disha. All rights reserved.</div>
            </div>
        </div>
    </div>
</body>
</html>
