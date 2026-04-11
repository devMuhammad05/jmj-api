<x-mail::message>
# New Subscription Request

A user has submitted a new subscription request and is awaiting your approval.

<x-mail::panel>
**Request Details**

- **User:** {{ $userName }} ({{ $userEmail }})
- **Plan:** {{ $planName }}
- **Amount:** ${{ number_format($amount, 2) }}
- **Gateway:** {{ $gateway ?? 'N/A' }}
- **Reference:** {{ $reference }}
</x-mail::panel>

Please log in to the admin panel to review and approve or reject this request.

Thanks,
**The {{ config('app.name') }} Team**
</x-mail::message>
