<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="row gy-3">
        <div class="col-12">
            <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
            <input wire:model="name" type="text" id="name" class="form-control" required autofocus autocomplete="name">
            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
            <input wire:model="email" type="email" id="email" class="form-control" required autocomplete="username">
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning mt-2 p-2 small">
                    {{ __('Your email address is unverified.') }}
                    <button wire:click.prevent="sendVerification" class="btn btn-link btn-sm text-decoration-underline">
                        {{ __('Click here to re-send verification email.') }}
                    </button>
                </div>
            @endif
        </div>

        <div class="col-12 d-flex justify-content-between align-items-center mt-2">
            <button class="btn btn-primary px-4">{{ __('Save Changes') }}</button>
            <span class="text-success small" wire:transition="fade" wire:loading.remove>{{ __('Saved.') }}</span>
        </div>
    </form>
</section>
