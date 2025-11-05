<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-4">
                <div
                    class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center py-3 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-building me-2"></i> Employer Profile
                    </h5>
                    <a wire:navigate href="{{ route('employer.profile.edit') }}"
                        class="btn btn-light btn-sm rounded-pill px-3">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>

                <div class="card-body p-4">
                    {{-- Success Message --}}
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Rating Section --}}
                    <div class="mb-4 text-center">
                        <h6 class="fw-bold mb-2 text-secondary">Company Rating</h6>

                        @php
                            $fullStars = (int) floor($average_rating ?? 0);
                            $hasHalf = (($average_rating ?? 0) - $fullStars) >= 0.5 ? 1 : 0;
                            $emptyStars = 5 - $fullStars - $hasHalf;
                        @endphp

                        <div class="d-flex align-items-center justify-content-center">
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="bi bi-star-fill text-warning fs-4 mx-1"></i>
                            @endfor
                            @if ($hasHalf)
                                <i class="bi bi-star-half text-warning fs-4 mx-1"></i>
                            @endif
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="bi bi-star text-secondary fs-4 mx-1"></i>
                            @endfor
                            <span class="ms-2 fw-semibold fs-6 text-dark">
                                {{ number_format($average_rating ?? 0, 1) }} ({{ $total_reviews ?? 0 }})
                            </span>
                        </div>

                        @if(($total_reviews ?? 0) == 0)
                            <p class="text-muted small mt-2">
                                No ratings yet
                            </p>
                        @endif
                    </div>

                    {{-- Profile Info --}}
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small text-uppercase fw-semibold">Company Name</label>
                                <p class="mb-0 fs-5 fw-semibold">{{ $company_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small text-uppercase fw-semibold">Email</label>
                                <p class="mb-0"><i class="bi bi-envelope text-primary me-2"></i>{{ $email ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small text-uppercase fw-semibold">Industry</label>
                                <p class="mb-0"><i
                                        class="bi bi-briefcase text-primary me-2"></i>{{ $industry ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small text-uppercase fw-semibold">Location</label>
                                <p class="mb-0"><i class="bi bi-geo-alt text-primary me-2"></i>{{ $location ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small text-uppercase fw-semibold">Website</label>
                                <p class="mb-0">
                                    @if($website)
                                        <a href="{{ $website }}" target="_blank"
                                            class="text-decoration-none text-primary fw-semibold">
                                            <i class="bi bi-globe me-2"></i>{{ $website }}
                                        </a>
                                    @else
                                        <i class="bi bi-globe me-2 text-primary"></i>N/A
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="text-muted small text-uppercase fw-semibold">About Company</label>
                                <p class="mb-0 text-muted" style="white-space: pre-wrap; line-height: 1.8;">
                                    {{ $about ?? 'No description provided.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div> <!-- End card-body -->
            </div> <!-- End card -->
        </div>
    </div>
</div>
