@extends('layouts.app')

@section('title', 'Login | Alor Disha')

@section('content')
    <style>
        :root {
            --signin-accent: #0f8d55;
            --signin-accent-deep: #0c6f45;
            --signin-warm: #f3a24d;
        }

        .signin-shell {
            min-height: 100vh;
            padding: 1.2rem;
            background:
                radial-gradient(circle at 10% 0%, rgba(26, 146, 94, 0.2), transparent 46%),
                radial-gradient(circle at 100% 100%, rgba(243, 162, 77, 0.2), transparent 44%),
                linear-gradient(145deg, #f6f2e9, #e9f5ef 62%, #f6efe3);
            display: grid;
            place-items: center;
        }

        .signin-grid {
            width: min(1080px, 100%);
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(14, 74, 50, 0.12);
            box-shadow: 0 30px 68px rgba(9, 46, 30, 0.2);
            background: rgba(255, 255, 255, 0.76);
            backdrop-filter: blur(14px);
            display: grid;
            grid-template-columns: 1.12fr 1fr;
        }

        .signin-hero {
            padding: 2.4rem;
            background:
                radial-gradient(circle at 85% 20%, rgba(95, 215, 162, 0.24), transparent 40%),
                radial-gradient(circle at 0% 100%, rgba(243, 162, 77, 0.2), transparent 44%),
                linear-gradient(150deg, #0a5134, #0f7a4b 55%, #145e3f);
            color: #e8fff2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .signin-hero::after {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            right: -140px;
            bottom: -160px;
            border: 1px solid rgba(202, 255, 226, 0.2);
            background: rgba(202, 255, 226, 0.08);
            pointer-events: none;
        }

        .signin-hero-top {
            position: relative;
            z-index: 1;
        }

        .signin-hero-top h1 {
            font-size: clamp(1.85rem, 3vw, 2.45rem);
            margin: 0.55rem 0 0.9rem;
            line-height: 1.2;
            font-weight: 800;
            max-width: 16ch;
        }

        .signin-hero-logo {
            width: 92px;
            height: 92px;
            margin-bottom: 0.55rem;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.2));
        }

        .signin-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #c2f6da;
            font-weight: 700;
        }

        .signin-hero p {
            color: #d8f6e8;
            line-height: 1.7;
            margin: 0;
            max-width: 42ch;
        }

        .signin-points {
            margin: 1.25rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.72rem;
        }

        .signin-points li {
            font-size: 0.92rem;
            color: #d6f3e5;
            padding: 0.58rem 0.82rem;
            border: 1px solid rgba(198, 251, 223, 0.2);
            background: rgba(209, 252, 228, 0.1);
            border-radius: 999px;
            width: fit-content;
        }

        .signin-message {
            margin-top: 1.2rem;
            max-width: 42ch;
            padding: 0.95rem 1rem 2.35rem;
            border-radius: 14px;
            background: rgba(205, 252, 227, 0.09);
            border: 1px solid rgba(205, 252, 227, 0.22);
            color: #dff9eb;
            line-height: 1.75;
            font-size: 0.95rem;
            position: relative;
        }

        .signin-typewriter-wrap {
            min-height: 56px;
            display: flex;
            align-items: flex-start;
        }

        .signin-typewriter-line {
            margin: 0;
            display: inline-block;
            line-height: 1.7;
        }

        .signin-typewriter-line::after {
            content: '|';
            margin-left: 2px;
            display: inline-block;
            animation: signinBlink 1s steps(1, end) infinite;
        }

        @keyframes signinBlink {
            0%,
            49% {
                opacity: 1;
            }
            50%,
            100% {
                opacity: 0;
            }
        }

        .signin-sign {
            margin: 0;
            color: #c6f6de;
            font-weight: 700;
            letter-spacing: 0.02em;
            position: absolute;
            right: 0.95rem;
            bottom: 0.72rem;
        }

        .signin-home-link {
            display: inline-flex;
            margin-top: 1.25rem;
            color: #d7ffe9;
            text-decoration: none;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .signin-panel {
            padding: 2.25rem;
            background: rgba(255, 255, 255, 0.84);
        }

        .signin-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.15rem;
        }

        .signin-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            padding: 0.2rem;
            background: rgba(18, 125, 77, 0.11);
            border: 1px solid rgba(18, 125, 77, 0.18);
        }

        .signin-brand h2 {
            margin: 0;
            font-size: 1.1rem;
            color: #083923;
        }

        .signin-brand span {
            color: #56806d;
            font-size: 0.82rem;
        }

        .signin-title {
            margin: 0;
            font-size: 1.72rem;
            color: #0d3e28;
            font-weight: 800;
        }

        .signin-sub {
            margin: 0.4rem 0 1.05rem;
            color: #4d7a66;
            line-height: 1.65;
        }

        .signin-alert {
            margin-bottom: 1rem;
            border-radius: 12px;
            padding: 0.75rem 0.9rem;
            font-size: 0.92rem;
            border: 1px solid;
        }

        .signin-alert.error {
            background: #fff0f0;
            border-color: #f0b4b4;
            color: #8b2d2d;
        }

        .signin-field {
            margin-bottom: 0.95rem;
        }

        .signin-field label {
            display: block;
            font-size: 0.87rem;
            color: #2f624d;
            font-weight: 700;
            margin-bottom: 0.38rem;
        }

        .signin-input-wrap {
            position: relative;
        }

        .signin-field input[type='text'],
        .signin-field input[type='password'] {
            width: 100%;
            border: 1px solid rgba(23, 93, 63, 0.24);
            border-radius: 12px;
            height: 46px;
            padding: 0 0.85rem;
            background: rgba(255, 255, 255, 0.95);
            color: #113f2b;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .signin-field input:focus {
            border-color: var(--signin-accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(15, 141, 85, 0.18);
        }

        .signin-toggle-pass {
            position: absolute;
            right: 0.45rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            padding: 0.35rem;
            cursor: pointer;
            color: #2f654e;
        }

        .signin-toggle-pass svg {
            width: 19px;
            height: 19px;
            stroke: currentColor;
        }

        .signin-remember {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: #356751;
            font-size: 0.88rem;
            margin: 0.3rem 0 1rem;
        }

        .signin-primary,
        .signin-google {
            width: 100%;
            border: 0;
            border-radius: 12px;
            height: 48px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
        }

        .signin-primary {
            background: linear-gradient(135deg, var(--signin-accent-deep), var(--signin-accent));
            color: #f3fff8;
            box-shadow: 0 12px 20px rgba(17, 108, 68, 0.26);
        }

        .signin-divider {
            margin: 1rem 0;
            display: flex;
            align-items: center;
            color: #6f9583;
            font-size: 0.79rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .signin-divider::before,
        .signin-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(23, 93, 63, 0.18);
        }

        .signin-divider::before {
            margin-right: 0.55rem;
        }

        .signin-divider::after {
            margin-left: 0.55rem;
        }

        .signin-google {
            border: 1px solid rgba(23, 93, 63, 0.24);
            background: #ffffff;
            color: #15472f;
        }

        .signin-google svg {
            width: 18px;
            height: 18px;
        }

        .signin-note {
            margin-top: 0.8rem;
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            background: #f8fbf9;
            border: 1px dashed rgba(23, 93, 63, 0.25);
            color: #4e7b68;
            font-size: 0.8rem;
            line-height: 1.55;
        }

        .signin-help {
            margin-top: 0.7rem;
            text-align: right;
        }

        .signin-help a {
            color: #2f6d52;
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 700;
        }

        .signin-help a:hover {
            text-decoration: underline;
        }

        html[data-theme='dark'] .signin-shell {
            background:
                radial-gradient(circle at 12% 0%, rgba(65, 178, 128, 0.22), transparent 46%),
                radial-gradient(circle at 100% 100%, rgba(243, 162, 77, 0.18), transparent 45%),
                linear-gradient(145deg, #091911, #0d2118 52%, #13241d);
        }

        html[data-theme='dark'] .signin-grid {
            border-color: rgba(115, 226, 172, 0.2);
            background: rgba(8, 27, 19, 0.68);
            box-shadow: 0 34px 72px rgba(0, 0, 0, 0.58);
        }

        html[data-theme='dark'] .signin-hero {
            background:
                radial-gradient(circle at 84% 20%, rgba(118, 238, 185, 0.24), transparent 40%),
                radial-gradient(circle at 0% 100%, rgba(243, 162, 77, 0.18), transparent 42%),
                linear-gradient(150deg, #093822, #0b5734 55%, #0f4b31);
        }

        html[data-theme='dark'] .signin-panel {
            background: rgba(8, 24, 17, 0.82);
        }

        html[data-theme='dark'] .signin-brand h2,
        html[data-theme='dark'] .signin-title {
            color: #defbe9;
        }

        html[data-theme='dark'] .signin-brand span,
        html[data-theme='dark'] .signin-sub,
        html[data-theme='dark'] .signin-remember,
        html[data-theme='dark'] .signin-note {
            color: #a4d2bc;
        }

        html[data-theme='dark'] .signin-field label {
            color: #bce5d1;
        }

        html[data-theme='dark'] .signin-field input[type='text'],
        html[data-theme='dark'] .signin-field input[type='password'] {
            background: rgba(8, 34, 23, 0.88);
            border-color: rgba(111, 210, 163, 0.28);
            color: #dbffec;
        }

        html[data-theme='dark'] .signin-field input[type='text']::placeholder,
        html[data-theme='dark'] .signin-field input[type='password']::placeholder {
            color: #78a891;
        }

        html[data-theme='dark'] .signin-toggle-pass {
            color: #9ad6b8;
        }

        html[data-theme='dark'] .signin-divider {
            color: #86b9a0;
        }

        html[data-theme='dark'] .signin-divider::before,
        html[data-theme='dark'] .signin-divider::after {
            background: rgba(105, 197, 154, 0.26);
        }

        html[data-theme='dark'] .signin-google {
            background: rgba(9, 36, 24, 0.78);
            border-color: rgba(111, 210, 163, 0.3);
            color: #d2f6e2;
        }

        html[data-theme='dark'] .signin-note {
            background: rgba(8, 38, 24, 0.55);
            border-color: rgba(111, 210, 163, 0.3);
        }

        html[data-theme='dark'] .signin-help a {
            color: #a8ddc3;
        }

        html[data-theme='dark'] .signin-message {
            background: rgba(178, 247, 210, 0.08);
            border-color: rgba(178, 247, 210, 0.24);
            color: #d8f8e7;
        }

        html[data-theme='dark'] .signin-sign {
            color: #b7ebd1;
        }

        html[data-theme='dark'] .signin-alert.error {
            background: rgba(102, 33, 33, 0.4);
            border-color: rgba(217, 127, 127, 0.46);
            color: #ffcaca;
        }

        @media (max-width: 900px) {
            .signin-grid {
                grid-template-columns: 1fr;
                max-width: 560px;
                border-radius: 20px;
            }

            .signin-hero {
                display: none;
            }

            .signin-panel {
                padding: 1.45rem 1.15rem;
            }

            .signin-shell {
                padding: 0.8rem;
            }
        }
    </style>

    <section class="signin-shell">
        <div class="signin-grid">
            <article class="signin-hero">
                <div class="signin-hero-top">
                    <span class="signin-eyebrow">Secure Access Portal</span>
                    <img src="{{ asset('logo.png') }}" alt="Alor Disha" class="signin-hero-logo">
                    <h1>Your Daily Work, Clear and Simple</h1>
                    <p>Welcome back. This space is built to help you manage classes, attendance, and fee records with confidence and focus.</p>

                    <div class="signin-message">
                        <div class="signin-typewriter-wrap">
                            <span class="signin-typewriter-line" id="signinTypewriter"></span>
                        </div>
                        <p class="signin-sign">By Alor Disha</p>
                    </div>
                </div>

                <div>
                    <a href="{{ url('/') }}" class="signin-home-link">Back to Welcome Page</a>
                </div>
            </article>

            <article class="signin-panel">
                <div class="signin-brand">
                    <img src="{{ asset('logo.png') }}" alt="Alor Disha" class="signin-logo">
                    <span>
                        <h2>Alor Disha</h2>
                        <span>Teacher & Admin Login</span>
                    </span>
                </div>

                <h2 class="signin-title">Sign In</h2>
                <p class="signin-sub">Use your registered phone/email and password. You can also use Google login if your account is already approved.</p>

                @if ($errors->any())
                    <div class="signin-alert error">{{ $errors->first('login') ?? $errors->first() }}</div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf

                    <div class="signin-field">
                        <label for="login">Phone or Email</label>
                        <div class="signin-input-wrap">
                            <input
                                type="text"
                                id="login"
                                name="login"
                                value="{{ old('login') }}"
                                placeholder="Enter phone number or email"
                                required
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <div class="signin-field">
                        <label for="passwordInput">Password</label>
                        <div class="signin-input-wrap">
                            <input
                                type="password"
                                id="passwordInput"
                                name="password"
                                placeholder="Enter password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" id="togglePassword" class="signin-toggle-pass" aria-label="Show or hide password">
                                <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.45801 12C3.73201 7.943 7.52201 5 12 5C16.478 5 20.268 7.943 21.542 12C20.268 16.057 16.478 19 12 19C7.52201 19 3.73201 16.057 2.45801 12Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3432 9 9.00001 10.3431 9.00001 12C9.00001 13.6569 10.3432 15 12 15Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg id="eyeOff" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                                    <path d="M10.732 5.07617C11.15 5.02617 11.573 5.00017 12 5.00017C16.478 5.00017 20.268 7.94317 21.542 12.0002C21.1775 13.161 20.6214 14.2525 19.898 15.2302" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14.12 14.1213C13.8394 14.402 13.4826 14.5932 13.0941 14.6708C12.7056 14.7485 12.3028 14.7091 11.9367 14.5576C11.5706 14.406 11.2577 14.1491 11.0375 13.8195C10.8173 13.4899 10.6997 13.1023 10.6997 12.7059C10.6997 12.3094 10.8173 11.9218 11.0375 11.5923" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.61189 6.61035C4.90489 7.75935 3.54889 9.47135 2.45889 12.0004C3.73289 16.0574 7.52289 19.0004 12.0009 19.0004C13.9326 19.0058 15.8304 18.4992 17.5019 17.5324" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M2 2L22 22" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="signin-remember" for="rememberLogin">
                        <input
                            type="checkbox"
                            id="rememberLogin"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span>Remember me on this device</span>
                    </label>

                    <button type="submit" class="signin-primary">Sign In</button>
                </form>

                <div class="signin-divider">or</div>

                <a href="{{ route('auth.google.redirect') }}" class="signin-google">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="#EA4335" d="M24 9.5c3.53 0 6.7 1.22 9.2 3.6l6.84-6.84C35.9 2.43 30.37 0 24 0 14.63 0 6.54 5.38 2.56 13.22l7.97 6.19C12.34 13.33 17.68 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.5 24.5c0-1.7-.15-3.33-.43-4.9H24v9.27h12.7c-.55 2.96-2.2 5.47-4.68 7.16l7.2 5.6c4.2-3.87 6.62-9.57 6.62-17.13z"/>
                        <path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.76 24c0-1.6.28-3.15.77-4.59l-7.97-6.19A24.03 24.03 0 0 0 0 24c0 3.88.93 7.56 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.37 0 11.72-2.1 15.63-5.72l-7.2-5.6c-2 1.34-4.58 2.12-8.43 2.12-6.32 0-11.66-3.83-13.47-9.09l-7.97 6.19C6.54 42.62 14.63 48 24 48z"/>
                    </svg>
                    Continue with Google
                </a>

                <p class="signin-note">Google login does not create a new account. Only existing active users can sign in via Google.</p>
                <div class="signin-help">
                    <a href="mailto:team.alordisha@gmail.com?subject=Need%20Help%20-%20Login%20Support">Need Help? Contact Team</a>
                </div>
            </article>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const passwordInput = document.getElementById('passwordInput');
            const togglePassword = document.getElementById('togglePassword');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeOff = document.getElementById('eyeOff');

            if (!passwordInput || !togglePassword) {
                return;
            }

            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeOff.style.display = isPassword ? 'block' : 'none';
            });
        })();

        (() => {
            const target = document.getElementById('signinTypewriter');

            if (!target) {
                return;
            }

            const messages = [
                'Keep teaching smooth and focused every day.',
                'Track attendance quickly with fewer clicks.',
                'Maintain clean records and confident reporting.'
            ];

            let messageIndex = 0;
            let charIndex = 0;
            let isDeleting = false;

            const typeSpeed = 48;
            const deleteSpeed = 28;
            const pauseAfterType = 1200;
            const pauseAfterDelete = 380;

            const tick = () => {
                const current = messages[messageIndex];

                if (!isDeleting) {
                    charIndex += 1;
                    target.textContent = current.slice(0, charIndex);

                    if (charIndex >= current.length) {
                        isDeleting = true;
                        window.setTimeout(tick, pauseAfterType);
                        return;
                    }

                    window.setTimeout(tick, typeSpeed);
                    return;
                }

                charIndex -= 1;
                target.textContent = current.slice(0, Math.max(0, charIndex));

                if (charIndex <= 0) {
                    isDeleting = false;
                    messageIndex = (messageIndex + 1) % messages.length;
                    window.setTimeout(tick, pauseAfterDelete);
                    return;
                }

                window.setTimeout(tick, deleteSpeed);
            };

            tick();
        })();
    </script>
@endpush
