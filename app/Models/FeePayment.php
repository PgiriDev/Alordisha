<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'branch_id',
        'year_label',
        'month',
        'year',
        'amount',
        'receipt_no',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];
}
