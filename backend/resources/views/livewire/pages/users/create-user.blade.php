<div>
    @if (session()->has('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="submit">
        <div>
            <label>Name</label>
            <input wire:model.defer="name" type="text" />
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Email</label>
            <input wire:model.defer="email" type="email" />
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Password</label>
            <input wire:model.defer="password" type="password" />
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Confirm Password</label>
            <input wire:model.defer="password_confirmation" type="password" />
        </div>

        <button type="submit">Create User</button>
    </form>
</div>

