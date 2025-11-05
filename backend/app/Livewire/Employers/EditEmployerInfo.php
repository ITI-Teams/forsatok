<?php

namespace App\Livewire\Employers;

use App\Domains\Employers\Actions\UpdateEmployerInfoAction;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Employers\Requests\UpdateEmployerInfoRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Livewire\Component;

class EditEmployerInfo extends Component
{
    public $company_name, $industry, $location, $about, $website, $email, $phone;
    public $current_password, $password, $password_confirmation;
    public $showPasswordSection = false;

    private function domainRules(): array
    {
        return (new UpdateEmployerInfoRequest())->rules();
    }

    private function emailRules(): array
    {
        return ['email' => ['required', 'email', 'max:255']];
    }

    private function domainMessages(): array
    {
        return (new UpdateEmployerInfoRequest())->messages();
    }

    private function emailMessages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must be at most 255 characters.',
        ];
    }

    public function updated($propertyName): void
    {
        $rules = array_merge($this->domainRules(), $this->emailRules());
        $messages = array_merge($this->domainMessages(), $this->emailMessages());
        $this->validateOnly($propertyName, $rules, $messages);
    }

    public function mount(): void
    {
        $info = EmployerInfo::firstOrNew(['user_id' => Auth::id()]);
        $user = Auth::user();

        $this->company_name = $info->company_name;
        $this->industry = $info->industry;
        $this->location = $info->location;
        $this->about = $info->about;
        $this->website = $info->website;
        $this->phone = null; // Phone not stored in employer_infos table
        $this->email = $user->email ?? '';
    }

    public function updateProfile(UpdateEmployerInfoAction $action)
    {
        if (! Auth::check()) {
            return $this->redirectRoute('login');
        }

        $rules = array_merge($this->domainRules(), $this->emailRules());
        $messages = array_merge($this->domainMessages(), $this->emailMessages());
        $validated = $this->validate($rules, $messages);

        $info = EmployerInfo::firstOrNew(['user_id' => Auth::id()]);
        if (! $info->exists) {
            $info->user_id = Auth::id();
        }
        $action->execute($info, $validated);

        // Update user email if changed
        $user = Auth::user();
        if ($user->email !== $this->email) {
            $user->email = $this->email;
            $user->email_verified_at = null;
            $user->save();
        }

        session()->flash('message', 'Profile updated successfully!');

        return $this->redirectRoute('employer.profile', navigate: true);
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation', 'showPasswordSection']);
        session()->flash('password_message', 'Password updated successfully!');
    }

    public function sendPasswordResetLink()
    {
        $user = Auth::user();

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        session()->flash('reset_link_message', 'Password reset link has been sent to your email!');
    }

    public function render()
    {
        return view('livewire.employers.edit-employer-info')->layout('layouts.app');
    }
}
