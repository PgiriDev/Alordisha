@extends('layouts.app')

@section('content')

<style>
    /* --- GLASS FORM STYLES --- */
    
    /* Card Container */
    .ethereal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    /* Typography */
    .page-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.8rem;
        color: white;
        font-weight: 300;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .form-label {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    /* Glass Inputs */
    .form-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-glass:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: #6366f1;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
        color: white;
        outline: none;
    }

    /* Submit Button */
    .btn-save {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.4);
        width: 100%;
        padding: 12px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    
    .btn-save:hover {
        background: #10b981;
        color: white;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        transform: translateY(-2px);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('branches.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Branches
            </a>
        </div>

        <div class="ethereal-card">
            
            <h3 class="page-title">Create New Branch</h3>

            <form method="POST" action="{{ route('branches.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label">Branch Name</label>
                    <input name="name" class="form-control form-glass" placeholder="e.g. Sridharanagar, Ramgangaa" required>
                </div>

                <button class="btn btn-save">
                    <i class="fa-solid fa-check"></i> Create Branch
                </button>

            </form>
            
        </div>
    </div>
</div>

@endsection
