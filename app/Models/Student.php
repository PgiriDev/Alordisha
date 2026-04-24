<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_name',
        'dob',
        'class_level',
        'phone',
        'whatsapp',
        'address',
        'institution',
        'registration_number',
        'photo_path',
        'aadhaar_path',
        'subject_years',
        'branch_id',
        'teacher_id',
        'status',
    ];

    protected $casts = [
        'subject_years' => 'array',
    ];

    // Student belongs to a teacher (User)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Student belongs to a branch
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // Student has many attendance records
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function whatsappRecipients()
    {
        return $this->hasMany(WhatsAppRecipient::class, 'student_id');
    }
}
