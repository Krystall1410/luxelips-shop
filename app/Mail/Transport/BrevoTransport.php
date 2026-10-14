<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoTransport extends AbstractTransport
{
    protected string $key;

    public function __construct(string $key)
    {
        parent::__construct();
        $this->key = $key;
    }

    /**
     * Send the message using Brevo HTTP API (Port 443 HTTPS).
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $senderList = $email->getFrom();
        $sender = $senderList[0] ?? null;

        $fromAddress = $sender ? $sender->getAddress() : config('mail.from.address', 'hello@luxelips.com');
        $fromName = $sender ? ($sender->getName() ?: config('mail.from.name', 'LuxeLips')) : config('mail.from.name', 'LuxeLips');

        $recipients = [];
        foreach ($email->getTo() as $recipient) {
            $recipients[] = [
                'email' => $recipient->getAddress(),
                'name' => $recipient->getName() ?: null,
            ];
        }

        $payload = [
            'sender' => [
                'name' => $fromName,
                'email' => $fromAddress,
            ],
            'to' => $recipients,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() ?: nl2br((string) $email->getTextBody()),
        ];

        $response = Http::withHeaders([
            'api-key' => $this->key,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(15)->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            $errorMessage = 'Brevo API Error [' . $response->status() . ']: ' . $response->body();
            Log::error($errorMessage);
            throw new \Exception($errorMessage);
        }

        Log::info("Brevo email sent successfully to: " . implode(', ', array_column($recipients, 'email')));
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
