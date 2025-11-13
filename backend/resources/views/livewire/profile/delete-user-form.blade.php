<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>
<div>
    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="fa-solid fa-trash-can me-1"></i> {{ __('Delete Account') }}
    </button>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form wire:submit="deleteUser" class="modal-content bg-body-secondary">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">{{ __('Confirm Account Deletion') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted small">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
                    </p>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">{{ __('Password') }}</label>
                        <input wire:model="password" type="password" class="form-control" placeholder="{{ __('Enter your password') }}">
                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
