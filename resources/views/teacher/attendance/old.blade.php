<style>
    /* --- PARTIAL VIEW STYLES --- */
    .ajax-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Glass Table */
    .glass-table { width: 100%; border-collapse: collapse; color: #e2e8f0; }
    .glass-table thead th { text-align: left; padding: 15px; color: #94a3b8; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
    .glass-table tbody td { padding: 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); vertical-align: middle; }
    .glass-table tbody tr:hover { background: rgba(255, 255, 255, 0.03); }

    /* Glowing Badges */
    .badge-glass { padding: 6px 16px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; display: inline-block; min-width: 80px; text-align: center; }
    .badge-glass-present { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); box-shadow: 0 0 10px rgba(16, 185, 129, 0.1); }
    .badge-glass-absent { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); box-shadow: 0 0 10px rgba(239, 68, 68, 0.1); }
    .badge-glass-other { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3); }
    
    /* Summary Badge */
    .summary-pill { font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); margin-left: 10px; }
</style>

<div class="ajax-fade-in">

    {{-- Show Context Header with Stats --}}
    @if($records->count() > 0)
        @php
            // Get Context Info
            $firstRecord = $records->first();
            $subjectName = $firstRecord->subject->name ?? 'Unknown Subject';
            $branchName = $firstRecord->branch->name ?? 'Unknown Branch';
            $recordDate = \Carbon\Carbon::parse($firstRecord->date)->format('d M, Y');

            // CALCULATE TOTALS
            $totalPresent = $records->filter(fn($r) => strtolower($r->status) == 'present')->count();
            $totalAbsent = $records->filter(fn($r) => strtolower($r->status) == 'absent')->count();
        @endphp

        <div class="mb-4 p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" 
             style="background: rgba(99, 102, 241, 0.1); border-radius: 16px; border: 1px solid rgba(99, 102, 241, 0.2);">
            
            {{-- Left: Title & Branch --}}
            <div>
                <small class="text-white-50 d-block mb-1">Attendance Record For</small>
                <div class="d-flex align-items-center">
                    <span class="text-white fw-bold fs-5 me-2">
                        <i class="fa-solid fa-book-open me-2" style="color: #6366f1;"></i>
                        {{ $subjectName }}
                    </span>
                    <span class="text-white-50 mx-2">|</span>
                    <span class="text-light small">{{ $branchName }}</span>
                </div>
            </div>

            {{-- Right: Stats & Date --}}
            <div class="d-flex flex-column align-items-end">
                <span class="badge bg-dark border border-secondary text-light px-3 py-2 rounded-pill mb-2">
                    <i class="fa-regular fa-calendar me-2"></i> {{ $recordDate }}
                </span>
                
                <div class="d-flex">
                    <span class="summary-pill" style="color: #34d399;">
                        <i class="fa-solid fa-check-circle"></i> {{ $totalPresent }} Present
                    </span>
                    <span class="summary-pill" style="color: #f87171;">
                        <i class="fa-solid fa-circle-xmark"></i> {{ $totalAbsent }} Absent
                    </span>
                </div>
            </div>
        </div>
    @endif

    @if($records->count() == 0)
        <div class="text-center p-5" style="border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
            <i class="fa-regular fa-calendar-xmark fa-2x mb-3" style="color: #94a3b8;"></i>
            <p class="text-muted m-0">No attendance records found for these criteria.</p>
        </div>
    @else

        <div class="table-responsive">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th width="150" class="text-center">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($records as $rec)
                    @php
                        $status = strtolower(trim($rec->status));
                    @endphp

                    <tr>
                        <td style="color: white; font-weight: 500;">
                            <div class="d-flex align-items-center">
                                <span style="display:inline-flex; width:35px; height:35px; 
                                             background:rgba(255,255,255,0.1); border-radius:50%; 
                                             align-items:center; justify-content:center; margin-right:12px; 
                                             font-size:0.85rem; color:#cbd5e1; font-weight:600;">
                                    {{ strtoupper(substr($rec->student->name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="text-white">{{ $rec->student->name }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            @if($status == 'present')
                                <span class="badge-glass badge-glass-present">Present</span>
                            @elseif($status == 'absent')
                                <span class="badge-glass badge-glass-absent">Absent</span>
                            @else
                                <span class="badge-glass badge-glass-other">{{ $rec->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

</div>