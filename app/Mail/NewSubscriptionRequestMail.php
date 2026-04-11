<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubscriptionRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Subscription Request Awaiting Approval',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-subscription-request',
            with: [
                'userName' => $this->payment->user->full_name,
                'userEmail' => $this->payment->user->email,
                'planName' => $this->payment->plan->name,
                'amount' => $this->payment->amount,
                'gateway' => $this->payment->gateway?->name,
                'reference' => $this->payment->reference,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
