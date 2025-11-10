<?php

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;

class GetAllContactMessagesAction
{
    /**
     * Get all contact messages with optional search and pagination.
     *
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute(int $perPage = 10, ?string $search = null)
    {
        $query = ContactMessage::query()->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }
}