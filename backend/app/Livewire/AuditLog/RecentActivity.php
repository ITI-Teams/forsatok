<?php
namespace App\Livewire\AuditLog;

use App\Domains\Shared\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class RecentActivity extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;

    public function render()
    {
        $logs = AuditLog::with('user')->latest()->paginate($this->perPage);

        return view('livewire.activity.recent-activity', [
            'logs' => $logs,
        ])->layout('layouts.app');
    }
}
