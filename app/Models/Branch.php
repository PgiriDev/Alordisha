<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\User;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function students()
    {
        return $this->hasMany(Student::class, 'branch_id');
    }

    public function teachers()
    {
        return User::where('role', 'teacher')
                    ->whereJsonContains('branches', (string)$this->id);
    }
}