<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
};
?>

<div>
    <h2 class="fw-bold mb-2">Forgot Password</h2>
    <p class="text-muted mb-4">Enter your email to receive a password reset link.</p>

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

    <form wire:submit="sendPasswordResetLink" class="needs-validation" novalidate>
        <div class="mb-3">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="username@gmail.com" required autofocus>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn w-100 text-white fw-semibold py-2"
                style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            {{ __('Send Reset Link') }}
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
