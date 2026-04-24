<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','father_name','phone','whatsapp','email','address','photo_path','aadhaar_path','role','password','status','subjects','branches'
    ];

    protected $hidden = ['password'];

    protected $casts = [
    'branches' => 'array',
    'subjects' => 'array',
];

    public function students()
    {
        return $this->hasMany(Student::class, 'teacher_id');
    }

    public function whatsappCampaigns()
    {
        return $this->hasMany(WhatsAppCampaign::class, 'teacher_id');
    }

    public function whatsappRecipients()
    {
        return $this->hasMany(WhatsAppRecipient::class, 'teacher_id');
    }

    /* -------------------------------------------------
     * RETURN SUBJECT NAMES FROM JSON [1,2,3]
     * ------------------------------------------------- */
    public function subjectNames()
    {
        $ids = $this->subjects ?? [];

        return \App\Models\Subject::whereIn('id', $ids)->pluck('name')->toArray();
    }

    /* -------------------------------------------------
     * RETURN BRANCH NAMES FROM JSON [1,2]
     * ------------------------------------------------- */
    public function branchNames()
    {
        $ids = $this->branches ?? [];

        return \App\Models\Branch::whereIn('id', $ids)->pluck('name')->toArray();
    }
}
