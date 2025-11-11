<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-4 text-primary mb-0">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="container py-5">
        <div class="row g-4">
            {{-- Profile Information --}}
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 bg-body-secondary">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-primary">
                            <i class="fa-solid fa-user me-2"></i>{{ __('Profile Information') }}
                        </h5>
                        <p class="text-muted small mb-4">
                            {{ __("Update your account's profile information and email address.") }}
                        </p>

                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 bg-body-secondary">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-primary">
                            <i class="fa-solid fa-lock me-2"></i>{{ __('Update Password') }}
                        </h5>
                        <p class="text-muted small mb-4">
                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                        </p>

                        <livewire:profile.update-password-form />
                    </div>
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-body-secondary">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-danger">
                            <i class="fa-solid fa-trash-can me-2"></i>{{ __('Delete Account') }}
                        </h5>
                        <p class="text-muted small mb-4">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please make sure you have saved any important information before continuing.') }}
                        </p>

                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
