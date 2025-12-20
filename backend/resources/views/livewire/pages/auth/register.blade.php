<?php

use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Shared\Services\Audit\AuditLogger;
use App\Domains\Users\Models\User;
use App\Events\UserRegistered;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['type'] = 'employer';

        $user = User::create($validated);

        $user->assignRole('employer');

        EmployerInfo::create([
            'user_id' => $user->id,
            'company_name' => '',
        ]);


        event(new Registered($user));


        event(new UserRegistered($user));
        $admins = User::role('admin')->get();
        Notification::send($admins, new NewUserRegisteredNotification($user));

        Auth::login($user);
        app(AuditLogger::class)->log([
            'action' => 'Create New account',
            'user' => auth()->user(),
            'model' => auth()->user(),
            'changes' => ['message' => 'A new account has been created', 'User' => $user->name,]
        ]);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div>
    <h2 class="fw-bold mb-2">Register</h2>
    <p class="text-muted mb-4">Create your account now!</p>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Register Form -->
    <form wire:submit="register" class="needs-validation" novalidate>
        <div class="mb-3">
            <label class="form-label" for="name">{{ __('Full Name') }}</label>
            <input wire:model="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                placeholder="John Doe" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                placeholder="username@gmail.com" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <input wire:model="password" id="password" type="password"
                class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror" required
                autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn w-100 text-white fw-semibold py-2 mb-2"
            style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            {{ __('Register') }}
        </button>

        <a href="{{ route('auth.google', ['source' => 'livewire', 'type' => 'employer']) }}"
            class="btn btn-outline-danger w-100 py-2 fw-semibold d-flex align-items-center justify-content-center">
            <svg class="me-2" width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" />
                <path fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" />
            </svg>
            {{ __('Continue with Google') }}
        </a>
    </form>

    <div class="text-center mt-3">
        <small>{{ __("Already have an account?") }}
            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" wire:navigate>
                {{ __('Login') }}
            </a>
        </small>
    </div>
</div>