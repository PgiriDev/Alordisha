@extends('layouts.app')

@section('title', 'Privacy & Policy - Alor Disha')

@section('content')
    <style>
        .policy-wrap { max-width: 900px; margin: 2rem auto; padding: 0 1rem 2rem; color: var(--text-main, #0f172a); }
        .policy-card { background: rgba(255,255,255,.85); border: 1px solid rgba(148,163,184,.25); border-radius: 16px; padding: 2rem; box-shadow: 0 12px 28px rgba(15,23,42,.08); }
        [data-theme='dark'] .policy-card { background: rgba(30,41,59,.78); border-color: rgba(148,163,184,.2); box-shadow: 0 12px 28px rgba(0,0,0,.35); }
        .policy-title { font-size: 2rem; font-weight: 800; margin-bottom: .5rem; }
        .policy-sub { color: var(--text-muted, #64748b); margin-bottom: 1.5rem; }
        .policy-section { margin-top: 1.5rem; }
        .policy-section h2 { font-size: 1.2rem; margin-bottom: .6rem; }
        .policy-section p, .policy-section li { line-height: 1.7; color: var(--text-muted, #64748b); }
        .policy-back { display: inline-block; margin-top: 1.75rem; text-decoration: none; font-weight: 600; color: #4f46e5; }
    </style>

    <div class="policy-wrap">
        <div class="policy-card">
            <h1 class="policy-title">Privacy & Policy</h1>
            <p class="policy-sub">Last updated: {{ now()->format('d M Y') }}</p>

            <div class="policy-section">
                <h2>1. Information We Collect</h2>
                <p>We collect basic profile and academic information required to manage students, attendance, and fee records inside the Alor Disha platform.</p>
            </div>

            <div class="policy-section">
                <h2>2. How We Use Information</h2>
                <ul>
                    <li>To maintain student and teacher records.</li>
                    <li>To track attendance and fee payments.</li>
                    <li>To improve support and communication.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>3. Data Protection</h2>
                <p>We use controlled access and authentication to protect user data. Only authorized staff can access sensitive records.</p>
            </div>

            <div class="policy-section">
                <h2>4. Contact</h2>
                <p>If you have questions about this policy, contact us at <a href="mailto:adsp0000001@gmail.com">adsp0000001@gmail.com</a> or call <a href="tel:+917404917787">+91 74049 17787</a>.</p>
            </div>

            <a class="policy-back" href="{{ url('/') }}">? Back to Welcome Page</a>
        </div>
    </div>
@endsection
