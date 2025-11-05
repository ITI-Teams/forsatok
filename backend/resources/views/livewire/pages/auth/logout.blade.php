<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function mount()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
};
?>

<div class="flex items-center justify-center min-h-screen">
    <p>Logging you out...</p>
</div>
