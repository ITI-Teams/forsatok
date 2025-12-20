<?php

use App\Domains\Shared\Services\Audit\AuditLogger;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        app(AuditLogger::class)->log([
            'action' => 'login',
            'user' => auth()->user(),
            'model' => auth()->user(),
            'changes' => ['message' => 'User logged in', 'User' => auth()->user()->name,]
        ]);

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
                class="form-control @error('form.email') is-invalid @enderror" name="email"
                placeholder="username@gmail.com" required autofocus autocomplete="username">
            @error('form.email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input wire:model="form.password" id="password" type="password"
                class="form-control @error('form.password') is-invalid @enderror" name="password" required
                autocomplete="current-password">
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

        <button type="submit" class="btn w-100 text-white py-2 fw-semibold mb-2"
            style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            {{ __('Log in') }}
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
        <small>{{ __("New user?") }}
            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none" wire:navigate>
                {{ __('Signup') }}
            </a>
        </small>
    </div>
</div>