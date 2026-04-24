@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<style>
/* ---------- GLASS THEME BASE ---------- */
:root {
    --glass-border: rgba(255, 255, 255, 0.1);
    --glass-bg: rgba(255, 255, 255, 0.05);
    --accent-color: #6366f1;
}

.ethereal-card {
    background: rgba(20, 20, 25, 0.6);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    animation: fadeInUp 0.6s cubic-bezier(.2,.8,.2,1) forwards;
}

.page-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.8rem;
    color: white;
    font-weight: 300;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--glass-border);
}

.form-label { color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px; }

/* --- FORM STYLES FIXED --- */
.form-glass {
    /* Critical: Use background-color to allow the dropdown arrow to show */
    background-color: var(--glass-bg); 
    border: 1px solid var(--glass-border);
    color: white;
    border-radius: 12px;
    padding: 12px 15px;
    font-size: 0.95rem;
    transition: all .3s ease;
}

/* FIX: Force Dropdown Options to be Dark so White text is visible */
.form-glass option {
    background-color: #18181b; 
    color: white;
}

/* FIX: Add a custom White Arrow for Select Inputs */
select.form-glass {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.form-glass:focus {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: var(--accent-color);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    outline: none;
    color: white;
}
.form-glass::placeholder { color: rgba(255, 255, 255, 0.3); }

/* Buttons */
.btn-glass {
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 500;
    transition: all .25s ease;
    border: 1px solid var(--glass-border);
    background: rgba(255, 255, 255, 0.03);
    color: #e6eefc;
}
.btn-glass:hover { background: rgba(255, 255, 255, 0.08); transform: translateY(-1px); }

/* Custom File Input Style */
.file-upload-wrapper { position: relative; cursor: pointer; }
.file-upload-label {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; cursor: pointer; text-align: left;
}
.file-name-span {
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: rgba(255, 255, 255, 0.5); max-width: 80%;
}

.btn-save {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white; border: none; width: 100%; padding: 14px;
    border-radius: 50px; font-weight: 600; letter-spacing: 0.5px;
    display: flex; justify-content: center; align-items: center; gap: 10px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); transition: 0.3s;
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4); }

/* ---------- CAMERA CONTROLS ---------- */
.camera-modal {
    position: fixed; inset: 0; display: none;
    align-items: center; justify-content: center;
    background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(8px); z-index: 2200;
}
.camera-card {
    width: 100%; max-width: 600px;
    background: #18181b; border-radius: 24px; padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    position: relative; display: flex; flex-direction: column; align-items: center;
}
.camera-video-wrapper {
    width: 100%; height: 480px;
    border-radius: 16px; overflow: hidden; position: relative;
    background: #000; margin-bottom: 20px; border: 1px solid rgba(255, 255, 255, 0.1);
}
.camera-video { 
    width: 100%; height: 100%; object-fit: cover;
    transform: scaleX(1); transition: transform 0.4s ease;
}
.camera-video.flipped { transform: scaleX(-1); }

.camera-controls {
    display: flex; align-items: center; justify-content: space-evenly;
    width: 100%; padding: 0 20px;
}
.icon-btn {
    width: 50px; height: 50px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
    color: white; cursor: pointer; transition: all 0.2s ease;
}
.icon-btn:hover { background: rgba(255, 255, 255, 0.2); transform: scale(1.1); }
.icon-btn svg { width: 22px; height: 22px; stroke-width: 2; }
.capture-main-btn {
    width: 80px; height: 80px; border-radius: 50%; background: transparent;
    border: 4px solid rgba(255, 255, 255, 0.8);
    display: flex; justify-content: center; align-items: center; cursor: pointer;
    transition: transform 0.2s; position: relative;
}
.capture-main-btn::after {
    content: ''; width: 64px; height: 64px; background: white; border-radius: 50%; transition: all 0.2s;
}
.capture-main-btn:hover { transform: scale(1.05); }
.capture-main-btn:active::after { transform: scale(0.9); background: #e2e2e2; }

.btn-camera-trigger {
    width: 45px; height: 45px; border-radius: 12px;
    background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); transition: all 0.2s;
}
.btn-camera-trigger:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4); }

.crop-container {
    width: 100%; height: 480px; background: #000; border-radius: 16px; overflow: hidden; margin-bottom: 20px;
}
#cropImage { max-width: 100%; }

.btn-confirm-crop {
    background: white; color: black; padding: 10px 25px; border-radius: 30px;
    font-weight: 600; border: none; display: flex; align-items: center; gap: 8px; transition: 0.2s;
}
.btn-confirm-crop:hover { transform: scale(1.05); box-shadow: 0 0 15px rgba(255,255,255,0.3); }

.photo-preview {
    width: 120px; height: 160px; object-fit: cover; border-radius: 12px;
    border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    display: {{ $student->photo_path ? 'block' : 'none' }}; margin-top: 15px;
}

/* Progress Bar Styles */
.upload-progress-container { margin-top: 10px; display: none; }
.progress { height: 6px; background-color: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; }
.progress-bar { background: #10b981; transition: width 0.3s ease; }
.upload-status { font-size: 0.75rem; margin-top: 4px; color: rgba(255,255,255,0.6); }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('students.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="ethereal-card">
            <h3 class="page-title">Edit Student Details</h3>

            <form id="studentEditForm" action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Hidden Inputs for Async Upload Paths --}}
                <input type="hidden" name="photo_path" id="uploadedPhotoPath" value="{{ $student->photo_path }}">
                <input type="hidden" name="aadhaar_path" id="uploadedAadhaarPath" value="{{ $student->aadhaar_path }}">

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input name="name" class="form-control form-glass" value="{{ $student->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Father's Name</label>
                        <input name="father_name" class="form-control form-glass" value="{{ $student->father_name }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control form-glass" value="{{ $student->dob }}">
                    </div>

                    {{-- UPDATED: Class/School Level Dropdown --}}
                    <div class="col-md-6">
                        <label class="form-label">Class / School Level</label>
                        <select name="class_level" class="form-select form-glass">
                            <option value="" disabled>Select Class</option>
                            @foreach(['NURSERY','KG-I','KG-II','STD-I','STD-II','STD-III','STD-V','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','OTHERS'] as $opt)
                                <option value="{{ $opt }}" @if($student->class_level == $opt) selected @endif>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input name="phone" class="form-control form-glass" value="{{ $student->phone }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Number</label>
                        <input name="whatsapp" class="form-control form-glass" value="{{ $student->whatsapp }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control form-glass" rows="2">{{ $student->address }}</textarea>
                    </div>

                    {{-- UPDATED: Institution Dropdown --}}
                    <div class="col-md-6">
                        <label class="form-label">Institution (MVKC / SSSP)</label>
                        <select name="institution" class="form-select form-glass">
                            <option value="" disabled>Select Institution</option>
                            <option value="MVKC" @if($student->institution == 'MVKC') selected @endif>MVKC</option>
                            <option value="SSSP" @if($student->institution == 'SSSP') selected @endif>SSSP</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assign Branch</label>
                        <select name="branch_id" class="form-select form-glass" required>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" @if($b->id == $student->branch_id) selected @endif>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12"><div style="border-top:1px solid rgba(255,255,255,0.08); margin:10px 0;"></div></div>

                    {{-- UPDATED: Dynamic Year Labels with Pre-Selection --}}
                    <div class="col-12">
                        <label class="form-label mb-3">Subjects & Year Levels</label>
                        <div id="subjectYearArea">
                            @foreach($student->subject_years ?? [] as $i => $sy)
                                @php
                                    // 1. Find Subject Name to check for "Computer"
                                    $subName = '';
                                    foreach($subjects as $s){
                                        if($s->id == $sy['subject_id']) { $subName = $s->name; break; }
                                    }
                                    // 2. Determine which options to show
                                    $isComputer = stripos($subName, 'COMPUTER') !== false;
                                    $yearOptions = $isComputer 
                                        ? ['DIPLOMA', 'BESIC', 'NULL'] 
                                        : ['PP-1', 'PP-2', 'PP-3', 'PR-1', '1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', 'KISHALAY-1', 'KISHALAY-2', 'SAHAJ PATH-1', 'SAHAJ PATH-2'];
                                @endphp

                                <div class="row mb-3 subject-year-row align-items-end">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block mb-1">Subject</small>
                                        <select name="subject_years[{{ $i }}][subject_id]" class="form-select form-glass subject-select" onchange="updateYearOptions(this)">
                                            @foreach($subjects as $s)
                                                <option value="{{ $s->id }}" @if($s->id == $sy['subject_id']) selected @endif>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block mb-1">Year Label</small>
                                        {{-- Render Correct Options via PHP for Initial Load --}}
                                        <select name="subject_years[{{ $i }}][year_label]" class="form-select form-glass year-select">
                                            @foreach($yearOptions as $opt)
                                                <option value="{{ $opt }}" @if($sy['year_label'] == $opt) selected @endif>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-remove removeRow" title="Remove">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-glass btn-add mt-2" id="addRow">
                            <i class="fa-solid fa-plus me-2"></i> Add Another Subject
                        </button>
                    </div>

                    <div class="col-12"><div style="border-top:1px solid rgba(255,255,255,0.08); margin:10px 0;"></div></div>

                    <div class="col-md-6">
                        <label class="form-label">Student Photo (Portrait 3:4)</label>
                        
                        <input id="photoInput" type="file" accept="image/*" style="display:none;">

                        <div class="d-flex gap-2 align-items-start">
                            <label for="photoInput" class="btn btn-glass file-upload-label flex-grow-1">
                                <span id="photoFileName" class="file-name-span">Change Photo</span>
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </label>
                             
                            <button type="button" id="openCamera" class="btn-camera-trigger" title="Open Camera">
                                <i class="fa-solid fa-camera" style="font-size: 18px;"></i>
                            </button>
                        </div>

                         <div class="upload-progress-container" id="photoProgressContainer">
                            <div class="progress">
                                <div class="progress-bar" id="photoProgressBar" style="width: 0%"></div>
                            </div>
                            <div class="upload-status" id="photoStatusText">Uploading... 0%</div>
                        </div>
                        
                        <div class="small-muted mt-2" style="color: rgba(255,255,255,0.5); font-size: 0.8rem;">
                            Upload or capture via camera. Both support cropping.
                        </div>
                        <img id="photoPreview" src="{{ $student->photo_path ? asset('storage/'.$student->photo_path) : '' }}" alt="preview" class="photo-preview">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Aadhaar Card Scan</label>
                        
                        <input id="aadhaarInput" type="file" accept="image/*" style="display:none;">
                        
                        <label for="aadhaarInput" class="btn btn-glass file-upload-label w-100">
                            <span id="aadhaarFileName" class="file-name-span">Change Aadhaar Scan</span>
                            <i class="fa-solid fa-file-arrow-up"></i>
                        </label>

                         <div class="upload-progress-container" id="aadhaarProgressContainer">
                            <div class="progress">
                                <div class="progress-bar" id="aadhaarProgressBar" style="width: 0%"></div>
                            </div>
                            <div class="upload-status" id="aadhaarStatusText">Uploading... 0%</div>
                        </div>

                        @if($student->aadhaar_path)
                            <div class="mt-2">
                                <a href="{{ asset('storage/'.$student->aadhaar_path) }}" target="_blank" class="text-decoration-none small text-muted">
                                    <i class="fa-solid fa-eye me-1"></i> View Current Aadhaar
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-glass">
                            <option value="active" @if($student->status=='active') selected @endif>Active</option>
                            <option value="inactive" @if($student->status=='inactive') selected @endif>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn-save">
                            <i class="fa-regular fa-floppy-disk me-2"></i> Save Updates
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modals for Camera and Crop (Unchanged) --}}
<div id="cameraModal" class="camera-modal">
    <div class="camera-card">
        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
            <span class="text-white fw-bold"><i class="fa-solid fa-camera me-2"></i>Take Photo</span>
            <span class="text-muted small">Make sure face is clear</span>
        </div>
        <div class="camera-video-wrapper">
            <video id="cameraVideo" class="camera-video" autoplay playsinline muted></video>
        </div>
        <div class="camera-controls">
            <button id="flipBtn" class="icon-btn" title="Switch Camera">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0-4.418-3.582-8-8-8s-8 3.582-8 8H2l5 6 5-6H7c0-2.761 2.239-5 5-5s5 2.239 5 5-2.239 5-5 5V17c3.866 0 7-3.134 7-7z"/>
                </svg>
            </button>
            <button id="captureBtn" class="capture-main-btn" title="Capture"></button>
            <button id="cameraClose" class="icon-btn" title="Close Camera">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</div>

<div id="cropModal" class="camera-modal">
    <div class="camera-card">
        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
            <span class="text-white fw-bold"><i class="fa-solid fa-crop me-2"></i>Crop Photo (3:4)</span>
        </div>
        <div class="crop-container">
            <img id="cropImage" src="">
        </div>
        <div class="d-flex justify-content-center gap-3 w-100">
            <button id="cropCancel" class="icon-btn" title="Cancel" style="border-radius:30px; width:auto; padding:0 20px;">
                <i class="fa-solid fa-xmark me-2"></i> Cancel
            </button>
            <button id="cropConfirm" class="btn-confirm-crop">
                <i class="fa-solid fa-check"></i> Crop & Upload
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
// ---------- Dynamic Dropdown Options ----------
const standardYears = [
    "PP-1", "PP-2", "PP-3", "PR-1", "1ST", "2ND", "3RD", "4TH", "5TH", "6TH", "7TH", 
    "KISHALAY-1", "KISHALAY-2", "SAHAJ PATH-1", "SAHAJ PATH-2"
];
const computerYears = ["DIPLOMA", "BESIC", "NULL"];

function updateYearOptions(subjectSelect) {
    const row = subjectSelect.closest('.subject-year-row');
    const yearSelect = row.querySelector('.year-select');
    
    // Check if selected subject is 'Computer' (case-insensitive)
    const selectedText = subjectSelect.options[subjectSelect.selectedIndex].text.toUpperCase();
    
    let options = standardYears;
    if (selectedText.includes("COMPUTER")) {
        options = computerYears;
    }

    // Reset dropdown and add options
    yearSelect.innerHTML = '<option value="" disabled selected>Select Year</option>';

    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.textContent = opt;
        yearSelect.appendChild(option);
    });
}

// NOTE: We do NOT run an automatic update on DOMContentLoaded here because 
// PHP has already rendered the correct selected options. 
// updateYearOptions is only needed when the user CHANGES the subject.

// ---------- Subject Row Logic ----------
document.getElementById('addRow').addEventListener('click', function() {
    const area = document.getElementById('subjectYearArea');
    const index = area.querySelectorAll('.subject-year-row').length;
    const row = document.createElement('div');
    row.classList.add('row','mb-3','subject-year-row','align-items-end');
    
    row.innerHTML = `
        <div class="col-md-6">
            <small class="text-muted d-block mb-1">Subject</small>
            <select name="subject_years[${index}][subject_id]" class="form-select form-glass subject-select" onchange="updateYearOptions(this)">
                <option value="" disabled selected>Select Subject</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <small class="text-muted d-block mb-1">Year Label</small>
            <select name="subject_years[${index}][year_label]" class="form-select form-glass year-select">
                <option value="" disabled selected>Select Year</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-remove removeRow" title="Remove"><i class="fa-solid fa-xmark"></i></button>
        </div>
    `;
    area.appendChild(row);
    // Initialize the new row
    updateYearOptions(row.querySelector('.subject-select'));
});

document.addEventListener('click', function(e){
    if (e.target.closest('.removeRow')) e.target.closest('.subject-year-row').remove();
});

// ---------- Camera & Cropper & Upload Logic (Identical to before) ----------
const cameraModal = document.getElementById('cameraModal');
const cropModal = document.getElementById('cropModal');
const cameraVideo = document.getElementById('cameraVideo');
const photoPreview = document.getElementById('photoPreview');
const photoInput = document.getElementById('photoInput');
const cropImage = document.getElementById('cropImage');
const openCameraBtn = document.getElementById('openCamera');
const captureBtn = document.getElementById('captureBtn');
const cameraClose = document.getElementById('cameraClose');
const flipBtn = document.getElementById('flipBtn');
const cropConfirm = document.getElementById('cropConfirm');
const cropCancel = document.getElementById('cropCancel');
let stream = null;
let facingMode = 'user';
let cropper = null;

openCameraBtn.addEventListener('click', async () => { cameraModal.style.display = 'flex'; await startCamera(); });
function closeCameraModal() { stopCamera(); cameraModal.style.display = 'none'; }
cameraClose.addEventListener('click', closeCameraModal);
flipBtn.addEventListener('click', async () => { facingMode = (facingMode === 'user') ? 'environment' : 'user'; await startCamera(); });

async function startCamera(){
    stopCamera();
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode, width: { ideal: 1280 }, height: { ideal: 1280 } }, audio: false });
        cameraVideo.srcObject = stream;
        if (facingMode === 'user') cameraVideo.classList.add('flipped');
        else cameraVideo.classList.remove('flipped');
    } catch (err) { alert('Camera access error: ' + err.message); cameraModal.style.display = 'none'; }
}
function stopCamera(){ if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; } cameraVideo.srcObject = null; }

captureBtn.addEventListener('click', () => {
    if(!stream) return;
    const canvas = document.createElement('canvas');
    canvas.width = cameraVideo.videoWidth;
    canvas.height = cameraVideo.videoHeight;
    const ctx = canvas.getContext('2d');
    if(facingMode === 'user') { ctx.translate(canvas.width, 0); ctx.scale(-1, 1); }
    ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
    stopCamera(); cameraModal.style.display = 'none'; openCropModal(canvas.toDataURL('image/jpeg'));
});

photoInput.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = (evt) => { openCropModal(evt.target.result); }
        reader.readAsDataURL(this.files[0]);
    }
});

function openCropModal(imageSrc) {
    cropImage.src = imageSrc;
    cropModal.style.display = 'flex';
    if(cropper) cropper.destroy();
    cropper = new Cropper(cropImage, { aspectRatio: 3 / 4, viewMode: 1, dragMode: 'move', background: false });
}

cropCancel.addEventListener('click', () => { if(cropper) cropper.destroy(); cropModal.style.display = 'none'; photoInput.value = ''; });

cropConfirm.addEventListener('click', () => {
    if(!cropper) return;
    cropper.getCroppedCanvas({ width: 600, height: 800 }).toBlob((blob) => {
        const file = new File([blob], "student-photo.jpg", { type: "image/jpeg" });
        photoPreview.src = URL.createObjectURL(blob); photoPreview.style.display = 'block';
        uploadFile(file, 'photo', 'photoProgressContainer', 'photoProgressBar', 'photoStatusText', 'uploadedPhotoPath');
    }, 'image/jpeg', 0.9);
    cropper.destroy(); cropModal.style.display = 'none';
});

document.getElementById('aadhaarInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) { updateFileName(this, 'aadhaarFileName'); uploadFile(file, 'aadhaar', 'aadhaarProgressContainer', 'aadhaarProgressBar', 'aadhaarStatusText', 'uploadedAadhaarPath'); }
});

function updateFileName(input, elementId) { document.getElementById(elementId).innerText = input.files[0] ? input.files[0].name : 'Choose File'; }

function uploadFile(file, type, containerId, barId, textId, hiddenInputId) {
    const container = document.getElementById(containerId); const bar = document.getElementById(barId); const text = document.getElementById(textId); const hiddenInput = document.getElementById(hiddenInputId);
    container.style.display = 'block'; bar.style.width = '0%'; bar.classList.remove('bg-danger'); bar.classList.add('bg-success'); text.innerText = "Uploading... 0%";
    const formData = new FormData(); formData.append('file', file); formData.append('_token', '{{ csrf_token() }}');
    const xhr = new XMLHttpRequest(); xhr.open('POST', '{{ route("upload.temp") }}', true);
    xhr.upload.onprogress = function(e) { if (e.lengthComputable) { const percentComplete = Math.round((e.loaded / e.total) * 100); bar.style.width = percentComplete + '%'; text.innerText = "Uploading... " + percentComplete + "%"; } };
    xhr.onload = function() { if (xhr.status === 200) { const response = JSON.parse(xhr.responseText); hiddenInput.value = response.path; text.innerText = "Upload Complete!"; text.style.color = '#10b981'; } else { text.innerText = "Upload Failed!"; text.style.color = '#ef4444'; bar.classList.remove('bg-success'); bar.classList.add('bg-danger'); } };
    xhr.onerror = function() { text.innerText = "Error uploading file."; };
    xhr.send(formData);
}
</script>

@endsection