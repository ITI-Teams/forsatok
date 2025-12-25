@component('mail::message')
# Account Application Update

Dear {{ $userName }},

We regret to inform you that your account application has been **declined** after review.

## Reason for Rejection
@component('mail::panel')
{{ $reason }}
@endcomponent

## What Can You Do?
If you believe this was made in error or have additional information to provide, you may:

1. **Re-apply:** You can submit a new application with updated information.
2. **Contact Support:** Reach out to us at {{ $supportEmail }} for clarification.

We appreciate your interest in our platform and encourage you to address the concerns mentioned above before reapplying.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
