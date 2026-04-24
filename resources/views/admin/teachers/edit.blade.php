@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap');

    .ethereal-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        color: var(--color-text);
        font-weight: 700;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid color-mix(in srgb, var(--color-border) 82%, transparent);
        letter-spacing: .01em;
    }

    .form-glass {
        border-radius: 12px;
    }

    .glass-checkbox-container {
        border-radius: 12px;
        padding: 15px;
        max-height: 150px;
        overflow-y: auto;
    }

    .form-check {
        margin-bottom: 8px;
    }

    .btn-save {
        width: 100%;
        padding: 12px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .file-upload-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .file-name-span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 82%;
    }

    .btn-camera-trigger {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
    }

    .photo-preview {
        width: 110px;
        height: 146px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 10px;
        border: 1px solid color-mix(in srgb, var(--color-border) 82%, transparent);
    }

    .upload-progress-container {
        margin-top: 10px;
        display: none;
    }

    .upload-status {
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .camera-modal {
        position: fixed;
        inset: 0;
        z-index: 2200;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(6px);
    }

    .camera-card {
        width: min(620px, 92vw);
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: #111827;
        padding: 14px;
    }

    .camera-video-wrap,
    .crop-wrap {
        width: 100%;
        height: min(70vh, 500px);
        border-radius: 12px;
        overflow: hidden;
        background: #000;
        margin-bottom: 12px;
    }

    .camera-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .camera-video.flipped {
        transform: scaleX(-1);
    }

    .camera-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: .7rem;
    }

    .icon-round {
        width: 44px;
        height: 44px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .capture-main-btn {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #fff;
        border: 4px solid rgba(255, 255, 255, .7);
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('teachers.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Teachers List
            </a>
        </div>

        <div class="ethereal-card">

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <h3 class="page-title">Edit Teacher Details</h3>

            <form id="teacherEditForm" method="POST" action="{{ route('teachers.update', $teacher->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="photo_path" id="uploadedPhotoPath" value="{{ $teacher->photo_path }}">
                <input type="hidden" name="aadhaar_path" id="uploadedAadhaarPath" value="{{ $teacher->aadhaar_path }}">

                <div class="row g-4">
                    
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input name="name" class="form-control form-glass" value="{{ $teacher->name }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Father's Name</label>
                        <input name="father_name" class="form-control form-glass" value="{{ $teacher->father_name }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input id="phoneInput" name="phone" type="tel" inputmode="numeric" maxlength="15" autocomplete="off" spellcheck="false" class="form-control form-glass" value="{{ old('phone', $teacher->phone) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input name="email" type="email" class="form-control form-glass" value="{{ $teacher->email }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">New Password</label>
                        <input name="password" type="password" class="form-control form-glass" placeholder="Leave blank to keep current password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teacher Photo (3:4)</label>
                        <input id="photoInput" type="file" accept="image/*" class="d-none">

                        <div class="d-flex gap-2 align-items-start">
                            <label for="photoInput" class="btn btn-glass file-upload-label flex-grow-1">
                                <span id="photoFileName" class="file-name-span">Choose Teacher Photo</span>
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </label>

                            <button type="button" id="openCamera" class="btn-camera-trigger" title="Open Camera">
                                <i class="fa-solid fa-camera"></i>
                            </button>
                        </div>

                        <div class="upload-progress-container" id="photoProgressContainer">
                            <div class="progress"><div class="progress-bar" id="photoProgressBar" style="width: 0%"></div></div>
                            <div class="upload-status" id="photoStatusText">Uploading... 0%</div>
                        </div>

                        <img id="photoPreview" src="{{ $teacher->photo_path ? asset('storage/' . $teacher->photo_path) : '' }}" alt="Teacher photo" class="photo-preview {{ $teacher->photo_path ? '' : 'd-none' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Aadhaar Card Scan</label>
                        <input id="aadhaarInput" type="file" accept="image/*,.pdf" class="d-none">

                        <label for="aadhaarInput" class="btn btn-glass file-upload-label w-100">
                            <span id="aadhaarFileName" class="file-name-span">{{ $teacher->aadhaar_path ? basename($teacher->aadhaar_path) : 'Choose Aadhaar Scan' }}</span>
                            <i class="fa-solid fa-file-arrow-up"></i>
                        </label>

                        <div class="upload-progress-container" id="aadhaarProgressContainer">
                            <div class="progress"><div class="progress-bar" id="aadhaarProgressBar" style="width: 0%"></div></div>
                            <div class="upload-status" id="aadhaarStatusText">Uploading... 0%</div>
                        </div>

                        @if ($teacher->aadhaar_path)
                            <a href="{{ asset('storage/' . $teacher->aadhaar_path) }}" target="_blank" class="small d-inline-block mt-2">View current Aadhaar</a>
                        @endif
                    </div>

                    <div class="col-12">
                        <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0;"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assigned Subjects</label>
                        <div class="glass-checkbox-container">
                            @foreach($subjects as $s)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="subjects[]" value="{{ $s->id }}" id="subject_{{ $s->id }}"
                                    {{-- Check if the ID exists in the teacher's subject array --}}
                                    @if(in_array($s->id, $teacher->subjects ?? [])) checked @endif>
                                    
                                    <label class="form-check-label" for="subject_{{ $s->id }}">
                                        {{ $s->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text mt-2"><i class="fa-solid fa-circle-check me-1"></i> Select/Deselect subjects.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assigned Branches</label>
                        <div class="glass-checkbox-container">
                            @foreach($branches as $b)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="branches[]" value="{{ $b->id }}" id="branch_{{ $b->id }}"
                                    {{-- Check if the ID exists in the teacher's branch array --}}
                                    @if(in_array($b->id, $teacher->branches ?? [])) checked @endif>
                                    
                                    <label class="form-check-label" for="branch_{{ $b->id }}">
                                        {{ $b->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text mt-2"><i class="fa-solid fa-location-dot me-1"></i> Select/Deselect branches.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Account Status</label>
                        <select id="statusSelect" name="status" class="form-select form-glass" data-original-status="{{ $teacher->status }}">
                            <option value="active" @if($teacher->status=='active') selected @endif>Active</option>
                            <option value="inactive" @if($teacher->status=='inactive') selected @endif>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <button class="btn btn-save">
                            <i class="fa-regular fa-floppy-disk"></i> Save Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<div id="cameraModal" class="camera-modal">
    <div class="camera-card">
        <div class="camera-video-wrap">
            <video id="cameraVideo" class="camera-video" autoplay playsinline muted></video>
        </div>
        <div class="camera-actions">
            <button type="button" id="flipBtn" class="icon-round" title="Switch"><i class="fa-solid fa-rotate"></i></button>
            <button type="button" id="captureBtn" class="capture-main-btn" title="Capture"></button>
            <button type="button" id="cameraClose" class="icon-round" title="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
</div>

<div id="cropModal" class="camera-modal">
    <div class="camera-card">
        <div class="crop-wrap">
            <img id="cropImage" src="" alt="Crop image" style="max-width:100%;">
        </div>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" id="cropCancel" class="btn btn-outline-light btn-sm">Cancel</button>
            <button type="button" id="cropConfirm" class="btn btn-light btn-sm">Crop & Upload</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tempUploadUrl = "{{ route('admin.upload.temp') }}";
    const csrf = '{{ csrf_token() }}';

    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoFileName = document.getElementById('photoFileName');
    const uploadedPhotoPath = document.getElementById('uploadedPhotoPath');

    const aadhaarInput = document.getElementById('aadhaarInput');
    const aadhaarFileName = document.getElementById('aadhaarFileName');
    const uploadedAadhaarPath = document.getElementById('uploadedAadhaarPath');

    const cameraModal = document.getElementById('cameraModal');
    const cameraVideo = document.getElementById('cameraVideo');
    const openCamera = document.getElementById('openCamera');
    const flipBtn = document.getElementById('flipBtn');
    const captureBtn = document.getElementById('captureBtn');
    const cameraClose = document.getElementById('cameraClose');

    const cropModal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const cropCancel = document.getElementById('cropCancel');
    const cropConfirm = document.getElementById('cropConfirm');

    let stream = null;
    let facingMode = 'user';
    let cropper = null;

    const teacherEditForm = document.getElementById('teacherEditForm');
    const phoneInput = document.getElementById('phoneInput');
    const statusSelect = document.getElementById('statusSelect');
    const assignedStudentsCount = Number("{{ (int) ($assignedStudentsCount ?? 0) }}");

    if (teacherEditForm && phoneInput) {
        teacherEditForm.addEventListener('submit', function (event) {
            phoneInput.value = phoneInput.value.replace(/\D/g, '').slice(0, 15);

            if (statusSelect && statusSelect.dataset.originalStatus !== 'inactive' && statusSelect.value === 'inactive' && assignedStudentsCount > 0) {
                const proceed = confirm('This teacher has ' + assignedStudentsCount + ' assigned student(s). You can still set inactive, but remember to transfer students from Admin > Students page. Continue?');
                if (!proceed) {
                    event.preventDefault();
                }
            }
        });
    }

    const uploadFile = function (file, progressWrapId, progressBarId, statusId, hiddenInput) {
        const wrap = document.getElementById(progressWrapId);
        const bar = document.getElementById(progressBarId);
        const status = document.getElementById(statusId);

        wrap.style.display = 'block';
        bar.style.width = '0%';
        status.textContent = 'Uploading... 0%';

        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', csrf);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', tempUploadUrl, true);

        xhr.upload.onprogress = function (e) {
            if (!e.lengthComputable) return;
            const percent = Math.round((e.loaded / e.total) * 100);
            bar.style.width = percent + '%';
            status.textContent = 'Uploading... ' + percent + '%';
        };

        xhr.onload = function () {
            if (xhr.status === 200) {
                const res = JSON.parse(xhr.responseText);
                hiddenInput.value = res.path;
                status.textContent = 'Upload complete';
                return;
            }
            status.textContent = 'Upload failed';
        };

        xhr.onerror = function () {
            status.textContent = 'Upload failed';
        };

        xhr.send(fd);
    };

    const stopCamera = function () {
        if (stream) {
            stream.getTracks().forEach(function (track) { track.stop(); });
            stream = null;
        }
        cameraVideo.srcObject = null;
    };

    const startCamera = async function () {
        stopCamera();
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: facingMode, width: { ideal: 1280 }, height: { ideal: 1280 } },
            audio: false,
        });
        cameraVideo.srcObject = stream;
        cameraVideo.classList.toggle('flipped', facingMode === 'user');
    };

    const openCropModal = function (src) {
        cropImage.src = src;
        cropModal.style.display = 'flex';
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImage, { aspectRatio: 3 / 4, viewMode: 1, dragMode: 'move', background: false });
    };

    photoInput.addEventListener('change', function () {
        const file = photoInput.files && photoInput.files[0];
        if (!file) return;
        photoFileName.textContent = file.name;
        const reader = new FileReader();
        reader.onload = function (event) { openCropModal(event.target.result); };
        reader.readAsDataURL(file);
    });

    aadhaarInput.addEventListener('change', function () {
        const file = aadhaarInput.files && aadhaarInput.files[0];
        if (!file) return;
        aadhaarFileName.textContent = file.name;
        uploadFile(file, 'aadhaarProgressContainer', 'aadhaarProgressBar', 'aadhaarStatusText', uploadedAadhaarPath);
    });

    openCamera.addEventListener('click', async function () {
        try {
            cameraModal.style.display = 'flex';
            await startCamera();
        } catch (err) {
            alert('Camera access error: ' + err.message);
            cameraModal.style.display = 'none';
        }
    });

    flipBtn.addEventListener('click', async function () {
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        await startCamera();
    });

    captureBtn.addEventListener('click', function () {
        if (!stream) return;
        const canvas = document.createElement('canvas');
        canvas.width = cameraVideo.videoWidth;
        canvas.height = cameraVideo.videoHeight;
        const ctx = canvas.getContext('2d');
        if (facingMode === 'user') {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
        stopCamera();
        cameraModal.style.display = 'none';
        openCropModal(canvas.toDataURL('image/jpeg'));
    });

    cameraClose.addEventListener('click', function () {
        stopCamera();
        cameraModal.style.display = 'none';
    });

    cropCancel.addEventListener('click', function () {
        if (cropper) cropper.destroy();
        cropModal.style.display = 'none';
    });

    cropConfirm.addEventListener('click', function () {
        if (!cropper) return;
        cropper.getCroppedCanvas({ width: 600, height: 800 }).toBlob(function (blob) {
            const file = new File([blob], 'teacher-photo.jpg', { type: 'image/jpeg' });
            photoPreview.src = URL.createObjectURL(blob);
            photoPreview.classList.remove('d-none');
            photoPreview.style.display = 'block';
            uploadFile(file, 'photoProgressContainer', 'photoProgressBar', 'photoStatusText', uploadedPhotoPath);
            photoFileName.textContent = file.name;
        }, 'image/jpeg', 0.9);

        cropper.destroy();
        cropModal.style.display = 'none';
    });
});
</script>

@endsection