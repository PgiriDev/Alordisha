<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        body {
            margin: 0;
            padding: 0;
            background: #e8f7ee;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            color: #0f2f22;
        }

        .shell {
            width: 100%;
            box-sizing: border-box;
            padding: 28px 12px;
            background:
                radial-gradient(circle at 10% 0%, rgba(148, 230, 183, 0.45) 0%, rgba(232, 247, 238, 0) 48%),
                radial-gradient(circle at 100% 100%, rgba(106, 206, 152, 0.3) 0%, rgba(232, 247, 238, 0) 45%),
                #e8f7ee;
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

        .cta-wrap {
            text-align: center;
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

        .warning {
            margin-top: 14px;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            line-height: 1.6;
            color: #6b4f09;
            background: rgba(255, 244, 212, 0.78);
            border: 1px solid rgba(238, 188, 71, 0.44);
        }

        .cta {
            display: inline-block;
            margin-top: 16px;
            padding: 12px 18px;
            border-radius: 11px;
            background: linear-gradient(135deg, #11934f, #0d6c3a);
            color: #111111 !important;
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
            .warning {
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
                color: #c7f6db;
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
            .detail-row {
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

            .warning {
                color: #ffe5a7;
                background: rgba(119, 84, 8, 0.4);
                border-color: rgba(238, 188, 71, 0.42);
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
                    <img class="logo" src="{{ $logoUrl }}" alt="Alor Disha Logo">
                </div>
                <p class="hero-tag">Alor Disha Team</p>
            </div>

            <div class="content">
                <span class="badge">Login Activity</span>
                <h1 class="title">Welcome back, {{ $user->name }}</h1>
                <p class="text">A successful login was detected for your <strong>{{ ucfirst($user->role) }}</strong> account.</p>

                <div class="glass-box">
                    <div class="detail-row"><span class="label">Account Email:</span>{{ $user->email ?? 'N/A' }}</div>
                    <div class="detail-row"><span class="label">Access Role:</span>{{ ucfirst($user->role) }}</div>
                    <div class="detail-row"><span class="label">Time and Date:</span>{{ $meta['login_at'] ?? now()->format('d M Y, h:i A') }}</div>
                </div>

                <div class="warning">
                    <strong>Do not recognize this login?</strong> Secure your account immediately by resetting your password.
                </div>

                <div class="cta-wrap">
                    <a href="{{ url('/forgot-password') }}" class="cta" style="color:#111111 !important; text-decoration:none;">Secure My Account</a>
                </div>
            </div>

            <div class="footer">
                <div>&copy; {{ date('Y') }} Alor Disha. All rights reserved.</div>
                <div>Sridharnagar, South 24 Parganas, West Bengal, 743371</div>
            </div>
        </div>
    </div>
</body>
</html>
