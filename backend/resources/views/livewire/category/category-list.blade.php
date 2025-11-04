<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-6">Categories</h1>

    @if (session()->has('message'))
        <div class="mb-4 text-green-600">{{ session('message') }}</div>
    @endif

    <a wire:navigate href="{{ route('categories.trash') }}" class="text-blue-600">🗑️ View Trash</a>

    <table class="w-full border mt-4">
        <thead>
        <tr class="bg-gray-200">
            <th class="px-3 py-2">Name</th>
            <th class="px-3 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td class="border px-3 py-2">{{ $category->name }}</td>
                <td class="border px-3 py-2 space-x-2">
                    <a wire:navigate href="{{ route('categories.edit', $category->id) }}" class="text-blue-600">Edit</a>
                    <button wire:click="delete({{ $category->id }})" class="text-red-600">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
