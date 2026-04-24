<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $meta;
    public string $logoSrc;

    public function __construct(User $user, array $meta = [])
    {
        $this->user = $user;
        $this->meta = $meta;
        $this->logoSrc = $this->resolveLogoSrc();
    }

    public function build()
    {
        return $this
            ->subject('Login Successful - Alor Disha')
            ->view('emails.login-alert');
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
