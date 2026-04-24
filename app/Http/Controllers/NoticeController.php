<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    public function download(Notice $notice)
    {
        $isVisible = Notice::query()->visible()->whereKey($notice->id)->exists();

        abort_unless($isVisible && $notice->media_path, 404);
        abort_unless(Storage::disk('public')->exists($notice->media_path), 404);

        return response()->download(
            Storage::disk('public')->path($notice->media_path),
            $notice->media_name ?? basename($notice->media_path)
        );
    }
}
