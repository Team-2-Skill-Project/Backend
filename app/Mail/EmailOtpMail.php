<?php

namespace App\Mail;

use App\Models\EmailOtp;
use Carbon\CarbonInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EmailOtpMail extends Mailable
{
    public function __construct(public string $code, public string $purpose, public CarbonInterface $expiresAt) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->purpose === EmailOtp::PASSWORD_RESET
            ? 'SkillMatch password reset code' : 'SkillMatch email verification code');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.email-otp');
    }
}
