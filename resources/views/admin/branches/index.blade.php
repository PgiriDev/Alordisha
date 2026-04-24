@extends('layouts.app')

@section('content')

<style>
    /* --- PAGE-SPECIFIC STYLES --- */

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

    .page-header {
        font-family: 'Outfit', sans-serif;
        font-weight: 300;
        font-size: 2rem;
        color: white;
        margin-bottom: 0;
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        padding: 10px 24px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-glass:hover { transform: translateY(-2px); }

    .btn-glass-primary {
        background: rgba(99, 102, 241, 0.2);
        border-color: rgba(99, 102, 241, 0.4);
        color: #e0e7ff;
    }
    
    .btn-glass-primary:hover {
        background: #6366f1;
        color: white;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        color: #e2e8f0;
    }

    .modern-table thead th {
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
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 0.95rem;
        vertical-align: middle; /* Ensures icons and text align vertically */
    }

    .modern-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    /* --- NEW BADGE STYLES FOR COUNTS --- */
    .glass-badge {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-width: 40px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        backdrop-filter: blur(4px);
    }

    .badge-info {
        background: rgba(56, 189, 248, 0.1); /* Sky Blue tint */
        color: #38bdf8;
        border: 1px solid rgba(56, 189, 248, 0.2);
    }

    .badge-success {
        background: rgba(52, 211, 153, 0.1); /* Emerald tint */
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.2);
    }

    /* Action Buttons */
    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        color: #94a3b8;
        transition: 0.3s;
    }

    .btn-edit:hover { background: rgba(96,165,250,0.2); color: #60a5fa; }
    .btn-delete:hover { background: rgba(248,113,113,0.2); color: #f87171; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="page-header">Branches</h3>
    
    <a href="{{ route('branches.create') }}" class="btn-glass btn-glass-primary">
        <i class="fa-solid fa-plus"></i> Add Branch
    </a>
</div>


<div class="ethereal-card">
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th width="40%">Branch Name</th>
                    
                    <th width="20%" class="text-center">Teachers</th>
                    <th width="20%" class="text-center">Students</th>
                    
                    <th width="20%" class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($branches as $b)
                <tr>

                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-code-branch me-3" style="color: rgba(255,255,255,0.3);"></i>
                            <span class="fw-medium text-white">{{ $b->name }}</span>
                        </div>
                    </td>

                    <td class="text-center">
                        <span class="glass-badge badge-info">
                            {{ $b->teachers_count }}
                        </span>
                    </td>

                    <td class="text-center">
                        <span class="glass-badge badge-success">
                            {{ $b->students_count }}
                        </span>
                    </td>

                    <td class="text-end">
                        <a href="{{ route('branches.edit', $b->id) }}" class="action-btn btn-edit" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form action="{{ route('branches.destroy', $b->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this branch?')" class="action-btn btn-delete" title="Delete">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <i class="fa-solid fa-network-wired fa-3x mb-3" style="color: rgba(255,255,255,0.1);"></i>
                        <p class="text-muted m-0">No branches found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

@endsection