<x-mail::message>
# SkillMatch

{{ $purpose === \App\Models\EmailOtp::PASSWORD_RESET ? 'Reset your password' : 'Verify your email address' }}

Your verification code is:

<x-mail::panel>
{{ $code }}
</x-mail::panel>

This code expires in 10 minutes ({{ $expiresAt->format('H:i T') }}).

Do not share this code with anyone. If you did not request it, you can ignore this email.

The SkillMatch team
</x-mail::message>
