<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $teacher;
    public string $adminName;
    public string $logoSrc;

    public function __construct(User $teacher, string $adminName)
    {
        $this->teacher = $teacher;
        $this->adminName = $adminName;
        $this->logoSrc = $this->resolveLogoSrc();
    }

    public function build()
    {
        return $this
            ->subject('Welcome to Alor Disha')
            ->view('emails.teacher-welcome');
    }

    private function resolveLogoSrc(): string
    {
        $preferred = public_path('Mail logo.png');
        $fallback = public_path('logo.png');
        $legacy = public_path('alordish stamp.png');
        $path = file_exists($preferred)
            ? $preferred
            : (file_exists($fallback) ? $fallback : $legacy);

        if (!file_exists($path)) {
            return asset('logo.png');
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = $ext === 'gif' ? 'image/gif' : ($ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png');
        $data = base64_encode((string) file_get_contents($path));

        return 'data:' . $mime . ';base64,' . $data;
    }
}
