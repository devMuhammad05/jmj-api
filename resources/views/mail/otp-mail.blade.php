<x-mail::message>
# Email OTP Verification

Below is your One-Time Passcode (OTP) required to complete your authentication.
This code will expire in **10 minutes**.
**Do not share this code with anyone** for your account's safety.

<p style="text-align: center; background-color: rgba(100, 98, 98, 0.425); color: #ffffff; padding: 5px; font-size: 18px; letter-spacing: 2px;">
    <b>{{ $otp }}</b>
</p>

Stay safe,
**The {{ config('app.name') }} Team**
</x-mail::message>
