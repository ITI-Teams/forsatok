<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login', navigate: true);
    }
};
?>

<div>
    <h2 class="fw-bold mb-2">Reset Password</h2>
    <p class="text-muted mb-4">Enter your new password below.</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

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

    <form wire:submit="resetPassword" class="needs-validation" novalidate>
        <input type="hidden" wire:model="token">

        <div class="mb-3">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="username@gmail.com" required autofocus>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">{{ __('New Password') }}</label>
            <input wire:model="password" id="password" type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Enter new password" required>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirmation">{{ __('Confirm New Password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   placeholder="Confirm your new password" required>
            @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn w-100 text-white fw-semibold py-2"
                style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            {{ __('Reset Password') }}
        </button>
    </form>

    <div class="text-center mt-3">
        <small>{{ __('Remembered your password?') }}
            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" wire:navigate>
                {{ __('Login') }}
            </a>
        </small>
    </div>
</div>
