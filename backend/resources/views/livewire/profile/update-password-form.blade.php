<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="row gy-3">
        <div class="col-12">
            <label class="form-label fw-semibold">{{ __('Current Password') }}</label>
            <input wire:model="current_password" type="password" class="form-control" autocomplete="current-password">
            @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">{{ __('New Password') }}</label>
            <input wire:model="password" type="password" class="form-control" autocomplete="new-password">
            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" type="password" class="form-control" autocomplete="new-password">
            @error('password_confirmation') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 d-flex justify-content-between align-items-center mt-2">
            <button class="btn btn-primary px-4">{{ __('Update Password') }}</button>
            <span class="text-success small" wire:transition="fade" on="password-updated">{{ __('Saved.') }}</span>
        </div>
    </form>
</section>
