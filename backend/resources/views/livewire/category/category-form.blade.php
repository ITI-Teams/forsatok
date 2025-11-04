<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">
            {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
        </h1>

        @if (session()->has('message'))
            <div class="mb-4 text-green-600 font-semibold">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save">

            <!-- Category Name -->
            <div class="mb-4">
                <label class="block text-gray-700">Name</label>
                <input type="text" wire:model.defer="name"
                    class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300">
                @error('name') 
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-gray-700">Description</label>
                <textarea wire:model.defer="description"
                    class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-300"
                    rows="4"></textarea>
                @error('description') 
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    {{ $categoryId ? 'Update' : 'Create' }}
                </button>

                <button type="button"
                    wire:click="$reset"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
