<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div>
    <h2 class="fw-bold mb-2 text-center">Confirm Password</h2>
    <p class="text-muted mb-4 text-center">
        This is a secure area of the application.<br>
        Please confirm your password before continuing.
    </p>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="confirmPassword" class="mt-3">
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input wire:model="password"
                   type="password"
                   id="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Enter your password"
                   required
                   autocomplete="current-password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit"
                class="btn w-100 text-white fw-semibold mt-2"
                style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            Confirm
        </button>
    </form>

    <div class="text-center mt-3">
        <small>
            <a href="{{ route('auth.login') }}" class="text-decoration-none">
                Back to Login
            </a>
        </small>
    </div>
</div>
