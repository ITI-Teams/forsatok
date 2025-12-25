@component('mail::message')
# Account Suspended

Dear {{ $user->name }},

We regret to inform you that your account has been **suspended** due to a policy violation.

## Reason for Suspension
@component('mail::panel')
{{ $reason }}
@endcomponent

## What This Means
- You will no longer be able to access your account
- All active listings have been deactivated
- Pending applications will not be processed

## Appeal Process
If you believe this action was taken in error, you may appeal by contacting our support team at **{{ $supportEmail }}**.

Please include:
- Your registered email address
- Any relevant information or documentation

We take policy violations seriously to maintain a safe and trustworthy platform for all users.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
