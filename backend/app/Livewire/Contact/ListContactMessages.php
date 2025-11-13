<?php

namespace App\Livewire\Contact;

use App\Domains\Contact\Actions\GetAllContactMessagesAction;
use App\Domains\Contact\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;

class ListContactMessages extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'bootstrap';
    protected $updatesQueryString = ['search'];
    protected $listeners = ['refreshMessages' => '$refresh'];

    public function deleteMessage($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        session()->flash('success', 'Message deleted successfully!');
        $this->resetPage();
    }
    public function render(GetAllContactMessagesAction $getAllMessagesAction)
    {
        $messages = $getAllMessagesAction->execute(10, $this->search);

        return view('livewire.contact.list-contact-messages', [
            'messages' => $messages,
        ])->layout('layouts.app');
    }
}
