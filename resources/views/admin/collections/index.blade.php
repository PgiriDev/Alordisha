@extends('layouts.app')

@section('title', 'Book Collection')

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endpush

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap');

        .top-bar {
            display: none !important;
        }

        .library-shell {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            position: relative;
            display: grid;
            gap: .9rem;
            max-width: 1180px;
            margin: 0 auto;
            padding: .35rem;
            isolation: isolate;
        }

        .surface-panel {
            border-radius: 20px;
            border: 1px solid color-mix(in srgb, var(--color-border) 78%, transparent);
            background:
                linear-gradient(170deg, color-mix(in srgb, var(--color-surface) 95%, transparent), color-mix(in srgb, var(--color-surface-strong) 42%, transparent));
            box-shadow: 0 16px 30px -24px rgba(2, 8, 23, .55);
        }

        .library-header {
            grid-column: 1 / -1;
            display: grid;
            gap: .8rem;
            grid-template-columns: 1fr auto;
            align-items: center;
            padding: 1.1rem 1.25rem;
        }

        .hero-title {
            margin: 0;
            font-size: clamp(1.35rem, 1.05rem + 1vw, 2rem);
            line-height: 1.08;
            letter-spacing: -.03em;
            font-weight: 800;
            color: var(--color-text);
        }

        .hero-sub {
            margin: .4rem 0 0;
            max-width: 680px;
            color: var(--color-text-soft);
            font-size: .9rem;
            line-height: 1.45;
        }

        .add-btn {
            border-radius: 13px;
            min-height: 44px;
            padding: .65rem 1rem;
            font-weight: 800;
            letter-spacing: .01em;
            color: #101418;
            background: linear-gradient(145deg, #fcd34d, #fb923c);
            border: 1px solid color-mix(in srgb, #ea580c 52%, #facc15);
            box-shadow: 0 10px 22px -16px rgba(234, 88, 12, .8);
            transition: transform .24s ease, box-shadow .24s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px -18px rgba(234, 88, 12, .9);
            color: #101418;
        }

        .add-panel,
        .library-controls,
        .library-shelf {
            grid-column: 1 / -1;
            border-radius: 17px;
            border: 1px solid color-mix(in srgb, var(--color-border) 80%, transparent);
            background: color-mix(in srgb, var(--color-surface) 93%, transparent);
            box-shadow: 0 16px 30px -24px rgba(2, 8, 23, .55);
        }

        .add-panel {
            padding: .95rem 1rem;
        }

        .cover-pick {
            border: 1px dashed color-mix(in srgb, var(--color-border) 72%, transparent);
            border-radius: 12px;
            padding: .8rem;
            background: color-mix(in srgb, var(--color-surface-strong) 55%, transparent);
        }

        .upload-action-row {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .upload-action-row .btn {
            border-radius: 10px;
            font-weight: 700;
            padding-inline: .85rem;
        }

        .add-panel .form-control,
        .library-controls .form-control,
        .library-controls .form-select,
        .add-panel .form-select,
        .edit-panel .form-control {
            border-radius: 10px;
            border-color: color-mix(in srgb, var(--color-border) 74%, transparent);
            background: color-mix(in srgb, var(--color-surface) 92%, transparent);
            color: var(--color-text);
            min-height: 42px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .add-panel .form-control:focus,
        .library-controls .form-control:focus,
        .library-controls .form-select:focus,
        .add-panel .form-select:focus,
        .edit-panel .form-control:focus {
            border-color: color-mix(in srgb, var(--color-ring) 76%, var(--color-border));
            box-shadow: 0 0 0 .16rem color-mix(in srgb, var(--color-ring) 24%, transparent);
        }

        .upload-progress-container {
            margin-top: .7rem;
            display: none;
        }

        .upload-status {
            margin-top: .22rem;
            font-size: .76rem;
            color: var(--color-text-soft);
        }

        .library-controls {
            padding: .75rem;
        }

        .search-input-wrap {
            position: relative;
        }

        .search-input-wrap i {
            position: absolute;
            left: .8rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-soft);
            pointer-events: none;
            font-size: .9rem;
        }

        .library-controls .form-control {
            padding-left: 2.2rem;
        }

        .library-controls .btn {
            min-height: 42px;
            border-radius: 10px;
            font-weight: 700;
        }

        .library-shelf {
            padding: .9rem;
        }

        .shelf-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .7rem;
            margin-bottom: .85rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid color-mix(in srgb, var(--color-border) 72%, transparent);
        }

        .shelf-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: var(--color-text);
        }

        .shelf-count {
            font-size: .76rem;
            font-weight: 700;
            color: #9a3412;
            background: rgba(249, 115, 22, .15);
            border: 1px solid rgba(249, 115, 22, .22);
            border-radius: 999px;
            padding: .26rem .6rem;
            white-space: nowrap;
        }

        .library-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: .75rem;
            align-items: start;
        }

        .book-card {
            --cover-w: 104px;
            display: grid;
            grid-template-columns: var(--cover-w) minmax(0, 1fr);
            border-radius: 14px;
            border: 1px solid color-mix(in srgb, var(--color-border) 78%, transparent);
            background: linear-gradient(180deg, color-mix(in srgb, var(--color-surface) 95%, transparent), color-mix(in srgb, var(--color-surface-strong) 48%, transparent));
            position: relative;
            overflow: hidden;
            min-height: 146px;
            box-shadow: 0 8px 18px -16px rgba(2, 8, 23, .7);
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
            animation: shelfIn .34s ease both;
        }

        .book-card:hover,
        .book-card:focus-within {
            transform: translateY(-3px);
            border-color: color-mix(in srgb, var(--color-primary) 48%, var(--color-border));
            box-shadow:
                0 16px 24px -20px rgba(2, 8, 23, .82),
                0 0 0 1px color-mix(in srgb, var(--color-primary) 24%, transparent),
                0 0 20px -12px color-mix(in srgb, var(--color-primary) 42%, transparent);
        }

        .book-cover-wrap {
            position: relative;
            width: 100%;
            height: 100%;
            background: color-mix(in srgb, var(--color-surface-strong) 45%, transparent);
            overflow: hidden;
            border-radius: 13px 0 0 13px;
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .35s ease;
        }

        .book-card:hover .book-cover,
        .book-card:focus-within .book-cover {
            transform: scale(1.05);
        }

        .book-body {
            padding: .62rem .7rem .7rem;
            display: grid;
            gap: .3rem;
            align-content: start;
        }

        .book-name {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -.012em;
            color: var(--color-text);
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            overflow: hidden;
            min-height: calc(1.2em * 2);
        }

        .book-meta {
            margin: 0;
            color: color-mix(in srgb, var(--color-text-soft) 92%, transparent);
            font-size: .76rem;
            line-height: 1.35;
        }

        .book-author {
            font-weight: 600;
            font-size: .84rem;
            line-height: 1.42;
            color: color-mix(in srgb, var(--color-primary) 46%, var(--color-text));
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            overflow: hidden;
        }

        .book-date {
            margin-top: .1rem;
            font-size: .72rem;
            letter-spacing: .012em;
            color: color-mix(in srgb, var(--color-text-soft) 86%, transparent);
        }

        .card-actions {
            position: absolute;
            top: .4rem;
            right: .45rem;
            display: flex;
            gap: .3rem;
            z-index: 2;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity .22s ease, transform .22s ease;
        }

        .book-card:hover .card-actions,
        .book-card:focus-within .card-actions {
            opacity: 1;
            transform: translateY(0);
        }

        .icon-action {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid color-mix(in srgb, var(--color-border) 80%, transparent);
            background: rgba(255, 255, 255, .82);
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .15s ease, border-color .15s ease;
        }

        .icon-action:hover {
            transform: translateY(-1px);
            border-color: rgba(14, 165, 233, .45);
        }

        .icon-action.danger {
            color: #dc2626;
        }

        .edit-panel {
            position: absolute;
            top: .35rem;
            right: .35rem;
            bottom: .35rem;
            left: calc(var(--cover-w) + .35rem);
            margin: 0;
            border: 1px solid color-mix(in srgb, var(--color-border) 72%, transparent);
            background: color-mix(in srgb, var(--color-surface) 96%, transparent);
            border-radius: 10px;
            padding: .55rem;
            overflow: auto;
            z-index: 4;
            box-shadow: 0 14px 24px -18px rgba(2, 8, 23, .8);
        }

        .book-card.is-editing {
            z-index: 6;
        }

        .book-card.is-editing .book-name,
        .book-card.is-editing .book-author,
        .book-card.is-editing .book-date {
            opacity: .06;
        }

        .empty-library {
            grid-column: 1 / -1;
            border-radius: 14px;
            padding: 1rem;
            border: 1px dashed color-mix(in srgb, var(--color-border) 72%, transparent);
            background: color-mix(in srgb, var(--color-surface) 88%, transparent);
            text-align: center;
            color: var(--color-text-soft);
        }

        .pagination-wrap {
            margin-top: .2rem;
            padding-inline: .15rem;
        }

        .pagination-wrap .pagination {
            margin-bottom: 0;
        }

        @keyframes shelfIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991.98px) {
            .library-grid {
                grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .library-shell {
                gap: .75rem;
                padding: .1rem;
            }

            .library-header {
                grid-template-columns: 1fr;
                padding: .9rem;
            }

            .add-btn {
                width: 100%;
            }

            .hero-title {
                font-size: 1.2rem;
            }

            .hero-sub {
                font-size: .84rem;
            }

            .library-grid {
                grid-template-columns: 1fr;
                gap: .62rem;
            }

            .book-card {
                --cover-w: 96px;
                grid-template-columns: var(--cover-w) minmax(0, 1fr);
                min-height: 136px;
            }

            .card-actions {
                opacity: 1;
                transform: none;
            }

            .icon-action {
                width: 28px;
                height: 28px;
            }
        }

        @media (max-width: 420px) {
            .book-card {
                --cover-w: 88px;
                grid-template-columns: var(--cover-w) minmax(0, 1fr);
                min-height: 124px;
            }
        }

        :root[data-theme='dark'] .surface-panel,
        :root[data-theme='dark'] .add-panel,
        :root[data-theme='dark'] .library-controls,
        :root[data-theme='dark'] .library-shelf,
        :root[data-theme='dark'] .book-card {
            border-color: color-mix(in srgb, var(--color-border) 94%, transparent);
            box-shadow: 0 14px 24px -20px rgba(0, 0, 0, .88);
        }

        :root[data-theme='dark'] .icon-action {
            background: color-mix(in srgb, var(--color-surface-strong) 72%, transparent);
            color: var(--color-text);
        }

        .camera-modal {
            position: fixed;
            inset: 0;
            z-index: 1055;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.65);
            padding: 1rem;
        }

        .camera-card {
            width: min(560px, 100%);
            border-radius: 14px;
            overflow: hidden;
            background: var(--color-surface);
            border: 1px solid color-mix(in srgb, var(--color-border) 70%, transparent);
        }

        .camera-video-wrap {
            background: #000;
            aspect-ratio: 4 / 3;
            position: relative;
        }

        .camera-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .camera-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: .8rem;
            padding: .85rem;
        }

        .camera-main-btn {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 4px solid #fff;
            background: #e2e8f0;
        }

        .crop-wrap {
            background: #000;
            min-height: 320px;
            max-height: 72vh;
            overflow: hidden;
        }

        .crop-image {
            width: 100%;
            max-width: 100%;
            display: block;
        }
    </style>

    <section class="library-shell">
        <article class="library-header surface-panel">
            <div>
                <h2 class="hero-title">Book Collection</h2>
                <p class="hero-sub">A compact shelf view with cleaner cards, faster scanning, and better spacing so your collection feels organized at a glance.</p>
            </div>

            <button type="button" id="openAddPanel" class="btn btn-primary add-btn">
                <i class="fa-solid fa-plus me-1"></i> Add New Collection
            </button>
        </article>

        @if (session('success'))
            <div class="alert alert-success mb-0">{{ session('success') }}</div>
        @endif

        <article id="addPanel" class="add-panel d-none">
            <h3 class="section-title mb-2">Add New Collection</h3>
            <form id="createCollectionForm" action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="cover-pick">
                            <p class="mb-2 fw-semibold">Book Cover</p>

                            <input type="file" id="cover_image" name="cover_image" accept="image/*" class="d-none" required>

                            <div class="upload-action-row">
                                <button type="button" id="pickImageBtn" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-image me-1"></i> Upload
                                </button>
                                <button type="button" id="cameraTriggerBtn" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-camera me-1"></i> Camera
                                </button>
                            </div>

                            <small class="text-muted d-block mt-2">Use Upload or Camera button to pick cover image.</small>

                            <img id="cover_preview" src="" alt="Cover preview" class="img-thumbnail mt-2 d-none" style="max-width: 160px; border-radius: 10px;">

                            <div class="upload-progress-container" id="coverProgressWrap">
                                <div class="progress">
                                    <div class="progress-bar" id="coverProgressBar" style="width: 0%"></div>
                                </div>
                                <div class="upload-status" id="coverStatusText">Uploading... 0%</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="book_name" class="form-label">Book Name</label>
                                <input type="text" id="book_name" name="book_name" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label for="author" class="form-label">Author</label>
                                <input type="text" id="author" name="author" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" id="createSubmitBtn" class="btn btn-primary">Save Collection</button>
                    <button type="button" id="cancelAddPanel" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </form>
        </article>

        <article class="library-controls">
            <form method="GET" action="{{ route('admin.collections.index') }}" class="row g-2">
                <div class="col-md-7">
                    <div class="search-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search by book name or author" value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="author" class="form-select" aria-label="Filter by author category">
                        <option value="" {{ $authorFilter === '' ? 'selected' : '' }}>All Categories</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author }}" {{ $authorFilter === $author ? 'selected' : '' }}>{{ $author }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary" title="Apply filters">
                        Search
                    </button>
                </div>
            </form>
        </article>

        <section class="library-shelf">
            <div class="shelf-head">
                <h3 class="shelf-title">Shelf View</h3>
                <span class="shelf-count">{{ $collections->total() }} books</span>
            </div>

            <div class="library-grid">
                @forelse ($collections as $collection)
                    <article class="book-card">
                    <div class="book-cover-wrap">
                        <img src="{{ asset('storage/' . $collection->cover_image_path) }}" alt="{{ $collection->book_name }}" class="book-cover">
                    </div>

                    <div class="book-body">
                        <h4 class="book-name">{{ $collection->book_name }}</h4>
                        <p class="book-meta book-author">{{ $collection->author }}</p>
                        <p class="book-meta book-date">Added {{ $collection->created_at->format('d M Y') }}</p>

                        <div class="card-actions">
                            <button type="button" class="icon-action edit-toggle" data-target="edit-panel-{{ $collection->id }}" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Delete this collection item?')" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-action danger" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>

                        <div id="edit-panel-{{ $collection->id }}" class="edit-panel d-none">
                            <form method="POST" action="{{ route('admin.collections.update', $collection) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-2">
                                    <label class="form-label small mb-1">Book Name</label>
                                    <input type="text" name="book_name" class="form-control form-control-sm" value="{{ $collection->book_name }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Author</label>
                                    <input type="text" name="author" class="form-control form-control-sm" value="{{ $collection->author }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Change Cover (optional)</label>
                                    <input type="file" name="cover_image" class="form-control form-control-sm edit-cover-input" accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                    </article>
                @empty
                    <div class="empty-library">
                        <strong class="d-block mb-1">No collection found</strong>
                        Add your first physical book using the Add New Collection button.
                    </div>
                @endforelse
            </div>
        </section>

        <div class="pagination-wrap">
            {{ $collections->links() }}
        </div>
    </section>

    <div id="cameraModal" class="camera-modal">
        <div class="camera-card">
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <strong><i class="fa-solid fa-camera me-1"></i> Capture Cover</strong>
                <button type="button" id="cameraCloseBtn" class="btn btn-sm btn-outline-secondary">Close</button>
            </div>

            <div class="camera-video-wrap">
                <video id="cameraVideo" class="camera-video" autoplay playsinline muted></video>
            </div>

            <div class="camera-controls">
                <button type="button" id="flipCameraBtn" class="btn btn-outline-secondary btn-sm" title="Switch Camera">
                    <i class="fa-solid fa-rotate"></i>
                </button>
                <button type="button" id="captureCoverBtn" class="camera-main-btn" title="Capture"></button>
                <span class="text-muted small">Tap center button</span>
            </div>
        </div>
    </div>

    <div id="cropModal" class="camera-modal">
        <div class="camera-card">
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <strong><i class="fa-solid fa-crop me-1"></i> Crop Cover</strong>
                <button type="button" id="cropCancelBtn" class="btn btn-sm btn-outline-secondary">Cancel</button>
            </div>

            <div class="crop-wrap">
                <img id="cropImage" src="" alt="Crop image" class="crop-image">
            </div>

            <div class="d-flex justify-content-center gap-2 p-3 border-top">
                <button type="button" id="cropConfirmBtn" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-check me-1"></i> Crop & Use
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addPanel = document.getElementById('addPanel');
            const openAddPanel = document.getElementById('openAddPanel');
            const cancelAddPanel = document.getElementById('cancelAddPanel');

            const form = document.getElementById('createCollectionForm');
            const submitBtn = document.getElementById('createSubmitBtn');
            const fileInput = document.getElementById('cover_image');
            const preview = document.getElementById('cover_preview');
            const pickImageBtn = document.getElementById('pickImageBtn');
            const editCoverInputs = document.querySelectorAll('.edit-cover-input');

            const progressWrap = document.getElementById('coverProgressWrap');
            const progressBar = document.getElementById('coverProgressBar');
            const statusText = document.getElementById('coverStatusText');

            const cameraModal = document.getElementById('cameraModal');
            const cameraVideo = document.getElementById('cameraVideo');
            const cameraTriggerBtn = document.getElementById('cameraTriggerBtn');
            const cameraCloseBtn = document.getElementById('cameraCloseBtn');
            const captureCoverBtn = document.getElementById('captureCoverBtn');
            const flipCameraBtn = document.getElementById('flipCameraBtn');

            const cropModal = document.getElementById('cropModal');
            const cropImage = document.getElementById('cropImage');
            const cropCancelBtn = document.getElementById('cropCancelBtn');
            const cropConfirmBtn = document.getElementById('cropConfirmBtn');

            let stream = null;
            let facingMode = 'environment';
            let cropper = null;
            let activeFileInput = fileInput;

            if (openAddPanel && addPanel) {
                openAddPanel.addEventListener('click', function () {
                    addPanel.classList.remove('d-none');
                    addPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            if (cancelAddPanel && addPanel) {
                cancelAddPanel.addEventListener('click', function () {
                    addPanel.classList.add('d-none');
                });
            }

            document.querySelectorAll('.edit-toggle').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const target = document.getElementById(btn.dataset.target);
                    if (!target) {
                        return;
                    }

                    const currentCard = btn.closest('.book-card');
                    const willOpen = target.classList.contains('d-none');

                    document.querySelectorAll('.edit-panel').forEach(function (panel) {
                        panel.classList.add('d-none');
                    });

                    document.querySelectorAll('.book-card.is-editing').forEach(function (card) {
                        card.classList.remove('is-editing');
                    });

                    if (willOpen) {
                        target.classList.remove('d-none');
                        if (currentCard) {
                            currentCard.classList.add('is-editing');
                        }
                    }
                });
            });

            if (pickImageBtn && fileInput) {
                pickImageBtn.addEventListener('click', function () {
                    fileInput.click();
                });
            }

            const renderPreview = function (file) {
                if (!preview) {
                    return;
                }

                if (!file) {
                    preview.src = '';
                    preview.classList.add('d-none');
                    return;
                }

                const url = URL.createObjectURL(file);
                preview.src = url;
                preview.classList.remove('d-none');
            };

            const openCropFromInput = function (inputEl, showPreview) {
                if (!inputEl) {
                    return;
                }

                const file = inputEl.files && inputEl.files[0];
                activeFileInput = inputEl;

                if (!file) {
                    if (showPreview) {
                        renderPreview(null);
                    }
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    openCropModal(event.target.result);
                };
                reader.readAsDataURL(file);
            };

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    openCropFromInput(fileInput, true);
                });
            }

            editCoverInputs.forEach(function (inputEl) {
                inputEl.addEventListener('change', function () {
                    openCropFromInput(inputEl, false);
                });
            });

            const destroyCropper = function () {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            };

            const closeCropModal = function () {
                destroyCropper();
                cropModal.style.display = 'none';
            };

            const openCropModal = function (src) {
                if (typeof Cropper === 'undefined') {
                    alert('Cropper could not be loaded. Please refresh and try again.');
                    return;
                }

                cropImage.src = src;
                cropModal.style.display = 'flex';

                destroyCropper();
                cropper = new Cropper(cropImage, {
                    aspectRatio: 3 / 4,
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                });
            };

            const stopCamera = function () {
                if (stream) {
                    stream.getTracks().forEach(function (track) { track.stop(); });
                    stream = null;
                }
                cameraVideo.srcObject = null;
            };

            const closeCamera = function () {
                stopCamera();
                cameraModal.style.display = 'none';
            };

            const startCamera = async function () {
                stopCamera();

                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: facingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 960 }
                    },
                    audio: false
                });

                cameraVideo.srcObject = stream;
            };

            if (cameraTriggerBtn) {
                cameraTriggerBtn.addEventListener('click', async function () {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Camera is not available in this browser.');
                        return;
                    }

                    try {
                        cameraModal.style.display = 'flex';
                        await startCamera();
                    } catch (error) {
                        alert('Camera access failed: ' + error.message);
                        closeCamera();
                    }
                });
            }

            if (cameraCloseBtn) {
                cameraCloseBtn.addEventListener('click', closeCamera);
            }

            if (flipCameraBtn) {
                flipCameraBtn.addEventListener('click', async function () {
                    facingMode = facingMode === 'environment' ? 'user' : 'environment';
                    try {
                        await startCamera();
                    } catch (error) {
                        alert('Unable to switch camera: ' + error.message);
                        closeCamera();
                    }
                });
            }

            if (captureCoverBtn) {
                captureCoverBtn.addEventListener('click', function () {
                    if (!stream) {
                        return;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = cameraVideo.videoWidth;
                    canvas.height = cameraVideo.videoHeight;
                    const ctx = canvas.getContext('2d');

                    if (facingMode === 'user') {
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                    }

                    ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);

                    closeCamera();
                    activeFileInput = fileInput;
                    openCropModal(canvas.toDataURL('image/jpeg', 0.95));
                });
            }

            if (cameraModal) {
                cameraModal.addEventListener('click', function (event) {
                    if (event.target === cameraModal) {
                        closeCamera();
                    }
                });
            }

            if (cropCancelBtn) {
                cropCancelBtn.addEventListener('click', function () {
                    closeCropModal();
                    if (activeFileInput) {
                        activeFileInput.value = '';
                    }
                    if (activeFileInput === fileInput) {
                        renderPreview(null);
                    }
                });
            }

            if (cropModal) {
                cropModal.addEventListener('click', function (event) {
                    if (event.target === cropModal) {
                        closeCropModal();
                    }
                });
            }

            if (cropConfirmBtn) {
                cropConfirmBtn.addEventListener('click', function () {
                    if (!cropper) {
                        return;
                    }

                    cropper.getCroppedCanvas({ width: 720, height: 960 }).toBlob(function (blob) {
                        if (!blob) {
                            return;
                        }

                        const file = new File([blob], 'collection-cover.jpg', { type: 'image/jpeg' });
                        const transfer = new DataTransfer();
                        transfer.items.add(file);

                        if (activeFileInput) {
                            activeFileInput.files = transfer.files;
                        }

                        if (activeFileInput === fileInput) {
                            renderPreview(file);
                        }

                        closeCropModal();
                    }, 'image/jpeg', 0.9);
                });
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const selectedFile = fileInput.files && fileInput.files[0];
                    if (!selectedFile) {
                        alert('Please upload or capture a cover image first.');
                        return;
                    }

                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();

                    submitBtn.disabled = true;
                    progressWrap.style.display = 'block';
                    progressBar.style.width = '0%';
                    progressBar.classList.remove('bg-danger');
                    progressBar.classList.add('bg-success');
                    statusText.textContent = 'Uploading... 0%';

                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    xhr.upload.onprogress = function (e) {
                        if (!e.lengthComputable) {
                            return;
                        }

                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percent + '%';
                        statusText.textContent = 'Uploading... ' + percent + '%';
                    };

                    xhr.onload = function () {
                        submitBtn.disabled = false;

                        if (xhr.status >= 200 && xhr.status < 300) {
                            progressBar.style.width = '100%';
                            statusText.textContent = 'Upload complete! Refreshing library...';
                            window.location.reload();
                            return;
                        }

                        progressBar.classList.remove('bg-success');
                        progressBar.classList.add('bg-danger');
                        statusText.textContent = 'Upload failed. Please check the form and try again.';
                    };

                    xhr.onerror = function () {
                        submitBtn.disabled = false;
                        progressBar.classList.remove('bg-success');
                        progressBar.classList.add('bg-danger');
                        statusText.textContent = 'Network error while uploading.';
                    };

                    xhr.send(formData);
                });
            }
        });
    </script>
@endpush
