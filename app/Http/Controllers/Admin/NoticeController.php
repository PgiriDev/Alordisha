<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::query()->latest()->paginate(12);

        return view('admin.notices.index', compact('notices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:4000'],
            'media' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,gif,mp3,wav,ogg,m4a,aac'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $timezone = config('app.notice_timezone', 'Asia/Dhaka');

        $noticeData = [
            'title' => $validated['title'],
            'message' => $validated['message'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'starts_at' => isset($validated['starts_at'])
                ? Carbon::parse($validated['starts_at'], $timezone)->toDateTimeString()
                : null,
            'ends_at' => isset($validated['ends_at'])
                ? Carbon::parse($validated['ends_at'], $timezone)->toDateTimeString()
                : null,
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $path = $file->store('notices', 'public');

            $noticeData['media_path'] = $path;
            $noticeData['media_name'] = $file->getClientOriginalName();
            $noticeData['media_mime'] = $file->getClientMimeType();
            $noticeData['media_size'] = $file->getSize();
        }

        Notice::create($noticeData);

        return redirect()->route('admin.notices.index')->with('success', 'Notice published successfully.');
    }

    public function destroy(Notice $notice)
    {
        if ($notice->media_path && Storage::disk('public')->exists($notice->media_path)) {
            Storage::disk('public')->delete($notice->media_path);
        }

        $notice->delete();

        return redirect()->route('admin.notices.index')->with('success', 'Notice deleted successfully.');
    }
}
