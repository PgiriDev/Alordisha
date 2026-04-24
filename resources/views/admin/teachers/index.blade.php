@extends('layouts.app')

@section('content')

<style>
    /* --- PAGE-SPECIFIC STYLES --- */

    /* Glass Container */
    .ethereal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    /* Page Header */
    .page-header {
        font-family: 'Outfit', sans-serif;
        font-weight: 300;
        font-size: 2rem;
        color: white;
        margin-bottom: 0;
    }

    /* Glass Buttons */
    .btn-glass {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        padding: 10px 24px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-glass:hover {
        transform: translateY(-2px);
    }

    .btn-glass-primary {
        background: rgba(99, 102, 241, 0.2); /* Indigo tint */
        border-color: rgba(99, 102, 241, 0.4);
        color: #e0e7ff;
    }
    
    .btn-glass-primary:hover {
        background: #6366f1;
        color: white;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }

    /* Modern Table */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        color: #e2e8f0;
    }

    .modern-table thead th {
        text-align: left;
        padding: 18px 20px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-weight: 600;
    }

    .modern-table tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 0.95rem;
    }

    .modern-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges (Pills) */
    .glass-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-right: 4px;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }

    .badge-purple {
        background: rgba(168, 85, 247, 0.15);
        color: #d8b4fe;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .badge-blue {
        background: rgba(56, 189, 248, 0.15);
        color: #7dd3fc;
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    /* Action Icon Buttons */
    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: none;
        background: transparent;
        color: #94a3b8;
    }

    .btn-edit:hover { background: rgba(96, 165, 250, 0.2); color: #60a5fa; }
    .btn-delete:hover { background: rgba(248, 113, 113, 0.2); color: #f87171; }

    /* Animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 fade-up">
    <h3 class="page-header">Teachers</h3>
    
    <a href="{{ route('teachers.create') }}" class="btn-glass btn-glass-primary">
        <i class="fa-solid fa-plus"></i> Add Teacher
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
@endif

@if (session('warning'))
    <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
@endif

<div class="ethereal-card">
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th width="20%">Name</th>
<th width="15%">Phone</th>
<th width="20%">Subjects</th>
<th width="15%">Branches</th>
<th width="10%">Status</th>   <!-- NEW -->
<th width="20%" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $t)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if (!empty($t->photo_path))
                                    <img
                                        src="{{ asset('storage/' . ltrim($t->photo_path, '/')) }}"
                                        alt="{{ $t->name }}"
                                        style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 12px; border: 1px solid rgba(148, 163, 184, 0.35);"
                                    >
                                @else
                                    <div style="width: 35px; height: 35px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; color: #cbd5e1; font-size: 0.8rem;">
                                        {{ substr($t->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="fw-medium text-white">{{ $t->name }}</span>
                            </div>
                        </td>

                        <td style="color: #94a3b8;">{{ $t->phone }}</td>

                        <td>
                            @foreach($t->subjectNames() as $sub)
                                <span class="glass-badge badge-purple">{{ $sub }}</span>
                            @endforeach
                        </td>

                        <td>
                            @foreach($t->branchNames() as $branch)
                                <span class="glass-badge badge-blue">{{ $branch }}</span>
                            @endforeach
                        </td>
                        <td>
    @if($t->status == 'active')
        <span class="glass-badge" style="
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        ">Active</span>

    @else
        <span class="glass-badge" style="
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        ">Inactive</span>
    @endif
</td>
                        <td class="text-end">
                            <a href="{{ route('teachers.edit', $t->id) }}" class="action-btn btn-edit" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('teachers.destroy', $t->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete Teacher?')" class="action-btn btn-delete" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fa-solid fa-chalkboard-user fa-3x mb-3" style="color: rgba(255,255,255,0.1);"></i>
                            <p class="text-muted m-0">No teachers found in the system.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection