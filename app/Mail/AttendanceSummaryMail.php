<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $summary;
    public User $teacher;
    public ?string $pdfBinary;
    public ?string $pdfFileName;
    public string $logoSrc;

    public function __construct(array $summary, User $teacher, ?string $pdfBinary = null, ?string $pdfFileName = null)
    {
        $this->summary = $summary;
        $this->teacher = $teacher;
        $this->pdfBinary = $pdfBinary;
        $this->pdfFileName = $pdfFileName;
        $this->logoSrc = $this->resolveLogoSrc();
    }

    public function build()
    {
        $mail = $this
            ->subject('Attendance Submitted - ' . ($this->summary['date'] ?? now()->format('Y-m-d')))
            ->view('emails.attendance-summary');

        if (!empty($this->pdfBinary) && !empty($this->pdfFileName)) {
            $mail->attachData($this->pdfBinary, $this->pdfFileName, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
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
