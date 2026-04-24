@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
    @php
        $photoUrl = $student->photo_path
            ? asset('storage/' . $student->photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=1f3b6e&color=fff';

        $aadhaarUrl = $student->aadhaar_path ? asset('storage/' . $student->aadhaar_path) : null;
        $aadhaarExt = strtolower(pathinfo((string) ($student->aadhaar_path ?? ''), PATHINFO_EXTENSION));
        $aadhaarIsImage = in_array($aadhaarExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
        $aadhaarFileName = $student->aadhaar_path ? basename($student->aadhaar_path) : null;
        $subjectYears = (array) ($student->subject_years ?? []);
        $subjectYearText = collect($subjectYears)
            ->map(function ($entry) use ($subjectMap) {
                $subjectName = $subjectMap[$entry['subject_id'] ?? null] ?? ('Subject #' . ($entry['subject_id'] ?? 'N/A'));
                $yearLabel = trim((string) ($entry['year_label'] ?? ''));

                return $yearLabel !== '' ? ($subjectName . ' ' . $yearLabel) : $subjectName;
            })
            ->implode(', ');
    @endphp

    <style>
        .ethereal-card.details-readonly-card {
            background: rgba(20, 20, 25, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 34px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .page-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            color: var(--color-text);
            font-weight: 300;
            margin-bottom: 26px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .readonly-form .form-label {
            color: var(--color-text-soft);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-glass {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--color-text);
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .form-glass[readonly] {
            pointer-events: none;
            opacity: 1;
        }

        textarea.form-glass {
            min-height: 78px;
            resize: none;
            font-weight: 500;
        }

        .section-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin: 6px 0;
        }

        .photo-box {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: .7rem;
            background: rgba(255, 255, 255, 0.03);
            display: inline-flex;
            justify-content: center;
        }

        .media-preview-row {
            margin-bottom: .25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: .25rem;
        }

        .media-preview-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: .6rem;
            background: rgba(255, 255, 255, 0.03);
            height: 100%;
            width: fit-content;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .35rem;
        }

        .media-preview-label {
            color: var(--color-text-soft);
            font-size: .82rem;
            font-weight: 600;
            margin-bottom: .1rem;
            padding-left: .15rem;
        }

        .photo-preview {
            width: 110px;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .aadhaar-thumb {
            width: 110px;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            cursor: zoom-in;
            transition: transform .2s ease;
        }

        .aadhaar-thumb:hover {
            transform: scale(1.03);
        }

        .aadhaar-empty {
            color: var(--color-text-soft);
            min-height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 1px dashed rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            padding: .65rem;
        }


        .aadhaar-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(2, 8, 24, 0.58);
            z-index: 1200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }

        .aadhaar-modal-backdrop.show {
            display: flex;
        }

        .aadhaar-modal {
            width: min(760px, 92vw);
            max-height: 82vh;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(10, 18, 34, 0.96);
            padding: .9rem;
            box-shadow: 0 26px 48px -32px rgba(0, 0, 0, 0.85);
        }

        .aadhaar-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: .6rem;
        }

        .aadhaar-modal-title {
            font-size: .92rem;
            color: var(--color-text-soft);
            font-weight: 600;
        }

        .aadhaar-modal-close {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.04);
            color: var(--color-text);
            cursor: pointer;
        }

        .aadhaar-modal-image {
            width: 100%;
            max-height: calc(82vh - 80px);
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.02);
            transform-origin: center center;
            transition: transform .12s ease;
            cursor: zoom-in;
            user-select: none;
            -webkit-user-drag: none;
            touch-action: none;
        }

        .status-readonly.active {
            color: #22c55e;
            border-color: color-mix(in srgb, #22c55e 45%, rgba(255, 255, 255, 0.1));
        }

        .status-readonly.inactive {
            color: #ef4444;
            border-color: color-mix(in srgb, #ef4444 45%, rgba(255, 255, 255, 0.1));
        }

        .btn-close-readonly {
            width: 100%;
            border: none;
            border-radius: 999px;
            height: 3.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.03rem;
            letter-spacing: .01em;
            color: #fff;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 0 10px 24px -16px rgba(239, 68, 68, 0.75);
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .btn-close-readonly:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 28px -16px rgba(239, 68, 68, 0.82);
            filter: brightness(1.03);
        }

        .doc-actions .btn {
            border-radius: 999px;
        }

        @media (max-width: 767.98px) {
            .ethereal-card.details-readonly-card {
                padding: 20px;
                border-radius: 20px;
            }

            .page-title {
                font-size: 1.92rem;
                margin-bottom: 20px;
            }

            .photo-box {
                display: inline-flex;
            }

            .btn-close-readonly {
                height: 3.05rem;
            }

            .media-preview-card {
                margin-bottom: .7rem;
                max-width: 100%;
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.students') }}" class="text-decoration-none text-muted small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="ethereal-card details-readonly-card">
                <h3 class="page-title">Student Details</h3>

                <div class="row g-4 readonly-form">
                    <div class="col-6 col-md-4 col-lg-3 media-preview-row">
                        <div class="media-preview-label">Student Photo</div>
                        <div class="media-preview-card">
                            <img src="{{ $photoUrl }}" alt="{{ $student->name }}" class="photo-preview">
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-3 media-preview-row">
                        <div class="media-preview-label">Aadhaar / Document</div>
                        <div class="media-preview-card">
                            @if($aadhaarUrl && $aadhaarIsImage)
                                <img
                                    src="{{ $aadhaarUrl }}"
                                    alt="Aadhaar"
                                    class="aadhaar-thumb"
                                    id="aadhaarThumb"
                                    aria-label="Preview Aadhaar"
                                >
                            @elseif($aadhaarUrl)
                                <div class="aadhaar-empty">
                                    <div>
                                            Document available
                                    </div>
                                </div>
                                <div class="doc-actions mt-2">
                                    <a href="{{ $aadhaarUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">Open</a>
                                    <a href="{{ $aadhaarUrl }}" download class="btn btn-outline-secondary btn-sm ms-2">Download</a>
                                </div>
                            @else
                                <div class="aadhaar-empty">No document uploaded.</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12"><div class="section-divider"></div></div>

                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input class="form-control form-glass" value="{{ $student->name ?: 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Father's Name</label>
                        <input class="form-control form-glass" value="{{ $student->father_name ?: 'N/A' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input class="form-control form-glass" value="{{ $student->dob ?: 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Class / School Level</label>
                        <input class="form-control form-glass" value="{{ $student->class_level ?: 'N/A' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input class="form-control form-glass" value="{{ $student->phone ?: 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Number</label>
                        <input class="form-control form-glass" value="{{ $student->whatsapp ?: 'N/A' }}" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea class="form-control form-glass" rows="2" readonly>{{ $student->address ?: 'N/A' }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Institution (MVKC / SSSP)</label>
                        <input class="form-control form-glass" value="{{ $student->institution ?: 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assign Branch</label>
                        <input class="form-control form-glass" value="{{ $student->branch->name ?? 'N/A' }}" readonly>
                    </div>

                    <div class="col-12"><div class="section-divider"></div></div>

                    <div class="col-md-6">
                        <label class="form-label">Teacher</label>
                        <input class="form-control form-glass" value="{{ $student->teacher->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Registration Number</label>
                        <input class="form-control form-glass" value="{{ $student->registration_number ?: 'N/A' }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Subjects & Year Levels</label>
                        <textarea class="form-control form-glass" rows="2" readonly>{{ $subjectYearText !== '' ? $subjectYearText : 'No subject mapping found.' }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <input
                            class="form-control form-glass status-readonly {{ $student->status === 'active' ? 'active' : 'inactive' }}"
                            value="{{ ucfirst($student->status ?? 'inactive') }}"
                            readonly
                        >
                    </div>

                    <div class="col-12 mt-2">
                        <a href="{{ route('admin.students') }}" class="btn-close-readonly">Close</a>
                    </div>
                </div>
            </div>

            @if($aadhaarUrl && $aadhaarIsImage)
                <div class="aadhaar-modal-backdrop" id="aadhaarModalBackdrop" aria-hidden="true">
                    <div class="aadhaar-modal" role="dialog" aria-modal="true" aria-label="Aadhaar preview">
                        <div class="aadhaar-modal-head">
                            <div class="aadhaar-modal-title">Aadhaar Preview</div>
                            <button type="button" class="aadhaar-modal-close" id="aadhaarModalClose" aria-label="Close preview">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <img src="{{ $aadhaarUrl }}" alt="Aadhaar preview" class="aadhaar-modal-image" id="aadhaarModalImage">
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@if($aadhaarUrl && $aadhaarIsImage)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const thumb = document.getElementById('aadhaarThumb');
                const backdrop = document.getElementById('aadhaarModalBackdrop');
                const closeBtn = document.getElementById('aadhaarModalClose');
                const modalImage = document.getElementById('aadhaarModalImage');

                if (!thumb || !backdrop || !closeBtn || !modalImage) {
                    return;
                }

                let scale = 1;
                const MIN_ZOOM = 1;
                const MAX_ZOOM = 4;
                const pointers = new Map();
                let initialPinchDistance = null;
                let initialScale = 1;

                const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

                const applyZoom = () => {
                    modalImage.style.transform = `scale(${scale})`;
                    modalImage.style.cursor = scale > 1 ? 'zoom-out' : 'zoom-in';
                };

                const zoomBy = (delta) => {
                    scale = clamp(scale + delta, MIN_ZOOM, MAX_ZOOM);
                    applyZoom();
                };

                const resetZoom = () => {
                    scale = 1;
                    initialPinchDistance = null;
                    initialScale = 1;
                    pointers.clear();
                    applyZoom();
                };

                const closePreview = () => {
                    backdrop.classList.remove('show');
                    backdrop.setAttribute('aria-hidden', 'true');
                    resetZoom();
                };

                thumb.addEventListener('click', () => {
                    backdrop.classList.add('show');
                    backdrop.setAttribute('aria-hidden', 'false');
                    resetZoom();
                });

                modalImage.addEventListener('wheel', (event) => {
                    event.preventDefault();
                    zoomBy(event.deltaY < 0 ? 0.18 : -0.18);
                }, { passive: false });

                modalImage.addEventListener('dblclick', (event) => {
                    event.preventDefault();
                    scale = scale > 1 ? 1 : 2;
                    applyZoom();
                });

                const getDistance = (p1, p2) => {
                    const dx = p2.clientX - p1.clientX;
                    const dy = p2.clientY - p1.clientY;
                    return Math.hypot(dx, dy);
                };

                modalImage.addEventListener('pointerdown', (event) => {
                    pointers.set(event.pointerId, event);
                    modalImage.setPointerCapture(event.pointerId);

                    if (pointers.size === 2) {
                        const [first, second] = Array.from(pointers.values());
                        initialPinchDistance = getDistance(first, second);
                        initialScale = scale;
                    }
                });

                modalImage.addEventListener('pointermove', (event) => {
                    if (!pointers.has(event.pointerId)) {
                        return;
                    }

                    pointers.set(event.pointerId, event);

                    if (pointers.size === 2) {
                        const [first, second] = Array.from(pointers.values());
                        const currentDistance = getDistance(first, second);

                        if (initialPinchDistance && initialPinchDistance > 0) {
                            const zoomRatio = currentDistance / initialPinchDistance;
                            scale = clamp(initialScale * zoomRatio, MIN_ZOOM, MAX_ZOOM);
                            applyZoom();
                        }
                    }
                });

                const clearPointer = (event) => {
                    pointers.delete(event.pointerId);
                    if (pointers.size < 2) {
                        initialPinchDistance = null;
                        initialScale = scale;
                    }
                };

                modalImage.addEventListener('pointerup', clearPointer);
                modalImage.addEventListener('pointercancel', clearPointer);
                modalImage.addEventListener('pointerleave', clearPointer);

                closeBtn.addEventListener('click', closePreview);
                backdrop.addEventListener('click', (event) => {
                    if (event.target === backdrop) {
                        closePreview();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closePreview();
                    }
                });
            });
        </script>
    @endpush
@endif
