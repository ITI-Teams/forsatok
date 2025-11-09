{{-- <x-app-layout> --}}
    <div class="">

        {{-- Success Message --}}
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Header --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h4 class="fw-semibold mb-0">
                <i class="fa-solid fa-user-tie text-primary me-2"></i> Employer Profile
            </h4>
            <a href="{{ route('employer.profile.edit') }}" class="btn btn-primary px-4">
                <i class="fa-solid fa-pen-to-square me-2"></i> Edit Profile
            </a>
        </div>

        {{-- Profile Card Full Width --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border rounded-4 bg-body text-body w-100">
                    <div class="card-body p-4">

                        {{-- Company Rating --}}
                        <div class="mb-5 text-center">
                            <h6 class="fw-bold mb-3 text-secondary">Company Rating</h6>
                            @php
                                $fullStars = (int) floor($average_rating ?? 0);
                                $hasHalf = (($average_rating ?? 0) - $fullStars) >= 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $hasHalf;
                            @endphp
                            <div class="d-flex align-items-center justify-content-center flex-wrap gap-1 mb-2">
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <i class="bi bi-star-fill text-warning fs-4"></i>
                                @endfor
                                @if ($hasHalf)
                                    <i class="bi bi-star-half text-warning fs-4"></i>
                                @endif
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="bi bi-star text-secondary fs-4"></i>
                                @endfor
                                <span class="ms-2 fw-semibold fs-6">{{ number_format($average_rating ?? 0, 1) }}
                                    ({{ $total_reviews ?? 0 }})</span>
                            </div>
                            @if(($total_reviews ?? 0) == 0)
                                <p class="text-muted small">No ratings yet</p>
                            @endif
                        </div>

                        {{-- Profile Info Grid --}}
                        <div class="row g-4">

                            @php
                                $profileItems = [
                                    ['label' => 'Company Name', 'value' => $company_name ?? 'N/A', 'icon' => 'fa-solid fa-building'],
                                    ['label' => 'Email', 'value' => $email ?? 'N/A', 'icon' => 'bi bi-envelope'],
                                    ['label' => 'Industry', 'value' => $industry ?? 'N/A', 'icon' => 'bi bi-briefcase'],
                                    ['label' => 'Location', 'value' => $location_display ?? 'N/A', 'icon' => 'bi bi-geo-alt'],
                                    ['label' => 'Website', 'value' => $website ?? 'N/A', 'icon' => 'bi bi-globe', 'is_link' => true],
                                    ['label' => 'About Company', 'value' => $about ?? 'No description provided.', 'icon' => 'fa-solid fa-info', 'is_textarea' => true],
                                ];
                            @endphp

                            @foreach($profileItems as $item)
                                <div class="{{ $item['label'] == 'About Company' ? 'col-12' : 'col-lg-6' }}">
                                    <div class="p-4 border rounded-4 shadow-sm bg-body text-body h-100 d-flex flex-column">
                                        <label
                                            class="text-muted small text-uppercase fw-semibold mb-2">{{ $item['label'] }}</label>
                                        @if(isset($item['is_link']) && $item['is_link'] && $item['value'] != 'N/A')
                                            <a href="{{ $item['value'] }}" target="_blank"
                                                class="text-decoration-none fw-semibold text-primary">
                                                <i class="{{ $item['icon'] }} me-2"></i>{{ $item['value'] }}
                                            </a>
                                        @elseif(isset($item['is_textarea']) && $item['is_textarea'])
                                            <p class="mb-0 text-muted" style="white-space: pre-wrap; line-height: 1.6;">
                                                {{ $item['value'] }}</p>
                                        @else
                                            <p class="mb-0 fs-6 fw-semibold"><i
                                                    class="{{ $item['icon'] }} text-primary me-2"></i>{{ $item['value'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        </div> <!-- End Grid -->

                    </div> <!-- End card-body -->
                </div> <!-- End card -->
            </div>
        </div>

    </div>
    {{-- </x-app-layout> --}}
