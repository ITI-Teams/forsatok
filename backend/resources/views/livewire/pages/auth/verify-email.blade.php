<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
};
?>

<div class="text-center">
    <h2 class="fw-bold mb-3">Verify Your Email</h2>
    <p class="text-muted mb-4">
        Thanks for signing up! Please verify your email address by clicking the link we just sent to your inbox.
        <br>
        Didn’t receive the email? You can request another below.
    </p>

    <!-- Status Message -->
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success py-2">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-flex justify-content-center gap-2 mt-3">
        <button wire:click="sendVerification" type="button"
                class="btn text-white fw-semibold px-4"
                style="background: linear-gradient(90deg, #6a11cb, #2575fc);">
            Resend Email
        </button>

        <button wire:click="logout" type="button" class="btn btn-outline-secondary">
            Logout
        </button>
    </div>
</div>
