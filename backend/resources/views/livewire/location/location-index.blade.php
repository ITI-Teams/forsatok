<div class="container-fluid py-3" data-bs-theme="auto">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-location-dot me-2"></i>Location Management
        </h4>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'countries' ? 'active' : '' }}"
                    wire:click="setTab('countries')"
                    type="button"
                    role="tab">
                <i class="fa-solid fa-globe me-2"></i>Countries
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'cities' ? 'active' : '' }}"
                    wire:click="setTab('cities')"
                    type="button"
                    role="tab">
                <i class="fa-solid fa-city me-2"></i>Cities
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        @if($activeTab === 'countries')
            <livewire:location.country-list />
        @else
            <livewire:location.city-list />
        @endif
    </div>
</div>

