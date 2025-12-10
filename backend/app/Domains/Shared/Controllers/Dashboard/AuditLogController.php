<?php

namespace App\Domains\Shared\Controllers\Dashboard;

use App\Domains\Shared\Models\AuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Audit Log Controller
 *
 * Handles audit log viewing in the admin dashboard.
 */
class AuditLogController extends Controller
{
    /**
     * List all audit logs with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(min(100, max(1, (int) $request->input('per_page', 15))));

        return response()->json([
            'status' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
