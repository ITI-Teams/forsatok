<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Success Messages -->
            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('password_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('password_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('reset_link_message'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('reset_link_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Profile Information Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-badge me-2"></i>Edit Employer Profile
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form wire:submit.prevent="updateProfile" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model="company_name"
                                       class="form-control form-control-lg @error('company_name') is-invalid @enderror"
                                       placeholder="Enter company name">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" wire:model="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       placeholder="company@example.com" readonly>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Industry</label>
                                <input type="text" wire:model="industry"
                                       class="form-control form-control-lg @error('industry') is-invalid @enderror"
                                       placeholder="e.g., Technology, Healthcare">
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location</label>
                                <input type="text" wire:model="location"
                                       class="form-control form-control-lg @error('location') is-invalid @enderror"
                                       placeholder="City, Country">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Website</label>
                                <input type="url" wire:model="website"
                                       class="form-control form-control-lg @error('website') is-invalid @enderror"
                                       placeholder="https://www.example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">About Company</label>
                                <textarea wire:model="about"
                                          class="form-control @error('about') is-invalid @enderror"
                                          rows="5"
                                          placeholder="Tell us about your company..."></textarea>
                                @error('about')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a wire:navigate href="{{ route('employer.profile') }}"
                               class="btn btn-outline-secondary btn-lg px-4">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Reset Card -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-secondary text-white rounded-top-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock me-2"></i>Password Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Change Password Section -->
                    <div class="mb-4">
                        <button type="button"
                                wire:click="$set('showPasswordSection', true)"
                                class="btn btn-outline-primary"
                                @if($showPasswordSection) style="display: none;" @endif>
                            <i class="bi bi-key me-2"></i>Change Password
                        </button>

                        @if($showPasswordSection)
                            <form wire:submit.prevent="updatePassword" class="border rounded p-4 bg-light" novalidate>
                                <h6 class="mb-3 fw-semibold">Change Password</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Current Password</label>
                                    <input type="password" wire:model="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           placeholder="Enter current password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password" wire:model="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Enter new password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirm New Password</label>
                                    <input type="password" wire:model="password_confirmation"
                                           class="form-control"
                                           placeholder="Confirm new password">
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check me-2"></i>Update Password
                                    </button>
                                    <button type="button"
                                            wire:click="$set('showPasswordSection', false)"
                                            class="btn btn-outline-secondary">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>

                    <!-- Reset Password Link -->
                    <div class="border-top pt-3">
                        <h6 class="fw-semibold mb-2">Forgot Your Password?</h6>
                        <p class="text-muted small mb-3">Click the button below to receive a password reset link via email.</p>
                        <button type="button" wire:click="sendPasswordResetLink"
                                class="btn btn-outline-info">
                            <i class="bi bi-envelope me-2"></i>Send Password Reset Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
