<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_name',
        'author',
        'book_type',
        'cover_image_path',
        'notes',
        'added_by',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
