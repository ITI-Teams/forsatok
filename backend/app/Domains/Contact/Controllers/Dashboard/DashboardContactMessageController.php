<?php

namespace App\Domains\Contact\Controllers\Dashboard;

use App\Domains\Contact\Models\ContactMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dashboard Contact Message Controller
 *
 * Handles contact message operations in the dashboard.
 */
class DashboardContactMessageController extends Controller
{
    /**
     * List all contact messages (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));

        $messages = ContactMessage::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Delete a contact message.
     */
    public function destroy(ContactMessage $message): JsonResponse
    {
        $message->delete();

        return response()->json([
            'status' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }

    /**
     * List contact messages for employer.
     */
    public function employerIndex(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    /**
     * List contact messages for shared access (admin|employer).
     */
    public function sharedIndex(Request $request): JsonResponse
    {
        return $this->index($request);
    }
}
