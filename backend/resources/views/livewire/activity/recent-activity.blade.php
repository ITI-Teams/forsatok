<div>
    <h4 class="mb-3">Recent Activity</h4>
    <div class="list-group">
        @foreach($logs as $log)
            <div class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{ $log->action }}</strong>
                    <div class="small text-muted">
                        @if($log->user) {{ $log->user->name }} @else System @endif
                        • {{ $log->created_at->diffForHumans() }}
                        @if($log->model_type)
                            • <em>{{ class_basename($log->model_type) }}#{{ $log->model_id }}</em>
                        @endif
                    </div>
                    <div class="mt-2 small">
                        @if($log->changes)
                            <pre class="mb-0" style="font-size:11px;">{{ json_encode($log->changes, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif
                    </div>
                </div>

                <div class="text-end">
                    <div class="small text-muted">{{ $log->ip_address }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
