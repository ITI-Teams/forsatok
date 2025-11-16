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
            <input wire:model="name" id="name" type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="John Doe" required autofocus autocomplete="name">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="username@gmail.com" required autocomplete="username">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <input wire:model="password" id="password" type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit"
                class="btn w-100 text-white fw-semibold py-2"
                style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            {{ __('Register') }}
        </button>
    </form>

    <div class="text-center mt-3">
        <small>{{ __("Already have an account?") }}
            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" wire:navigate>
                {{ __('Login') }}
            </a>
        </small>
    </div>
</div>
