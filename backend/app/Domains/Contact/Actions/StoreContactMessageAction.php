<?php

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Users\Models\User;
use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Notification;

class StoreContactMessageAction
{
    /**
     * Store a new contact message.
     *
     * @param array $data
     * @return ContactMessage
     */
    public function execute(array $data): ContactMessage
    {
        $message = ContactMessage::create([
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'subject'   => $data['subject'] ?? null,
            'message'   => $data['message'],
            'user_id'   => $data['user_id'] ?? null,
            'contactable_type' => $data['contactable_type'] ?? null,
            'contactable_id'   => $data['contactable_id'] ?? null,
        ]);

        event(new MessageSent($message));

        if ($message->contactable_id === null) {
            $admins = User::role('admin')->get();
            Notification::send($admins, new NewMessageNotification($message));

        } else {
            $user = User::find($message->contactable_id);
            if ($user) {
                $user->notify(new NewMessageNotification($message));
            }
        }
        return $message;
    }
}
