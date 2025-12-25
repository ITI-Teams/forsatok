<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Users\Models\User;
use App\Domains\Users\Actions\SoftDeleteUserAction;
use Livewire\Attributes\On;

class UserList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = [];

    // Rejection Modal Properties
    public $showRejectModal = false;
    public $rejectUserId = null;
    public $rejectUserName = '';
    public $rejectionReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    #[On('userSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage(); // ترجع للصفحة 1 عند البحث
    }

    public function delete($id, SoftDeleteUserAction $delete)
    {
        $user = User::findOrFail($id);
        $delete->execute($user);

        session()->flash('success', 'User moved to trash!');
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);

        if ($user->status === 'approved') {
            $this->dispatch('user-error', message: 'User is already approved!');
            return;
        }

        $user->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // TODO: Send approval email notification
        // Mail::to($user->email)->send(new AccountApproved($user));

        $this->dispatch('user-approved');
    }

    public function openRejectModal($id, $name)
    {
        $this->rejectUserId = $id;
        $this->rejectUserName = $name;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectUserId = null;
        $this->rejectUserName = '';
        $this->rejectionReason = '';
    }

    public function submitReject()
    {
        $this->validate([
            'rejectionReason' => 'required|min:10',
        ], [
            'rejectionReason.required' => 'Rejection reason is required.',
            'rejectionReason.min' => 'Rejection reason must be at least 10 characters.',
        ]);

        $this->reject($this->rejectUserId, $this->rejectionReason);
        $this->closeRejectModal();
    }

    public function reject($id, $reason = null)
    {
        $user = User::findOrFail($id);

        if ($user->status === 'approved') {
            $this->dispatch('user-error', message: 'Cannot reject an approved user!');
            return;
        }

        $rejectionReason = $reason ?: 'Rejected by admin via dashboard';
        $userName = $user->name;
        $userEmail = $user->email;

        // Archive to rejected_users
        \Illuminate\Support\Facades\DB::table('rejected_users')->insert([
            'email' => $user->email,
            'name' => $user->name,
            'type' => $user->type,
            'rejection_reason' => $rejectionReason,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->forceDelete();

        // Send rejection email
        \Illuminate\Support\Facades\Mail::to($userEmail)->send(
            new \App\Mail\AccountRejected($userName, $userEmail, $rejectionReason)
        );

        $this->dispatch('user-rejected');
    }

    // Ban Modal Properties
    public $showBanModal = false;
    public $banUserId = null;
    public $banUserName = '';
    public $banReason = '';

    public function openBanModal($id, $name)
    {
        $this->banUserId = $id;
        $this->banUserName = $name;
        $this->banReason = '';
        $this->showBanModal = true;
    }

    public function closeBanModal()
    {
        $this->showBanModal = false;
        $this->banUserId = null;
        $this->banUserName = '';
        $this->banReason = '';
    }

    public function submitBan()
    {
        $this->validate([
            'banReason' => 'required|min:10',
        ], [
            'banReason.required' => 'Ban reason is required.',
            'banReason.min' => 'Ban reason must be at least 10 characters.',
        ]);

        $user = User::findOrFail($this->banUserId);

        if ($user->status === 'banned') {
            $this->dispatch('user-error', message: 'User is already banned!');
            $this->closeBanModal();
            return;
        }

        $user->update(['status' => 'banned']);

        // Send ban email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(
            new \App\Mail\AccountBanned($user, $this->banReason)
        );

        $this->closeBanModal();
        $this->dispatch('user-banned');
    }

    public function unban($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'banned') {
            $this->dispatch('user-error', message: 'User is not banned!');
            return;
        }

        $user->update(['status' => 'approved']);
        $this->dispatch('user-unbanned');
    }

    public function render()
    {
        $query = User::latest();

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    $method = $i === 0 ? 'where' : 'orWhere';
                    $q->$method($field, 'like', "%{$this->search}%");
                }
            });
        }

        $users = $query->paginate(5);

        return view('livewire.users.user-list', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}
