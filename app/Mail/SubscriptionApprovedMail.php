<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Subscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Payment Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.subscription-approved',
            with: [
                'userName' => $this->subscription->user->full_name,
                'planName' => $this->subscription->plan->name,
                'startsAt' => $this->subscription->starts_at->toFormattedDateString(),
                'endsAt' => $this->subscription->ends_at->toFormattedDateString(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
