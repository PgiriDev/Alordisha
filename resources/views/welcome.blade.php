@extends('layouts.app')

@section('title', 'Alor Disha - Modern Learning')

@section('content')
    @php
        use App\Models\Notice;

        $isLoggedIn = session()->has('user_id');
        $role = session('role');
        $dashboardRoute = $role === 'admin' ? route('admin.dashboard') : route('teacher.dashboard');
        try {
            $welcomeNotices = Notice::query()->visible()->latest()->take(25)->get();
        } catch (\Throwable $e) {
            report($e);
            $welcomeNotices = collect();
        }

        $welcomeNoticePayload = $welcomeNotices->map(function ($notice) {
            return [
                'id' => $notice->id,
                'created_at' => optional($notice->created_at)->toIso8601String(),
            ];
        })->values();
        
        $quotes = [
            [
                'text' => 'The highest education is that which does not merely give us information but makes our life in harmony with all existence.',
                'author' => 'Rabindranath Tagore',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSA0KtFRhCPJ6y-7nakPb_exLvEn7Hh_P9qUBbzI2AeutHy5pa9n0grbKzvs_PCY5m9EeJa&s'
            ],
            [
                'text' => 'Arise, awake, and stop not till the goal is reached.',
                'author' => 'Swami Vivekananda',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdPWQGeTznWA3lmoqX-TG1xnsia4WQfEvtfQ&s/512px-Swami_Vivekananda_Jaipur_1898.jpg'
            ],
            [
                'text' => 'We are born to break boundaries, not to build them.',
                'author' => 'Kazi Nazrul Islam',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSUHxhbxbtt77NnsErsDh2cTeArJVkSN1q7dw&s/512px-Kazi_Nazrul_Islam.jpg'
            ],
            [
                'text' => 'All of us do not have equal talent....',
                'author' => 'DR. APJ Abdul Kalam',
                'image' => 'https://www.thehawk.in/_next/image?url=https%3A%2F%2Fd2py10ayqu2jji.cloudfront.net%2Feb7fee5f-3191-4e7b-a0bb-6de866af5740%2F202507273463853-9138c0bf-e76f-499d-afc8-a7fd8c9f7ca3.jpg&w=3840&q=75'
            ]
        ];
        $randomQuote = collect($quotes)->random();
    @endphp

    <style>
        :root {
            --bg-color: #2b3037;
            --surface: #2f353d;
            --surface-soft: #353c45;
            --shadow-light: #3a424c;
            --shadow-dark: #1e2228;
            --primary: #c5a059;
            --primary-hover: #e0bb6e;
            --secondary: #a88af0;
            --text-main: #e7ebf1;
            --text-muted: #9aa5b5;
            --glass-bg: var(--surface);
            --glass-border: rgba(255, 255, 255, 0.03);
            --glass-shadow: 14px 14px 28px var(--shadow-dark), -14px -14px 28px var(--shadow-light);
            --inset-shadow: inset 7px 7px 14px var(--shadow-dark), inset -7px -7px 14px var(--shadow-light);
        }

        [data-theme='dark'] {
            --bg-color: #1f2329;
            --surface: #232830;
            --surface-soft: #29303a;
            --shadow-light: #2f3742;
            --shadow-dark: #171b20;
            --text-main: #e7ebf1;
            --text-muted: #92a0b2;
        }

        html,
        body {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        /* Hide visual page scrollbars on welcome screen while preserving scroll */
        html,
        body,
        .welcome-shell {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        .welcome-shell::-webkit-scrollbar {
            display: none;
        }

        .welcome-shell {
            background: radial-gradient(circle at 20% 0%, #353b47 0%, var(--bg-color) 40%, #1c2025 100%);
            color: var(--text-main);
            font-family: 'Montserrat', 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            width: 100%;
            margin-left: 0;
            margin-right: 0;
            transition: background 0.4s ease, color 0.4s ease;
        }

        .welcome-shell::before,
        .welcome-shell::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            z-index: 0;
            filter: blur(90px);
            opacity: 0.22;
            animation: float 14s infinite ease-in-out alternate;
        }
        .welcome-shell::before { width: 420px; height: 420px; background: #ab8d52; top: -140px; left: -140px; }
        .welcome-shell::after { width: 360px; height: 360px; background: #6d5ec2; bottom: 6%; right: -80px; animation-delay: -6s; }

        .glass-card {
            background: var(--surface);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            border-radius: 1.6rem;
            position: relative;
            z-index: 10;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            100% { transform: translateY(-26px) scale(1.03); }
        }
        @keyframes metallicShift {
            to { background-position: 200% center; }
        }

        .animate-up { animation: fadeInUp 0.6s ease forwards; opacity: 1; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        .welcome-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            margin: 1.5rem auto;
            width: min(99%, 1500px);
            position: sticky;
            top: 1.2rem;
            z-index: 50;
        }
        .welcome-brand { display: flex; align-items: center; gap: 0.85rem; text-decoration: none; color: var(--text-main); }
        .welcome-brand-logo {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: color-mix(in srgb, var(--surface) 90%, #0f141b 10%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--inset-shadow);
            padding: 5px;
            flex-shrink: 0;
        }
        .welcome-brand-logo img {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            object-fit: contain;
            object-position: 62% 50%;
            display: block;
        }
        .welcome-brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
            background: linear-gradient(to right, #a67c00, #bf953f, #fcf6ba, #b38728, #fdffcc, #8a6400);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: metallicShift 6s linear infinite;
        }
        .welcome-brand-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.35;
            min-height: 1.05rem;
            max-width: min(44vw, 34rem);
            display: inline-flex;
            align-items: center;
            gap: 0.1rem;
        }

        .welcome-tagline-type {
            display: inline;
        }

        .welcome-tagline-caret {
            display: inline-block;
            width: 0.55ch;
            color: color-mix(in srgb, var(--primary) 78%, #ffffff);
            animation: taglineCaretBlink 0.9s steps(1, end) infinite;
            transform: translateY(-0.03em);
        }

        @keyframes taglineCaretBlink {
            0%,
            50% { opacity: 1; }
            51%,
            100% { opacity: 0; }
        }

        .welcome-nav-links {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 1rem;
        }
        .welcome-nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-weight: 600;
            padding: 0.6rem 1rem;
            border-radius: 999px;
            background: var(--surface);
            box-shadow: 6px 6px 12px var(--shadow-dark), -6px -6px 12px var(--shadow-light);
            transition: all 0.25s ease;
        }
        .welcome-nav-links a:hover {
            color: var(--primary);
            background: var(--surface);
            box-shadow: var(--inset-shadow);
        }

        .welcome-actions { display: flex; align-items: center; gap: 0.9rem; }
        .notification-bell-btn {
            border: none;
            color: var(--text-main);
            cursor: pointer;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--surface);
            box-shadow: 6px 6px 12px var(--shadow-dark), -6px -6px 12px var(--shadow-light);
            display: grid;
            place-items: center;
            position: relative;
            transition: transform 0.25s ease;
        }
        .notification-bell-btn:hover { transform: translateY(-1px); }
        .notification-bell-btn.has-new svg { animation: bellRing 1.2s ease-in-out infinite; transform-origin: top center; }
        .notification-bell-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 20px;
            height: 20px;
            border-radius: 999px;
            padding: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ef4444;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            box-shadow: 0 6px 12px rgba(0,0,0,0.25);
        }
        @keyframes bellRing {
            0%, 100% { transform: rotate(0deg); }
            15% { transform: rotate(14deg); }
            30% { transform: rotate(-12deg); }
            45% { transform: rotate(8deg); }
            60% { transform: rotate(-6deg); }
            75% { transform: rotate(3deg); }
        }

        .welcome-login {
            background: linear-gradient(145deg, #3a4049, #272c33);
            color: var(--primary);
            padding: 0.6rem 1.4rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 8px 8px 16px var(--shadow-dark), -8px -8px 16px var(--shadow-light);
            transition: all 0.25s ease;
        }
        .welcome-login:hover {
            color: var(--primary-hover);
            box-shadow: 10px 10px 18px var(--shadow-dark), -10px -10px 18px var(--shadow-light);
        }
        .theme-toggle-btn {
            border: none;
            color: var(--text-main);
            cursor: pointer;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--surface);
            box-shadow: 6px 6px 12px var(--shadow-dark), -6px -6px 12px var(--shadow-light);
            display: grid;
            place-items: center;
        }

        .welcome-hero { width: min(99%, 1500px); margin: 3.2rem auto; padding: 0; text-align: center; }

        .welcome-section { width: min(99%, 1500px); margin: 3.3rem auto; padding: 3.2rem 1.5rem; }
        .section-head { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.4rem; }
        .section-head h2 { font-size: 2.35rem; margin: 0 0 0.4rem 0; color: var(--text-main); letter-spacing: 0.2px; }
        .section-head p { margin: 0; color: var(--text-muted); font-size: 1.05rem; }
        .section-badge {
            padding: 0.55rem 1rem;
            border-radius: 999px;
            color: var(--primary);
            background: var(--surface);
            box-shadow: var(--inset-shadow);
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
        }

        .section-actions { display: flex; align-items: center; gap: 0.8rem; }
        .section-toggle {
            border: none;
            padding: 0.55rem 0.95rem;
            border-radius: 999px;
            background: var(--surface);
            color: var(--text-main);
            font-weight: 700;
            cursor: pointer;
            box-shadow: 6px 6px 12px var(--shadow-dark), -6px -6px 12px var(--shadow-light);
            transition: all 0.25s ease;
        }
        .section-toggle:hover { color: var(--primary); box-shadow: var(--inset-shadow); }
        .section-toggle .arrow { display: inline-block; transition: transform 0.25s ease; margin-left: 0.35rem; }
        .section-toggle[aria-expanded='true'] .arrow { transform: rotate(180deg); }

        .welcome-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.7rem; }
        .welcome-grid.is-collapsed .extra-card { display: none; }
        .welcome-card {
            background: var(--surface);
            padding: 2rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(255,255,255,0.02);
            box-shadow: 10px 10px 20px var(--shadow-dark), -10px -10px 20px var(--shadow-light);
            transition: all 0.3s ease;
            text-align: center;
            cursor: pointer;
        }
        .welcome-card:hover {
            transform: translateY(-6px);
            box-shadow: 14px 14px 28px var(--shadow-dark), -12px -12px 26px var(--shadow-light);
        }
        .welcome-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: linear-gradient(145deg, #3b414c, #272c33);
            color: var(--primary);
            box-shadow: var(--inset-shadow);
            font-size: 1.35rem;
            font-weight: bold;
            margin-bottom: 1.2rem;
        }
        .welcome-card h3 { margin: 0 0 0.8rem 0; font-size: 1.28rem; color: var(--text-main); }
        .welcome-card p { margin: 0; color: var(--text-muted); line-height: 1.55; }

        .contact-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.2rem; }
        .contact-link {
            display: flex;
            flex-direction: column;
            padding: 1.35rem;
            background: var(--surface);
            border-radius: 1rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 9px 9px 18px var(--shadow-dark), -9px -9px 18px var(--shadow-light);
            transition: all 0.25s ease;
        }
        .contact-link:hover {
            color: var(--primary);
            box-shadow: var(--inset-shadow);
        }
        .contact-link small { font-size: 0.88rem; font-weight: 500; color: var(--text-muted); margin-top: 0.45rem; }

        .welcome-footer {
            max-width: 1500px;
            margin: 1.4rem auto 3rem;
            padding: 1.15rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            color: var(--text-muted);
            font-size: 0.95rem;
            flex-wrap: wrap;
        }
        .footer-social { display: flex; align-items: center; gap: 0.7rem; }
        .footer-social-link {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            background: var(--surface);
            box-shadow: 6px 6px 12px var(--shadow-dark), -6px -6px 12px var(--shadow-light);
            transition: all 0.25s ease;
            text-decoration: none;
        }
        .footer-social-link:hover { color: var(--primary); box-shadow: var(--inset-shadow); }
        .footer-social-link svg { width: 18px; height: 18px; }
        .footer-meta { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }

        .hero-heritage-wrap {
            position: relative;
            border-radius: 1.6rem;
            padding: 1.1rem;
            background: var(--surface);
            box-shadow: var(--glass-shadow);
        }
        .hero-quote-card {
            position: relative;
            overflow: hidden;
            background: var(--surface-soft);
            padding: 1.9rem;
            border-radius: 1.15rem;
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 1.2rem;
            box-shadow: var(--inset-shadow);
            transition: transform 0.3s ease;
        }
        .hero-quote-card:hover { transform: translateY(-3px); }
        .hero-quote-card::before {
            content: '“';
            position: absolute;
            top: -46px;
            left: 8px;
            font-size: 178px;
            font-weight: 700;
            color: rgba(197, 160, 89, 0.18);
            line-height: 1;
            pointer-events: none;
        }
        .hero-quote-img {
            width: 102px;
            height: 102px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(197, 160, 89, 0.45);
            box-shadow: 0 0 0 5px rgba(0,0,0,0.22), 8px 8px 16px var(--shadow-dark);
            flex-shrink: 0;
            position: relative;
            z-index: 2;
        }
        .hero-quote-content { position: relative; z-index: 2; text-align: left; }
        .hero-quote-text {
            font-size: clamp(1.05rem, 2vw, 1.46rem);
            color: var(--text-main);
            line-height: 1.62;
            margin: 0 0 0.45rem;
            font-style: italic;
            font-weight: 500;
        }
        .hero-quote-author { color: var(--primary); font-weight: 700; font-size: 1.03rem; }
        .hero-desc-card {
            margin-top: 0.95rem;
            padding: 1.05rem 1.4rem;
            border-radius: 0.9rem;
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-main);
            box-shadow: var(--inset-shadow);
            background: var(--surface-soft);
        }

        @keyframes heartbeatGlow {
            0% { transform: scale(1); box-shadow: 0 10px 25px rgba(0,0,0,0.35), 0 0 0 0 rgba(168,138,240,.3); }
            18% { transform: scale(1.06); box-shadow: 0 14px 30px rgba(0,0,0,0.4), 0 0 0 14px rgba(168,138,240,.08); }
            34% { transform: scale(1); box-shadow: 0 10px 25px rgba(0,0,0,0.35), 0 0 0 0 rgba(168,138,240,0); }
            52% { transform: scale(1.04); }
            70%, 100% { transform: scale(1); }
        }
        @keyframes botFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-2px); }
        }

        .chatbot-fab {
            position: fixed;
            bottom: 1.7rem;
            right: 1.7rem;
            width: 92px;
            height: 92px;
            padding: 0;
            border-radius: 50%;
            background: linear-gradient(145deg, #3a4049, #262b32);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 12px 12px 24px var(--shadow-dark), -10px -10px 22px var(--shadow-light);
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.25s ease;
            border: none;
            animation: heartbeatGlow 1.8s infinite;
            transform-style: preserve-3d;
            perspective: 900px;
            overflow: visible;
        }
        .chatbot-fab:hover { transform: scale(1.08); }
        .chatbot-fab-bot {
            position: relative;
            width: 58px;
            height: 58px;
            animation: botFloat 2.6s ease-in-out infinite;
            transform: translateY(4px);
            transform-style: preserve-3d;
            overflow: visible;
        }
        .bot-head {
            position: absolute;
            top: 6px;
            left: 8px;
            width: 40px;
            height: 23px;
            border-radius: 18px 18px 14px 14px;
            background: radial-gradient(circle at 30% 20%, #4a65ba, #243a7a 70%);
            box-shadow: inset 0 -4px 7px rgba(0,0,0,0.25), 0 4px 8px rgba(0,0,0,0.22);
            transform: none;
            z-index: 2;
        }
        .bot-antenna {
            position: absolute;
            top: -6px;
            left: 50%;
            width: 2px;
            height: 7px;
            background: #93b6ff;
            transform: translateX(-50%);
            box-shadow: 0 0 8px rgba(147, 182, 255, 0.7);
            z-index: 3;
        }
        .bot-antenna::after {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #6ea2ff;
            box-shadow: 0 0 8px rgba(110,162,255,.9);
        }
        .bot-eye { position: absolute; top: 7px; width: 7px; height: 7px; border-radius: 50%; background: #f8fbff; overflow: hidden; }
        .bot-eye.left { left: 10px; }
        .bot-eye.right { right: 10px; }
        .bot-pupil { position: absolute; top: 2px; left: 2px; width: 3px; height: 3px; border-radius: 50%; background: #14335d; transform: translate(var(--pupil-x, 0px), var(--pupil-y, 0px)); }
        .bot-mouth { position: absolute; bottom: 4px; left: 50%; width: 10px; height: 3px; border-radius: 999px; transform: translateX(-50%); background: rgba(255,255,255,.86); }
        .bot-body {
            position: absolute;
            top: 28px;
            left: 12px;
            width: 32px;
            height: 24px;
            border-radius: 14px 14px 16px 16px;
            background: radial-gradient(circle at 35% 20%, #3d59ab, #243a7a 75%);
            box-shadow: inset 0 -5px 8px rgba(0,0,0,.3), 0 4px 9px rgba(0,0,0,.24);
            z-index: 1;
        }
        .bot-core {
            position: absolute;
            top: 7px;
            left: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            transform: translateX(-50%);
            background: #66b2ff;
            box-shadow: 0 0 8px rgba(102,178,255,.9), 0 0 0 2px rgba(9,31,70,.4);
        }

        .chatbot-window {
            position: fixed;
            bottom: 6.6rem;
            right: 1.7rem;
            width: 350px;
            max-width: calc(100vw - 2rem);
            background: var(--surface);
            border-radius: 1.2rem;
            box-shadow: var(--glass-shadow);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform-origin: bottom right;
            transform: scale(0.8);
            opacity: 0;
            pointer-events: none;
            transition: all 0.28s ease;
        }
        .notice-window {
            position: fixed;
            top: 6.6rem;
            right: 1.7rem;
            width: 370px;
            max-width: calc(100vw - 2rem);
            background: var(--surface);
            border-radius: 1.2rem;
            box-shadow: var(--glass-shadow);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform-origin: top right;
            transform: scale(0.85);
            opacity: 0;
            pointer-events: none;
            transition: all 0.28s ease;
        }
        .notice-window.open { transform: scale(1); opacity: 1; pointer-events: all; }
        .notice-header {
            padding: 1rem 1.2rem;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--surface-soft);
            box-shadow: var(--inset-shadow);
        }
        .notice-header h3 { margin: 0; font-size: 1.02rem; font-weight: 700; }
        .notice-body {
            max-height: 360px;
            overflow-y: auto;
            padding: 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }
        .notice-item {
            background: var(--surface-soft);
            box-shadow: var(--inset-shadow);
            border-radius: 0.9rem;
            padding: 0.8rem 0.85rem;
        }
        .notice-item h4 { margin: 0 0 0.35rem; font-size: 0.95rem; color: var(--text-main); }
        .notice-item p { margin: 0; color: var(--text-muted); font-size: 0.88rem; line-height: 1.45; }
        .notice-meta {
            margin-top: 0.55rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.4rem;
        }
        .notice-time { color: var(--text-muted); font-size: 0.77rem; }
        .notice-download {
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            border: none;
            text-decoration: none;
            color: #1f1d16;
            font-size: 0.78rem;
            font-weight: 700;
            background: linear-gradient(145deg, #d7ba7a, #c5a059);
            box-shadow: 5px 5px 10px var(--shadow-dark), -3px -3px 8px rgba(255,255,255,0.18);
            white-space: nowrap;
        }
        .notice-empty {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            padding: 1.4rem 1rem;
            box-shadow: var(--inset-shadow);
            border-radius: 0.9rem;
            background: var(--surface-soft);
        }
        .chatbot-window.open { transform: scale(1); opacity: 1; pointer-events: all; }
        .chatbot-header {
            padding: 1rem 1.4rem;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--surface-soft);
            box-shadow: var(--inset-shadow);
        }
        .chatbot-header h3 { margin: 0; font-size: 1.05rem; font-weight: 700; display:flex; align-items:center; gap:0.5rem; }
        .close-chat-btn {
            background: var(--surface);
            border: none;
            color: var(--text-main);
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            box-shadow: 4px 4px 8px var(--shadow-dark), -4px -4px 8px var(--shadow-light);
        }

        .chatbot-body {
            height: 300px;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }
        .chatbot-body::-webkit-scrollbar { width: 4px; }
        .chatbot-body::-webkit-scrollbar-thumb { background: #4a525f; border-radius: 4px; }
        .chatbot-msg {
            max-width: 85%;
            padding: 0.72rem 0.95rem;
            border-radius: 0.95rem;
            font-size: 0.93rem;
            line-height: 1.42;
            animation: fadeInUp 0.28s ease forwards;
        }
        .msg-bot {
            color: var(--text-main);
            align-self: flex-start;
            box-shadow: var(--inset-shadow);
            background: var(--surface-soft);
            border-bottom-left-radius: 0.3rem;
        }
        .msg-user {
            color: #201f1a;
            align-self: flex-end;
            background: linear-gradient(145deg, #d7ba7a, #c5a059);
            border-bottom-right-radius: 0.3rem;
        }

        .chatbot-footer {
            padding: 0.9rem;
            display: flex;
            gap: 0.55rem;
            background: var(--surface-soft);
            box-shadow: var(--inset-shadow);
        }
        .chatbot-input {
            flex: 1;
            padding: 0.8rem 1rem;
            border-radius: 999px;
            border: none;
            background: var(--surface);
            color: var(--text-main);
            font-family: inherit;
            outline: none;
            box-shadow: var(--inset-shadow);
        }
        .chatbot-send {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            color: #1f1d16;
            border: none;
            display: grid;
            place-items: center;
            cursor: pointer;
            flex-shrink: 0;
            background: linear-gradient(145deg, #d7ba7a, #c5a059);
            box-shadow: 6px 6px 12px var(--shadow-dark), -4px -4px 10px rgba(255,255,255,0.18);
        }

        @media (max-width: 992px) {
            .hero-heritage-wrap { padding: 0.85rem; }
            .hero-quote-card {
                grid-template-columns: 82px 1fr;
                text-align: left;
                gap: 0.85rem;
                padding: 1.1rem;
            }
            .hero-quote-content { text-align: left; }
            .hero-quote-img {
                width: 78px;
                height: 78px;
            }
            .section-head { flex-direction: column; align-items: flex-start; gap: 0.9rem; }
            .welcome-nav-links { display: none; }
            .welcome-brand-sub {
                max-width: min(56vw, 17rem);
                min-height: 2rem;
            }
            .welcome-shell::before,
            .welcome-shell::after { display: none; }
            .welcome-nav,
            .welcome-hero,
            .welcome-section,
            .welcome-footer { width: calc(100% - 1rem); }
            .welcome-footer,
            .footer-meta { justify-content: center; text-align: center; }
            .chatbot-fab {
                width: 76px;
                height: 76px;
                right: 1rem;
                bottom: 1rem;
            }
            .chatbot-window {
                right: 1rem;
                bottom: 5.8rem;
                max-width: calc(100vw - 1rem);
            }
            .notice-window {
                right: 1rem;
                top: 5.8rem;
                max-width: calc(100vw - 1rem);
            }
        }

        /* Very small screens: keep navbar compact and avoid logo/tagline distortion */
        @media (max-width: 576px) {
            .welcome-brand-logo {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                padding: 4px;
            }
            .welcome-brand-text {
                font-size: 1.1rem;
            }
            /* Hide animated tagline on extra-small view so layout stays clean */
            .welcome-brand-sub {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .animate-up,
            .welcome-shell::before,
            .welcome-shell::after,
            .welcome-card,
            .contact-link,
            .welcome-login,
            .chatbot-fab,
            .chatbot-fab-bot {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>

    <div class="welcome-shell">
        <nav class="glass-card welcome-nav animate-up">
            <a href="#home" class="welcome-brand">
                <span class="welcome-brand-logo">
                    <img src="{{ asset('logo.png') }}" alt="Alor Disha" onerror="this.style.display='none'">
                </span>
                <span>
                    <p class="welcome-brand-text">Alor Disha</p>
                    <p class="welcome-brand-sub" aria-live="polite">
                        <span class="welcome-tagline-type" id="welcomeTaglineType"></span>
                        <span class="welcome-tagline-caret" aria-hidden="true">|</span>
                    </p>
                </span>
            </a>

            <div class="welcome-nav-links">
                <a href="#home">Home</a>
                <a href="#departments">Departments</a>
                <a href="#mentors">Mentors</a>
                <a href="#contact">Contact</a>
            </div>

            <div class="welcome-actions">
                <button type="button" class="notification-bell-btn" id="noticeToggle" aria-label="Open notifications">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.17V11a6 6 0 0 0-5-5.91V4a1 1 0 0 0-2 0v1.09A6 6 0 0 0 6 11v3.17c0 .53-.21 1.04-.59 1.42L4 17h5"/>
                        <path d="M9 17a3 3 0 0 0 6 0"/>
                    </svg>
                    <span class="notification-bell-badge" id="noticeCountBadge" style="display:none;">0</span>
                </button>

                <button type="button" class="theme-toggle-btn" data-theme-toggle aria-label="Toggle theme">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/>
                        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                </button>

                @if ($isLoggedIn)
                    <a href="{{ $dashboardRoute }}" class="welcome-login">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="welcome-login">Login</a>
                @endif
            </div>
        </nav>

        <div class="notice-window" id="noticeWindow">
            <div class="notice-header">
                <h3>🔔 Notifications</h3>
                <button class="close-chat-btn" id="noticeClose" aria-label="Close Notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="notice-body" id="noticeBody">
                @forelse ($welcomeNotices as $notice)
                    <article class="notice-item">
                        <h4>{{ $notice->title }}</h4>
                        @if($notice->message)
                            <p>{{ $notice->message }}</p>
                        @endif
                        <div class="notice-meta">
                            <span class="notice-time">{{ $notice->created_at->diffForHumans() }}</span>
                            @if($notice->media_path)
                                <a class="notice-download" href="{{ route('notices.download', $notice) }}">Download</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="notice-empty">No active notifications right now.</div>
                @endforelse
            </div>
        </div>

        <section id="home" class="welcome-hero animate-up delay-1">
            <div style="width: 100%; margin: 0 auto;">

                <div class="hero-heritage-wrap">
                    <div class="hero-quote-card">
                        <img src="{{ $randomQuote['image'] }}" alt="{{ $randomQuote['author'] }}" class="hero-quote-img" loading="lazy" referrerpolicy="no-referrer">
                        <div class="hero-quote-content">
                            <p class="hero-quote-text">"{{ $randomQuote['text'] }}"</p>
                            <span class="hero-quote-author">— {{ $randomQuote['author'] }}</span>
                        </div>
                    </div>

                    <div class="hero-desc-card">
                        'Alor Disha' — Empowering the Sundarbans through modern education, culture, and social service.
                    </div>
                </div>
            </div>
        </section>

        <section id="departments" class="glass-card welcome-section animate-up delay-2">
            <div class="section-head">
                <div>
                    <h2>Departments</h2>
                    <p>Skill-focused programs designed for practical growth and creativity.</p>
                </div>
                <div class="section-actions">
                    <span class="section-badge">Core Programs</span>
                    <button type="button" class="section-toggle" data-toggle-target="departmentsGrid" aria-expanded="false">
                        <span class="label">See More</span>
                        <span class="arrow">▼</span>
                    </button>
                </div>
            </div>

            <div class="welcome-grid is-collapsed" id="departmentsGrid">
                <article class="welcome-card">
                    <span class="welcome-card-icon">💻</span>
                    <h3>Computer</h3>
                    <p>Digital literacy, coding, and productivity tools.</p>
                </article>

                <article class="welcome-card">
                    <span class="welcome-card-icon">🧘</span>
                    <h3>Yoga</h3>
                    <p>Body-mind balance through guided practice.</p>
                </article>

                <article class="welcome-card">
                    <span class="welcome-card-icon">🎵</span>
                    <h3>Music</h3>
                    <p>Classical and modern vocal-instrument training.</p>
                </article>

                <article class="welcome-card">
                    <span class="welcome-card-icon">💃</span>
                    <h3>Dance</h3>
                    <p>Technique, expression, and stage confidence.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">🎨</span>
                    <h3>Fine Art</h3>
                    <p>Drawing, color theory, and creative expression.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">🎙️</span>
                    <h3>Recitation</h3>
                    <p>Voice training, rhythm, and expressive delivery.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">🪘</span>
                    <h3>Tabla</h3>
                    <p>Classical rhythms, technique, and performance.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">🥁</span>
                    <h3>Sreekhol</h3>
                    <p>Traditional beats, timing, and stage practice.</p>
                </article>
            </div>
        </section>

        <section id="mentors" class="glass-card welcome-section animate-up delay-3">
            <div class="section-head">
                <div>
                    <h2>Mentors</h2>
                    <p>Experienced teachers guiding each learner with personal attention.</p>
                </div>
                <div class="section-actions">
                    <span class="section-badge">Expert Faculty</span>
                    <button type="button" class="section-toggle" data-toggle-target="mentorsGrid" aria-expanded="false">
                        <span class="label">See More</span>
                        <span class="arrow">▼</span>
                    </button>
                </div>
            </div>

            <div class="welcome-grid is-collapsed" id="mentorsGrid">
                <article class="welcome-card">
                    <span class="welcome-card-icon">CF</span>
                    <h3>Computer Faculty</h3>
                    <p>Industry-ready practical learning methodology.</p>
                </article>

                <article class="welcome-card">
                    <span class="welcome-card-icon">YT</span>
                    <h3>Yoga Trainer</h3>
                    <p>Holistic posture, breathing, and mindfulness support.</p>
                </article>

                <article class="welcome-card">
                    <span class="welcome-card-icon">MM</span>
                    <h3>Music Mentor</h3>
                    <p>Strong fundamentals with performance practice.</p>
                </article>

                <article class="welcome-card">
                    <span class="welcome-card-icon">DC</span>
                    <h3>Dance Coach</h3>
                    <p>Creative rhythm sessions for all age groups.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">FA</span>
                    <h3>Fine Art Mentor</h3>
                    <p>Guidance on sketching, painting, and composition.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">RC</span>
                    <h3>Recitation Coach</h3>
                    <p>Voice modulation, clarity, and stage confidence.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">TB</span>
                    <h3>Tabla Instructor</h3>
                    <p>Rhythm control, hand technique, and tala basics.</p>
                </article>

                <article class="welcome-card extra-card">
                    <span class="welcome-card-icon">SK</span>
                    <h3>Sreekhol Mentor</h3>
                    <p>Traditional beats, timing, and ensemble practice.</p>
                </article>
            </div>
        </section>

        <section id="contact" class="glass-card welcome-section animate-up delay-3">
            <div class="section-head">
                <div>
                    <h2>Contact</h2>
                    <p>Reach out for admissions, schedules, and program details.</p>
                    <p><strong>Address - Sridharnagar, Patharpratima, South 24 Pgs, WB - 743371</strong></p>
                </div>
                <span class="section-badge">Support Desk</span>
            </div>

            <div class="contact-grid">
                <a href="tel:+917407917787" class="contact-link">
                    Call Us
                    <small>+91 74079 17787</small>
                </a>

                <a href="mailto:pgiri.help@gmail.com" class="contact-link">
                    Email Us
                    <small>pgiri.help@gmail.com</small>
                </a>

                <a href="https://wa.me/917407917787" target="_blank" class="contact-link">
                    WhatsApp
                    <small>Chat on WhatsApp: +91 74079 17787</small>
                </a>

                <a href="https://www.google.com/maps/place/ALOR+DISHA/@21.7423553,88.4307771,17z/data=!3m1!4b1!4m6!3m5!1s0x3a03b78dd2beefb9:0x985222d45e67d90d!8m2!3d21.7423553!4d88.4307771!16s%2Fg%2F11rwr4lct7?entry=ttu&g_ep=EgoyMDI2MDIyNS4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="contact-link">
                    Location
                    <small>Open live location in Google Maps</small>
                </a>
            </div>
        </section>

        <footer class="glass-card welcome-footer">
            <div class="footer-social">
                <a href="#" class="footer-social-link" aria-label="YouTube" title="YouTube">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M23.5 6.2a3.02 3.02 0 0 0-2.12-2.14C19.52 3.5 12 3.5 12 3.5s-7.52 0-9.38.56A3.02 3.02 0 0 0 .5 6.2 31.3 31.3 0 0 0 0 12a31.3 31.3 0 0 0 .5 5.8 3.02 3.02 0 0 0 2.12 2.14c1.86.56 9.38.56 9.38.56s7.52 0 9.38-.56a3.02 3.02 0 0 0 2.12-2.14A31.3 31.3 0 0 0 24 12a31.3 31.3 0 0 0-.5-5.8ZM9.6 15.74V8.26L15.84 12 9.6 15.74Z" />
                    </svg>
                </a>
                <a href="#" class="footer-social-link" aria-label="Facebook" title="Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M13.5 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.6 1.7-1.6h1.5V3.8c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4V10H8v3h2.5v8h3Z" />
                    </svg>
                </a>
                <a href="#" class="footer-social-link" aria-label="Telegram" title="Telegram">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M21.9 4.3a1 1 0 0 0-1-.14L2.6 11.2a1 1 0 0 0 .05 1.88l4.9 1.67 1.83 5.6a1 1 0 0 0 1.8.25l2.63-3.57 4.66 3.4a1 1 0 0 0 1.57-.63l2.9-14.5a1 1 0 0 0-.37-1.02ZM9.1 14.2l8.7-6.34-7.25 7.95-.3 2.31-1.15-3.92Z" />
                    </svg>
                </a>
            </div>
            <div class="footer-meta">
                <span>© {{ date('Y') }} Alor Disha. All rights reserved.</span>
                <a href="{{ route('privacy.policy') }}" style="color: var(--primary); text-decoration:none; font-weight:600;">Privacy & Policy</a>
            </div>
        </footer>

        <!-- Chatbot UI -->
        <button class="chatbot-fab" id="chatbotToggle" aria-label="Open Chat">
            <span class="chatbot-fab-bot" aria-hidden="true">
                <span class="bot-head" id="botHead">
                    <span class="bot-antenna"></span>
                    <span class="bot-eye left"><span class="bot-pupil"></span></span>
                    <span class="bot-eye right"><span class="bot-pupil"></span></span>
                    <span class="bot-mouth"></span>
                </span>
                <span class="bot-body">
                    <span class="bot-core"></span>
                </span>
            </span>
        </button>

        <div class="chatbot-window" id="chatbotWindow">
            <div class="chatbot-header">
                <h3><span style="font-size:1.4rem">🙂</span> Alo Helpdesk</h3>
                <button class="close-chat-btn" id="chatbotClose" aria-label="Close Chat">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="chatbot-body" id="chatbotBody">
                <div class="chatbot-msg msg-bot">
                    Hello! I'm Alo. How can I help you today with your admission or courses?
                </div>
            </div>
            <form class="chatbot-footer" id="chatbotForm">
                <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type a message..." required autocomplete="off">
                <button type="submit" class="chatbot-send" aria-label="Send">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </button>
            </form>
        </div>

        <script id="welcomeNoticeData" type="application/json">{!! $welcomeNoticePayload->toJson() !!}</script>
        <script id="welcomeConfigData" type="application/json">{!! json_encode(['chatEndpoint' => route('chatbot.ask'), 'csrfToken' => csrf_token()]) !!}</script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggleBtn = document.getElementById('chatbotToggle');
                const closeBtn = document.getElementById('chatbotClose');
                const chatWindow = document.getElementById('chatbotWindow');
                const chatForm = document.getElementById('chatbotForm');
                const chatInput = document.getElementById('chatbotInput');
                const chatBody = document.getElementById('chatbotBody');
                const botHead = document.getElementById('botHead');
                const sectionToggles = document.querySelectorAll('.section-toggle');
                const noticeToggleBtn = document.getElementById('noticeToggle');
                const noticeCloseBtn = document.getElementById('noticeClose');
                const noticeWindow = document.getElementById('noticeWindow');
                const noticeBadge = document.getElementById('noticeCountBadge');
                const noticeDataEl = document.getElementById('welcomeNoticeData');
                const configDataEl = document.getElementById('welcomeConfigData');
                const noticeData = noticeDataEl ? JSON.parse(noticeDataEl.textContent || '[]') : [];
                const configData = configDataEl ? JSON.parse(configDataEl.textContent || '{}') : {};
                const taglineEl = document.getElementById('welcomeTaglineType');
                const chatEndpoint = configData.chatEndpoint || '';
                const csrfToken = configData.csrfToken || '';

                if (taglineEl) {
                    const taglines = [
                        'ज्ञानविहीनस्य मुक्तिर्न भवति जन्मशतेनापि।',
                        'Without knowledge, liberation is not attained, even in a hundred births.',
                        'জ্ঞান ব্যতীত শত জন্মেও মুক্তি সম্ভব নয়।'
                    ];

                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        taglineEl.textContent = taglines[0];
                    } else {
                        let lineIndex = 0;
                        let charIndex = 0;
                        let isDeleting = false;

                        const TYPE_SPEED = 46;
                        const DELETE_SPEED = 26;
                        const HOLD_DELAY = 1700;

                        const stepTypewriter = () => {
                            const phraseChars = Array.from(taglines[lineIndex] || '');

                            if (!isDeleting) {
                                charIndex = Math.min(phraseChars.length, charIndex + 1);
                            } else {
                                charIndex = Math.max(0, charIndex - 1);
                            }

                            taglineEl.textContent = phraseChars.slice(0, charIndex).join('');

                            if (!isDeleting && charIndex === phraseChars.length) {
                                isDeleting = true;
                                window.setTimeout(stepTypewriter, HOLD_DELAY);
                                return;
                            }

                            if (isDeleting && charIndex === 0) {
                                isDeleting = false;
                                lineIndex = (lineIndex + 1) % taglines.length;
                            }

                            window.setTimeout(stepTypewriter, isDeleting ? DELETE_SPEED : TYPE_SPEED);
                        };

                        stepTypewriter();
                    }
                }

                const toggleChat = () => {
                    chatWindow.classList.toggle('open');
                    if (chatWindow.classList.contains('open')) {
                        chatInput.focus();
                    }
                };

                if(toggleBtn) toggleBtn.addEventListener('click', toggleChat);
                if(closeBtn) closeBtn.addEventListener('click', toggleChat);

                const updateNoticeBadge = () => {
                    if (!noticeToggleBtn || !noticeBadge) return;

                    const lastSeen = localStorage.getItem('welcome_notices_seen_at');
                    let unseenCount = noticeData.length;

                    if (lastSeen) {
                        unseenCount = noticeData.filter(item => item.created_at && new Date(item.created_at) > new Date(lastSeen)).length;
                    }

                    if (unseenCount > 0) {
                        noticeBadge.style.display = 'inline-flex';
                        noticeBadge.textContent = unseenCount > 99 ? '99+' : String(unseenCount);
                        noticeToggleBtn.classList.add('has-new');
                    } else {
                        noticeBadge.style.display = 'none';
                        noticeToggleBtn.classList.remove('has-new');
                    }
                };

                const toggleNotice = () => {
                    if (!noticeWindow) return;

                    noticeWindow.classList.toggle('open');
                    if (noticeWindow.classList.contains('open')) {
                        localStorage.setItem('welcome_notices_seen_at', new Date().toISOString());
                        updateNoticeBadge();
                    }
                };

                if(noticeToggleBtn) noticeToggleBtn.addEventListener('click', toggleNotice);
                if(noticeCloseBtn) noticeCloseBtn.addEventListener('click', toggleNotice);

                updateNoticeBadge();

                sectionToggles.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const targetId = btn.getAttribute('data-toggle-target');
                        const targetGrid = document.getElementById(targetId);
                        const label = btn.querySelector('.label');
                        if (!targetGrid || !label) return;
                        const isExpanded = btn.getAttribute('aria-expanded') === 'true';
                        btn.setAttribute('aria-expanded', String(!isExpanded));
                        targetGrid.classList.toggle('is-collapsed');
                        label.textContent = isExpanded ? 'See More' : 'See Less';
                    });
                });

                const followCursor = (event) => {
                    if (!toggleBtn || !botHead) return;
                    const rect = toggleBtn.getBoundingClientRect();
                    const centerX = rect.left + rect.width / 2;
                    const centerY = rect.top + rect.height / 2;
                    const dx = event.clientX - centerX;
                    const dy = event.clientY - centerY;
                    const max = 26;
                    const pupilX = Math.max(-0.7, Math.min(0.7, dx / 44));
                    const pupilY = Math.max(-0.6, Math.min(0.6, dy / 44));
                    toggleBtn.style.setProperty('--pupil-x', `${pupilX}px`);
                    toggleBtn.style.setProperty('--pupil-y', `${pupilY}px`);
                };

                document.addEventListener('mousemove', followCursor);

                if (toggleBtn) {
                    toggleBtn.addEventListener('mouseleave', () => {
                        toggleBtn.style.setProperty('--pupil-x', '0px');
                        toggleBtn.style.setProperty('--pupil-y', '0px');
                    });
                }

                if(chatForm) {
                    chatForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const msgText = chatInput.value.trim();
                        if (!msgText) return;

                        const userMsgEl = document.createElement('div');
                        userMsgEl.className = 'chatbot-msg msg-user';
                        userMsgEl.textContent = msgText;
                        chatBody.appendChild(userMsgEl);

                        chatInput.value = '';
                        chatBody.scrollTop = chatBody.scrollHeight;

                        const thinkingEl = document.createElement('div');
                        thinkingEl.className = 'chatbot-msg msg-bot';
                        thinkingEl.textContent = 'Typing...';
                        chatBody.appendChild(thinkingEl);
                        chatBody.scrollTop = chatBody.scrollHeight;

                        try {
                            const response = await fetch(chatEndpoint, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ message: msgText })
                            });

                            const payload = await response.json();
                            const botReply = (payload && typeof payload.reply === 'string' && payload.reply.trim() !== '')
                                ? payload.reply.trim()
                                : 'দুঃখিত, এই মুহূর্তে উত্তর দিতে পারছি না।';

                            const botMsgEl = document.createElement('div');
                            botMsgEl.className = 'chatbot-msg msg-bot';
                            botMsgEl.textContent = botReply;

                            thinkingEl.remove();
                            chatBody.appendChild(botMsgEl);
                            chatBody.scrollTop = chatBody.scrollHeight;
                        } catch (err) {
                            const botMsgEl = document.createElement('div');
                            botMsgEl.className = 'chatbot-msg msg-bot';
                            botMsgEl.textContent = 'নেটওয়ার্ক সমস্যা হয়েছে। একটু পর আবার চেষ্টা করুন।';

                            thinkingEl.remove();
                            chatBody.appendChild(botMsgEl);
                            chatBody.scrollTop = chatBody.scrollHeight;
                        }
                    });
                }
            });
        </script>
    </div>

@endsection
