@extends('layouts.app')

@section('title', 'Admin Notices')

@section('content')
    <section class="dashboard-grid">
        <article class="glass-card recent-card" style="grid-column: 1 / -1;">
            <h3 class="section-title">Post New Notice</h3>

            @if (session('success'))
                <div class="alert alert-success mt-3 mb-0">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.notices.store') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Notice Message</label>
                    <textarea id="message" name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="Write details...">{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="starts_at" class="form-label">Start Time (optional)</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}">
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="ends_at" class="form-label">End Time (optional)</label>
                        <input type="datetime-local" id="ends_at" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at') }}">
                        @error('ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label for="media" class="form-label">Media (PDF/Image/Audio)</label>
                    <input type="file" id="media" name="media" class="form-control @error('media') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.mp3,.wav,.ogg,.m4a,.aac">
                    @error('media')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-1">Max size: 10MB</small>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active on welcome page</label>
                </div>

                <button type="submit" class="btn btn-primary">Publish Notice</button>
            </form>
        </article>

        <article class="glass-card recent-card" style="grid-column: 1 / -1;">
            <h3 class="section-title">Published Notices</h3>

            <ul class="recent-list mt-3">
                @forelse ($notices as $notice)
                    <li class="recent-item">
                        <div>
                            <strong>{{ $notice->title }}</strong>
                            <span>{{ $notice->created_at->format('d M Y, h:i A') }} • {{ $notice->is_active ? 'Active' : 'Hidden' }}</span>
                            @if($notice->starts_at || $notice->ends_at)
                                <p class="mb-0 mt-1" style="color: var(--text-muted);">
                                    Schedule: {{ $notice->starts_at ? $notice->starts_at->format('d M Y h:i A') : 'Now' }}
                                    → {{ $notice->ends_at ? $notice->ends_at->format('d M Y h:i A') : 'No end' }}
                                </p>
                            @endif
                            @if($notice->message)
                                <p class="mb-0 mt-1" style="color: var(--text-muted);">{{ \Illuminate\Support\Str::limit($notice->message, 180) }}</p>
                            @endif
                            @if($notice->media_name)
                                <span class="pill mt-2 d-inline-block">Attachment: {{ $notice->media_name }}</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" onsubmit="return confirm('Delete this notice?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="recent-item">
                        <div>
                            <strong>No notices yet</strong>
                            <span>Post your first notification from the form above.</span>
                        </div>
                    </li>
                @endforelse
            </ul>

            <div class="mt-3">
                {{ $notices->links() }}
            </div>
        </article>
    </section>
@endsection
