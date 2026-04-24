<style>
    /* --- PARTIAL VIEW STYLES --- */
    .ajax-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Glass Table */
    .glass-table { width: 100%; border-collapse: collapse; color: #e2e8f0; }
    .glass-table thead th { text-align: left; padding: 15px; color: #94a3b8; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
    .glass-table tbody td { padding: 12px 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); vertical-align: middle; }
    .glass-table tbody tr:hover { background: rgba(255, 255, 255, 0.03); }

    /* Select Dropdown */
    .glass-select-sm { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: white; border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; width: 100%; cursor: pointer; transition: all 0.3s ease; }
    .glass-select-sm:focus { background: rgba(255, 255, 255, 0.1); border-color: #6366f1; outline: none; box-shadow: 0 0 10px rgba(99, 102, 241, 0.2); }
    .glass-select-sm option { background-color: #0f1115; color: white; padding: 10px; }

    /* Button */
    .btn-glow-success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 50px; padding: 12px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s ease; text-transform: uppercase; font-size: 0.85rem; display: flex; justify-content: center; align-items: center; gap: 10px; }
    .btn-glow-success:hover { background: #10b981; color: white; box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); transform: translateY(-2px); }
</style>

<div class="ajax-fade-in">

    {{-- Show Current Subject Context --}}
    @php
        $subjectName = \App\Models\Subject::find($subject_id)->name ?? 'Unknown Subject';
    @endphp
    
    <div class="mb-3 p-3 d-flex align-items-center justify-content-between" 
         style="background: rgba(99, 102, 241, 0.1); border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.2);">
        <div>
            <small class="text-white-50 d-block mb-1">Marking Attendance For</small>
            <span class="text-white fw-bold fs-5">
                <i class="fa-solid fa-book-open me-2" style="color: #6366f1;"></i>
                {{ $subjectName }}
            </span>
        </div>
        <span class="badge bg-dark border border-secondary text-light px-3 py-2 rounded-pill">
            {{ $students->count() }} Students
        </span>
    </div>

    @if($students->count() == 0)
        <div class="text-center p-5" style="border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
            <i class="fa-regular fa-folder-open fa-2x mb-3" style="color: #64748b;"></i>
            <p class="text-muted m-0">No students found for <strong>{{ $subjectName }}</strong> in this branch.</p>
        </div>

    @else

    <form action="{{ route('attendance.save') }}" method="POST">
        @csrf
        <input type="hidden" name="branch_id" value="{{ $branch_id }}">
        <input type="hidden" name="subject_id" value="{{ $subject_id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th width="200">Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $st)
                        @php
                            $prevStatus = strtolower(trim($attendance[$st->id]->status ?? 'present'));
                        @endphp
                        <tr>
                            <td style="color: white; font-weight: 500;">
                                <div class="d-flex align-items-center">
                                    <span style="display:inline-flex; width:35px; height:35px; 
                                                 background:rgba(255,255,255,0.1); border-radius:50%; 
                                                 align-items:center; justify-content:center; margin-right:12px; 
                                                 font-size:0.85rem; color:#cbd5e1; font-weight:600;">
                                        {{ strtoupper(substr($st->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="text-white">{{ $st->name }}</div>
                                        {{-- ID REMOVED HERE --}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select name="status[{{ $st->id }}]" class="glass-select-sm">
                                    <option value="Present" {{ $prevStatus == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="Absent"  {{ $prevStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button class="btn-glow-success w-100 mt-4">
            <i class="fa-solid fa-check-double"></i> Save Attendance Record
        </button>
    </form>
    @endif
</div>