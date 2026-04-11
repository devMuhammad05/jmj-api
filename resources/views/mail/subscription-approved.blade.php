<x-mail::message>
# Payment Approved

Hi **{{ $userName }}**,

Great news! Your payment has been approved and your **{{ $planName }}** subscription is now active.

<x-mail::panel>
**Subscription Details**

- **Plan:** {{ $planName }}
- **Start Date:** {{ $startsAt }}
- **End Date:** {{ $endsAt }}
</x-mail::panel>

You now have full access to all features included in the **{{ $planName }}** plan.

If you have any questions or need assistance, feel free to reach out to our support team.

Thanks,
**The {{ config('app.name') }} Team**
</x-mail::message>
