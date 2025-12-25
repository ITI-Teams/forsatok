@component('mail::message')
# Congratulations, {{ $user->name }}! 🎉

Your account has been **approved** and you can now access all features of our platform.

## Account Details
- **Email:** {{ $user->email }}
- **Account Type:** {{ ucfirst($user->type) }}
- **Approved On:** {{ now()->format('F j, Y') }}

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
Login to Your Account
@endcomponent

## What's Next?
Now that your account is approved, you can:
- Post job listings
- Manage applications
- Access your dashboard

If you have any questions, feel free to contact our support team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent