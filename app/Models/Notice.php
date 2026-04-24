<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'media_path',
        'media_name',
        'media_mime',
        'media_size',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function scopeVisible($query)
    {
        $noticeTimezone = config('app.notice_timezone', 'Asia/Dhaka');
        $now = now($noticeTimezone);

        return $query
            ->where('is_active', true)
            ->where(function ($subQuery) use ($now) {
                $subQuery->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($subQuery) use ($now) {
                $subQuery->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function getHasMediaAttribute(): bool
    {
        return !empty($this->media_path);
    }
}
