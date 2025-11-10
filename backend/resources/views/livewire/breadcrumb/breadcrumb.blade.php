<div class="breadcrumb-container mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            {{-- Optional fixed home/dashboard item --}}
            <li class="breadcrumb-item">
                <a wire:navigate href="{{ route('dashboard') ?? url('/') }}">Dashboard</a>
            </li>

            @foreach($segments as $segment)
                @if($segment['url'])
                    <li class="breadcrumb-item">
                        <a wire:navigate href="{{ $segment['url'] }}">{{ $segment['label'] }}</a>
                    </li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $segment['label'] }}
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>
