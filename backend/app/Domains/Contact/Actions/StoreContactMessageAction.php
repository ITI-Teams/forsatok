<?php

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;

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
        return ContactMessage::create([
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'subject'   => $data['subject'] ?? null,
            'message'   => $data['message'],
            'user_id'   => $data['user_id'] ?? null,
            'contactable_type' => $data['contactable_type'] ?? null,
            'contactable_id'   => $data['contactable_id'] ?? null,
        ]);
    }
}