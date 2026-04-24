@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    <style>
        .overview-card::before {
            display: none;
        }

        .overview-analytics {
            display: grid;
            grid-template-columns: minmax(180px, 220px) minmax(0, 1fr);
            gap: 1.15rem;
            align-items: center;
        }

        .overview-donut-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overview-donut {
            inline-size: 9rem;
            block-size: 9rem;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: conic-gradient(
                color-mix(in srgb, var(--color-success) 90%, var(--color-primary)) calc(var(--active) * 1%),
                color-mix(in srgb, var(--color-surface-strong) 70%, transparent) 0
            );
            border: 1px solid color-mix(in srgb, var(--color-border) 76%, transparent);
            box-shadow: inset 0 0 0 0.2rem color-mix(in srgb, var(--color-surface) 52%, transparent), 0 14px 24px -20px rgba(0, 0, 0, 0.75);
            will-change: background;
        }

        .overview-donut-inner {
            inline-size: 6.4rem;
            block-size: 6.4rem;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: color-mix(in srgb, var(--color-surface) 88%, transparent);
            border: 1px solid color-mix(in srgb, var(--color-border) 58%, transparent);
            line-height: 1.05;
            gap: .25rem;
        }

        .overview-donut-inner span {
            font-size: .78rem;
            color: var(--color-text-soft);
            font-weight: 600;
        }

        .overview-donut-inner strong {
            font-size: 1.35rem;
            color: var(--color-text);
            font-weight: 800;
        }

        .overview-chart-panel .metric-value {
            margin-bottom: .15rem;
        }

        .overview-chart-panel .metric-label {
            margin-top: 0;
        }

        .overview-bars {
            margin-top: .6rem;
            display: grid;
            gap: .55rem;
        }

        .overview-bar-item {
            display: grid;
            gap: .24rem;
        }

        .overview-bar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            font-size: .82rem;
            color: var(--color-text-soft);
        }

        .overview-bar-head strong {
            color: var(--color-text);
            font-weight: 700;
        }

        .overview-bar-track {
            block-size: .48rem;
            border-radius: 999px;
            background: color-mix(in srgb, var(--color-surface-strong) 72%, transparent);
            overflow: hidden;
        }

        .overview-bar-fill {
            display: block;
            block-size: 100%;
            inline-size: calc(var(--value) * 1%);
            border-radius: inherit;
            will-change: inline-size;
        }

        .overview-bar-fill.active {
            background: linear-gradient(90deg, color-mix(in srgb, var(--color-success) 88%, var(--color-primary)), color-mix(in srgb, var(--color-primary) 84%, #7ad7ff));
        }

        .overview-bar-fill.inactive {
            background: linear-gradient(90deg, #ef4444, #f97316);
        }

        .overview-bar-fill.attendance {
            background: linear-gradient(90deg, #22d3ee, #6366f1);
        }

        @media (max-width: 575.98px) {
            .overview-card {
                padding: .92rem;
            }

            .overview-sub {
                margin-bottom: .55rem;
                font-size: .9rem;
            }

            .overview-head {
                font-size: 1.85rem;
                margin-bottom: .15rem;
            }

            .overview-analytics {
                grid-template-columns: minmax(110px, 132px) minmax(0, 1fr);
                gap: .72rem;
                align-items: center;
            }

            .overview-donut {
                inline-size: 6.2rem;
                block-size: 6.2rem;
            }

            .overview-donut-inner {
                inline-size: 4.5rem;
                block-size: 4.5rem;
            }

            .overview-donut-inner span {
                font-size: .72rem;
            }

            .overview-donut-inner strong {
                font-size: 1.02rem;
            }

            .overview-chart-panel .metric-value {
                font-size: 2rem;
                line-height: 1;
            }

            .overview-chart-panel .metric-label {
                font-size: .78rem;
                margin-bottom: .35rem;
            }

            .overview-bars {
                gap: .45rem;
            }

            .overview-bar-head {
                font-size: .76rem;
            }

            .overview-bar-head strong {
                font-size: .76rem;
            }

            .overview-bar-track {
                block-size: .42rem;
            }
        }
    </style>

    <section class="dashboard-grid">
        <article class="overview-card">
            @php
                $inactivePercent = $totalStudents > 0
                    ? (int) round(($inactiveStudents / $totalStudents) * 100)
                    : 0;
            @endphp

            <p class="overview-sub">Today's Overview</p>
            <h2 class="overview-head">Teaching Dashboard</h2>

            <div class="overview-analytics" style="margin-top: 1rem;">
                <div class="overview-donut-wrap">
                    <div class="overview-donut" data-target="{{ $activePercent }}" style="--active: 0;" aria-hidden="true">
                        <div class="overview-donut-inner">
                            <span>Active</span>
                            <strong class="overview-active-percent" data-target="{{ $activePercent }}">0%</strong>
                        </div>
                    </div>
                </div>

                <div class="overview-chart-panel">
                    <p class="metric-value">{{ $activeStudents }}/{{ $totalStudents }}</p>
                    <p class="metric-label">Your students growth distribution</p>

                    <div class="overview-bars" role="presentation" aria-hidden="true">
                        <div class="overview-bar-item">
                            <div class="overview-bar-head">
                                <span>Active</span>
                                <strong class="overview-bar-percent" data-target="{{ $activePercent }}">0%</strong>
                            </div>
                            <div class="overview-bar-track">
                                <span class="overview-bar-fill active" data-target="{{ $activePercent }}" style="--value: 0;"></span>
                            </div>
                        </div>

                        <div class="overview-bar-item">
                            <div class="overview-bar-head">
                                <span>Inactive</span>
                                <strong class="overview-bar-percent" data-target="{{ $inactivePercent }}">0%</strong>
                            </div>
                            <div class="overview-bar-track">
                                <span class="overview-bar-fill inactive" data-target="{{ $inactivePercent }}" style="--value: 0;"></span>
                            </div>
                        </div>

                        <div class="overview-bar-item">
                            <div class="overview-bar-head">
                                <span>Attendance Today</span>
                                <strong class="overview-bar-percent" data-target="{{ $attendancePercent }}">0%</strong>
                            </div>
                            <div class="overview-bar-track">
                                <span class="overview-bar-fill attendance" data-target="{{ $attendancePercent }}" style="--value: 0;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <div class="quick-grid">
            <a href="{{ route('students.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 21V19C16 17.8954 15.1046 17 14 17H6C4.89543 17 4 17.8954 4 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13C12.2091 13 14 11.2091 14 9C14 6.79086 12.2091 5 10 5C7.79086 5 6 6.79086 6 9C6 11.2091 7.79086 13 10 13Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 8V14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 11H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Manage Students</h3>
                <p>{{ $totalStudents }} students</p>
            </a>

            <a href="{{ route('attendance.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.8"/><path d="M8 2V6M16 2V6M3 10H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 14L11 16L15 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Take Attendance</h3>
                <p>{{ $attendanceToday }} done today</p>
            </a>

            <a href="{{ route('fees.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 7C4 5.89543 4.89543 5 6 5H18C19.1046 5 20 5.89543 20 7V17C20 18.1046 19.1046 19 18 19H6C4.89543 19 4 18.1046 4 17V7Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 10H16M8 14H12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <h3>Fees Tracker</h3>
                <p>Collect and track payments</p>
            </a>

            <a href="{{ route('students.import') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M7 9L12 4L17 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15V18C4 19.1046 4.89543 20 6 20H18C19.1046 20 20 19.1046 20 18V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <h3>Import Students</h3>
                <p>Bulk upload with template</p>
            </a>
        </div>

        <article class="glass-card recent-card">
            <h3 class="section-title">Class Highlights</h3>

            <ul class="recent-list">
                <li class="recent-item">
                    <div>
                        <strong>Students</strong>
                        <span>Total assigned to you</span>
                    </div>
                    <span class="pill">{{ $totalStudents }}</span>
                </li>

                <li class="recent-item">
                    <div>
                        <strong>Subjects</strong>
                        <span>Current teaching load</span>
                    </div>
                    <span class="pill">{{ $totalSubjects }}</span>
                </li>

                <li class="recent-item">
                    <div>
                        <strong>Branches</strong>
                        <span>Linked teaching branches</span>
                    </div>
                    <span class="pill">{{ $totalBranches }}</span>
                </li>
            </ul>
        </article>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const donut = document.querySelector('.overview-donut');
            const donutPercent = document.querySelector('.overview-active-percent');
            const barFills = Array.from(document.querySelectorAll('.overview-bar-fill'));
            const barPercents = Array.from(document.querySelectorAll('.overview-bar-percent'));

            if (!donut || !donutPercent || barFills.length === 0) {
                return;
            }

            const donutTarget = Number(donut.dataset.target || 0);
            const barTargets = barFills.map((bar) => Number(bar.dataset.target || 0));
            const duration = 1200;
            let start = null;

            const easeOutCubic = (progress) => 1 - Math.pow(1 - progress, 3);

            const animate = (now) => {
                if (start === null) {
                    start = now;
                }

                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = easeOutCubic(progress);

                const currentDonut = Math.round(donutTarget * eased);
                donut.style.setProperty('--active', currentDonut);
                donutPercent.textContent = `${currentDonut}%`;

                barFills.forEach((bar, index) => {
                    const current = Math.round((barTargets[index] || 0) * eased);
                    bar.style.setProperty('--value', current);

                    if (barPercents[index]) {
                        barPercents[index].textContent = `${current}%`;
                    }
                });

                if (progress < 1) {
                    window.requestAnimationFrame(animate);
                }
            };

            donut.style.setProperty('--active', 0);
            donutPercent.textContent = '0%';

            barFills.forEach((bar, index) => {
                bar.style.setProperty('--value', 0);

                if (barPercents[index]) {
                    barPercents[index].textContent = '0%';
                }
            });

            window.setTimeout(() => {
                window.requestAnimationFrame(animate);
            }, 120);
        });
    </script>
@endpush
