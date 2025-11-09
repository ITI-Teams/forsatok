{{-- <x-app-layout> --}}
    <div class="">

        <div class="row justify-content-center">
            <div class="col-lg-12">

                {{-- Page Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-semibold mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i>Edit Employer Profile
                    </h4>
                    <a wire:navigate href="{{ route('employer.profile') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Back to Profile
                    </a>
                </div>

                {{-- Profile Update Card --}}
                <div class="card shadow-lg border-0 rounded-4 mb-4 w-100 bg-body text-body">
                    <div class="card-body p-4">
                        <form wire:submit.prevent="updateProfile" novalidate>
                            <div class="row g-3">

                                {{-- Company Name --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-building text-primary me-2"></i>Company Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" wire:model="company_name"
                                        class="form-control form-control-lg @error('company_name') is-invalid @enderror"
                                        placeholder="Enter company name">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-envelope text-primary me-2"></i>Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" wire:model="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        placeholder="company@example.com" readonly>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Industry --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-briefcase text-primary me-2"></i>Industry
                                    </label>
                                    <input type="text" wire:model="industry"
                                        class="form-control form-control-lg @error('industry') is-invalid @enderror"
                                        placeholder="e.g., Technology, Healthcare">
                                    @error('industry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Country --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-globe text-primary me-2"></i>Country
                                    </label>
                                    <select wire:model.live="country_id" class="form-select form-control-lg pb-3 @error('country_id') is-invalid @enderror">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- City --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-city text-primary me-2"></i>City
                                    </label>
                                    <select wire:model="city_id" class="form-select form-control-lg pb-3 @error('city_id') is-invalid @enderror" @if(!$country_id) disabled @endif>
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Address --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-map-marker-alt text-primary me-2"></i>Address
                                    </label>
                                    <input type="text" wire:model="address"
                                        class="form-control form-control-lg @error('address') is-invalid @enderror"
                                        placeholder="Street, Building, Office">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Website --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-globe text-primary me-2"></i>Website
                                    </label>
                                    <input type="url" wire:model="website"
                                        class="form-control form-control-lg @error('website') is-invalid @enderror"
                                        placeholder="https://www.example.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- About Company --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-info text-primary me-2"></i>About Company
                                    </label>
                                    <textarea wire:model="about"
                                        class="form-control @error('about') is-invalid @enderror" rows="5"
                                        placeholder="Tell us about your company..."></textarea>
                                    @error('about')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-circle me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Password Settings Card --}}
                <div class="card shadow-lg border-0 rounded-4 w-100 bg-body text-body">
                    <div class="card-header bg-secondary text-white rounded-top-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-shield-lock me-2"></i>Password Settings
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        {{-- Change Password Toggle --}}
                        <div class="mb-4">
                            <button type="button" wire:click="$set('showPasswordSection', true)"
                                class="btn btn-outline-primary" @if($showPasswordSection) style="display: none;" @endif>
                                <i class="bi bi-key me-2"></i>Change Password
                            </button>

                            @if($showPasswordSection)
                                <form wire:submit.prevent="updatePassword" class="border rounded p-4 bg-light" novalidate>
                                    <h6 class="mb-3 fw-semibold">Change Password</h6>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fa-solid fa-lock text-primary me-2"></i>Current Password
                                        </label>
                                        <input type="password" wire:model="current_password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            placeholder="Enter current password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fa-solid fa-key text-primary me-2"></i>New Password
                                        </label>
                                        <input type="password" wire:model="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Enter new password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fa-solid fa-key text-primary me-2"></i>Confirm New Password
                                        </label>
                                        <input type="password" wire:model="password_confirmation" class="form-control"
                                            placeholder="Confirm new password">
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check me-2"></i>Update Password
                                        </button>
                                        <button type="button" wire:click="$set('showPasswordSection', false)"
                                            class="btn btn-outline-secondary">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>

                        {{-- Reset Password Link --}}
                        <div class="border-top pt-3 mt-3">
                            <h6 class="fw-semibold mb-2">Forgot Your Password?</h6>
                            <p class="text-muted small mb-3">Click the button below to receive a password reset link via
                                email.</p>
                            <button type="button" wire:click="sendPasswordResetLink" class="btn btn-outline-info">
                                <i class="bi bi-envelope me-2"></i>Send Password Reset Link
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- </x-app-layout> --}}
