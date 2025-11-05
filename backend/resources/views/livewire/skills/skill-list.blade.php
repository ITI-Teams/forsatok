<div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-dark">Skills</h1>
        <a href="{{ route('skills.trash') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-trash"></i> View Trash
        </a>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <input type="text" wire:model.live="search" class="form-control w-50"
               placeholder="🔍 Search for skill...">

        <a href="{{ route('skills.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Skill
        </a>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0 text-secondary fw-semibold">All Skills</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                         <th class="px-4 py-3">Category</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($skills as $skill)
                        <tr>
                            <td class="px-4 py-3">{{ $skill->name }}</td>
                            <td class="px-4 py-3">
                                {{ $skill->category ? $skill->category->name : '—' }}
                            </td>
                            <td class="text-center px-4 py-3">
                                <a href="{{ route('skills.edit', $skill->id) }}" 
                                   class="btn btn-sm btn-primary px-3">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <button wire:click="delete({{ $skill->id }})"
                                        class="btn btn-sm btn-danger px-3 ms-2">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">No skills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
