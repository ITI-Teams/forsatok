<?php
namespace App\Domains\Shared\Services\Audit;


use App\Domains\Shared\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Log an audit entry.
     *
     * $context: [
     *   'action' => 'created'|'updated'|'deleted'|'login'|'custom',
     *   'model'  => $model (Eloquent model) OR ['type' => ..., 'id' => ...],
     *   'changes' => ['old'=>..., 'new'=>...],
     *   'user' => $user object or null
     * ]
     */
    public function log(array $context): AuditLog
    {
        $user = $context['user'] ?? Auth::user();

        $modelType = null;
        $modelId = null;
        if (!empty($context['model'])) {
            $m = $context['model'];
            if (is_object($m) && method_exists($m, 'getKey')) {
                $modelType = get_class($m);
                $modelId = $m->getKey();
            } elseif (is_array($m)) {
                $modelType = $m['type'] ?? null;
                $modelId = $m['id'] ?? null;
            }
        }

        $entry = AuditLog::create([
            'user_id' => $user?->id ?? null,
            'action' => $context['action'] ?? 'custom',
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => $context['changes'] ?? null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'route' => optional($this->request->route())->getName() ?? $this->request->path(),
        ]);

        return $entry;
    }
}
