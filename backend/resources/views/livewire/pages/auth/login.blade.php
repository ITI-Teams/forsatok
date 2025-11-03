<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div>
    <h2 class="fw-bold mb-2">Login</h2>
    <p class="text-muted mb-4">Welcome back! Please login to your account.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Login Form -->
    <form wire:submit="login" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input wire:model="form.email" id="email" type="email"
                   class="form-control @error('form.email') is-invalid @enderror"
                   name="email" placeholder="username@gmail.com" required autofocus autocomplete="username">
            @error('form.email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input wire:model="form.password" id="password" type="password"
                   class="form-control @error('form.password') is-invalid @enderror"
                   name="password" required autocomplete="current-password">
            @error('form.password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input">
                <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none" wire:navigate>
                    {{ __('Forgot Password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="btn w-100 text-white py-2 fw-semibold"
                style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            {{ __('Log in') }}
        </button>
    </form>

    <div class="text-center mt-3">
        <small>{{ __("New user?") }}
            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none" wire:navigate>
                {{ __('Signup') }}
            </a>
        </small>
    </div>
</div>
