<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">🗑️ Trash</h1>

    @if (session()->has('message'))
        <div class="mb-4 text-green-600">{{ session('message') }}</div>
    @endif

    <a wire:navigate href="{{ route('categories.index') }}" class="text-blue-600">⬅ Back to Categories</a>

    <table class="w-full border mt-4">
        <thead>
        <tr class="bg-gray-200">
            <th class="px-3 py-2">Name</th>
            <th class="px-3 py-2">Deleted At</th>
            <th class="px-3 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($trashedCategories as $category)
            <tr>
                <td class="border px-3 py-2">{{ $category->name }}</td>
                <td class="border px-3 py-2">{{ $category->deleted_at->diffForHumans() }}</td>
                <td class="border px-3 py-2 space-x-2">
                    <button wire:click="restore({{ $category->id }})" class="text-green-600">Restore</button>
                    <button wire:click="forceDelete({{ $category->id }})" class="text-red-600">Delete Permanently</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
