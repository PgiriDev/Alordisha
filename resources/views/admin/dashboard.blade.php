@extends('layouts.app')

@section('title', 'Admin Dashboard')

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

        .overview-bar-fill.teachers {
            background: linear-gradient(90deg, #22d3ee, #6366f1);
        }

        .quick-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: .55rem;
        }

        .quick-card {
            min-height: 6rem;
            padding: .7rem .5rem;
            gap: .26rem;
            border-radius: 14px;
        }

        .quick-icon {
            inline-size: 2rem;
            block-size: 2rem;
            margin-bottom: .02rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 0;
        }

        .quick-icon svg {
            width: 1.35rem;
            height: 1.35rem;
            display: block;
        }

        .quick-card h3 {
            font-size: .72rem;
            line-height: 1.12;
        }

        .quick-card p {
            font-size: .62rem;
        }

        @media (max-width: 1399.98px) {
            .quick-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .quick-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
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

            .quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .5rem;
            }

            .quick-card {
                min-height: 5.3rem;
                padding: .62rem .42rem;
            }

            .quick-card h3 {
                font-size: .66rem;
            }

            .quick-card p {
                font-size: .58rem;
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
                $teacherCoveragePercent = $totalStudents > 0
                    ? (int) min(100, round(($totalTeachers / $totalStudents) * 100))
                    : 0;
            @endphp

            <p class="overview-sub">Today's Overview</p>
            <h2 class="overview-head">Admin Control Panel</h2>

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
                    <p class="metric-label">Student growth distribution</p>

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
                                <span>Teacher Coverage</span>
                                <strong class="overview-bar-percent" data-target="{{ $teacherCoveragePercent }}">0%</strong>
                            </div>
                            <div class="overview-bar-track">
                                <span class="overview-bar-fill teachers" data-target="{{ $teacherCoveragePercent }}" style="--value: 0;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <div class="quick-grid">
            <a href="{{ route('admin.students') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 21V19C18 17.8954 17.1046 17 16 17H8C6.89543 17 6 17.8954 6 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 13C14.2091 13 16 11.2091 16 9C16 6.79086 14.2091 5 12 5C9.79086 5 8 6.79086 8 9C8 11.2091 9.79086 13 12 13Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Students</h3>
                <p>{{ $totalStudents }} total</p>
            </a>

            <a href="{{ route('teachers.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 21V19C16 17.8954 15.1046 17 14 17H6C4.89543 17 4 17.8954 4 19V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 13C12.2091 13 14 11.2091 14 9C14 6.79086 12.2091 5 10 5C7.79086 5 6 6.79086 6 9C6 11.2091 7.79086 13 10 13Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 8V14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M23 11H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Manage Teachers</h3>
                <p>{{ $totalTeachers }} total</p>
            </a>

            <a href="{{ route('branches.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 3V21M6 7H14C15.6569 7 17 8.34315 17 10C17 11.6569 15.6569 13 14 13H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Manage Branches</h3>
                <p>{{ $totalBranches }} active</p>
            </a>

            <a href="{{ route('subjects.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 4H18C19.1046 4 20 4.89543 20 6V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V6C4 4.89543 4.89543 4 6 4Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8H16M8 12H16M8 16H12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <h3>Manage Subjects</h3>
                <p>{{ $totalSubjects }} listed</p>
            </a>

            <a href="{{ route('admin.notices.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 8C18 4.68629 15.3137 2 12 2C8.68629 2 6 4.68629 6 8V11.5858C6 12.1162 5.78929 12.6249 5.41421 13L4 14.4142V16H20V14.4142L18.5858 13C18.2107 12.6249 18 12.1162 18 11.5858V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Manage Notices</h3>
                <p>Publish welcome alerts</p>
            </a>

            <a href="{{ route('admin.collections.index') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 4H16C17.1046 4 18 4.89543 18 6V18C18 19.1046 17.1046 20 16 20H5C3.89543 20 3 19.1046 3 18V6C3 4.89543 3.89543 4 5 4Z" stroke="currentColor" stroke-width="1.8"/><path d="M7 8H14M7 12H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M18 7H19.5C20.3284 7 21 7.67157 21 8.5V17.5C21 18.3284 20.3284 19 19.5 19H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <h3>Book Collection</h3>
                <p>{{ $totalCollections }} total</p>
            </a>

            <a href="{{ route('teachers.create') }}" class="quick-card">
                <div class="quick-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M5 12H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <h3>Add New Teacher</h3>
                <p>Quick onboarding</p>
            </a>
        </div>

        <article class="glass-card recent-card">
            <h3 class="section-title">Recent Registrations</h3>

            <ul class="recent-list">
                @forelse ($recentStudents as $student)
                    <li class="recent-item">
                        <div>
                            <strong>{{ $student->name }}</strong>
                            <span>{{ $student->created_at->diffForHumans() }}</span>
                        </div>
                        <span class="pill">{{ $student->branch->name ?? 'Unassigned' }}</span>
                    </li>
                @empty
                    <li class="recent-item">
                        <div>
                            <strong>No registrations yet</strong>
                            <span>New records will appear here</span>
                        </div>
                    </li>
                @endforelse
            </ul>
        </article>

        <article class="glass-card recent-card">
            <h3 class="section-title">Branch Overview</h3>

            <ul class="recent-list">
                @forelse ($branch_students as $branch)
                    <li class="recent-item">
                        <div>
                            <strong>{{ $branch->name }}</strong>
                            <span>Active enrollments</span>
                        </div>
                        <span class="pill">{{ $branch->students_count }} students</span>
                    </li>
                @empty
                    <li class="recent-item">
                        <div>
                            <strong>No branch data</strong>
                            <span>Create a branch to see analytics</span>
                        </div>
                    </li>
                @endforelse
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
