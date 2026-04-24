<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Mail\TeacherWelcomeMail;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')
            ->orderBy('name')
            ->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $branches = Branch::all();
        return view('admin.teachers.create', compact('subjects', 'branches'));
    }

    public function store(Request $r)
    {
        $normalizedPhone = preg_replace('/\D+/', '', (string) $r->input('phone', ''));
        $r->merge(['phone' => $normalizedPhone]);

        $r->validate([
            'name' => 'required',
            'phone' => ['required', 'min:7', 'max:15', Rule::unique('users', 'phone')],
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'photo_path' => 'nullable|string',
            'aadhaar_path' => 'nullable|string',
            'subjects' => 'nullable|array',
            'branches' => 'nullable|array',
        ]);

        $photoPath = $r->filled('photo_path')
            ? $this->moveFileFromTemp($r->photo_path, 'teachers/photos')
            : null;

        $aadhaarPath = $r->filled('aadhaar_path')
            ? $this->moveFileFromTemp($r->aadhaar_path, 'teachers/aadhaar')
            : null;

        $teacher = User::create([
            'name' => $r->name,
            'father_name' => $r->father_name,
            'phone' => $r->phone,
            'whatsapp' => $r->whatsapp,
            'email' => $r->email,
            'address' => $r->address,
            'photo_path' => $photoPath,
            'aadhaar_path' => $aadhaarPath,
            'role' => 'teacher',
            'password' => Hash::make($r->password),
            'status' => $r->status ?? 'active',

            // FIX → convert to integers
            'subjects' => $r->subjects ? array_map('intval', $r->subjects) : [],
            'branches' => $r->branches ? array_map('intval', $r->branches) : [],
        ]);

        if (!empty($teacher->email)) {
            $teacherId = (int) $teacher->id;
            $adminName = session('currentUser')->name ?? 'Admin';

            dispatch(function () use ($teacherId, $adminName) {
                try {
                    $freshTeacher = User::find($teacherId);

                    if (!$freshTeacher || empty($freshTeacher->email)) {
                        return;
                    }

                    Mail::to($freshTeacher->email)->send(new TeacherWelcomeMail($freshTeacher, $adminName));
                } catch (\Throwable $e) {
                    report($e);
                }
            })->afterResponse();
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function edit($id)
    {
        $teacher = User::findOrFail($id);
        $subjects = Subject::all();
        $branches = Branch::all();
        $assignedStudentsCount = Student::where('teacher_id', $teacher->id)->count();

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'branches', 'assignedStudentsCount'));
    }

    public function update(Request $r, $id)
    {
        $teacher = User::findOrFail($id);

        $normalizedPhone = preg_replace('/\D+/', '', (string) $r->input('phone', ''));
        $r->merge(['phone' => $normalizedPhone]);

        $r->validate([
            'name' => 'required',
            'phone' => [
                'required',
                'min:7',
                'max:15',
                Rule::unique('users', 'phone')->ignore($teacher->id),
            ],
            'email' => "nullable|email|unique:users,email,$id",
            'photo_path' => 'nullable|string',
            'aadhaar_path' => 'nullable|string',
            'subjects' => 'nullable|array',
            'branches' => 'nullable|array',
        ]);

        $teacher->name = $r->name;
        $teacher->father_name = $r->father_name;
        $teacher->phone = $r->phone;
        $teacher->whatsapp = $r->whatsapp;
        $teacher->email = $r->email;
        $teacher->address = $r->address;

        if ($r->filled('photo_path') && $r->photo_path !== $teacher->photo_path) {
            if (!empty($teacher->photo_path)) {
                Storage::disk('public')->delete($teacher->photo_path);
            }

            $teacher->photo_path = $this->moveFileFromTemp($r->photo_path, 'teachers/photos');
        }

        if ($r->filled('aadhaar_path') && $r->aadhaar_path !== $teacher->aadhaar_path) {
            if (!empty($teacher->aadhaar_path)) {
                Storage::disk('public')->delete($teacher->aadhaar_path);
            }

            $teacher->aadhaar_path = $this->moveFileFromTemp($r->aadhaar_path, 'teachers/aadhaar');
        }

        if ($r->filled('password')) {
            $teacher->password = Hash::make($r->password);
        }

        $teacher->status = $r->status ?? 'active';

        $assignedStudentsCount = Student::where('teacher_id', $teacher->id)->count();
        $isSwitchingToInactive = ($teacher->getOriginal('status') !== 'inactive') && ($teacher->status === 'inactive');

        // FIX → convert to integers
        $teacher->subjects = $r->subjects ? array_map('intval', $r->subjects) : [];
        $teacher->branches = $r->branches ? array_map('intval', $r->branches) : [];

        $teacher->save();

        if ($isSwitchingToInactive && $assignedStudentsCount > 0) {
            return redirect()->route('teachers.index')
                ->with('warning', 'Teacher set to inactive. ' . $assignedStudentsCount . ' assigned student(s) may need transfer from Students menu.');
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        $teacher = User::findOrFail($id);

        if (!empty($teacher->photo_path)) {
            Storage::disk('public')->delete($teacher->photo_path);
        }

        if (!empty($teacher->aadhaar_path)) {
            Storage::disk('public')->delete($teacher->aadhaar_path);
        }

        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    public function uploadTempFile(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $path = $request->file('file')->store('temp', 'public');

        return response()->json(['path' => $path]);
    }

    private function moveFileFromTemp(string $tempPath, string $targetFolder): string
    {
        if (str_starts_with($tempPath, 'temp/') && Storage::disk('public')->exists($tempPath)) {
            $newPath = $targetFolder . '/' . basename($tempPath);
            Storage::disk('public')->move($tempPath, $newPath);

            return $newPath;
        }

        return $tempPath;
    }
}
