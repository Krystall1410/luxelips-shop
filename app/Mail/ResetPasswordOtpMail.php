<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otpCode;
    public string $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otpCode, string $recipientName = 'Khách hàng')
    {
        $this->otpCode = $otpCode;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã OTP khôi phục mật khẩu tài khoản LuxeLips PRO',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password_otp',
            with: [
                'otpCode' => $this->otpCode,
                'recipientName' => $this->recipientName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
