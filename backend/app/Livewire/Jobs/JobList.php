<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\job\SoftDeleteJobAction;
use App\Domains\Jobs\Models\JobPost;
use App\Events\JobApproved;
use App\Events\JobRejected;
use App\Notifications\JobApprovedNotification;
use App\Notifications\JobRejectedNotification;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Domains\Users\Models\User;
use App\Notifications\JobCreatedNotification;
use Illuminate\Support\Facades\Notification;

class JobList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = ['title', 'experience'];
    public $showApprovalModal = false;
    public $selectedJob = null;

    public $selectedJobId = null;
    public $rejectionReason = '';

    #[On('jobSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage();
    }

    public function delete($id, SoftDeleteJobAction $delete)
    {
        $job = JobPost::findOrFail($id);
        $delete->execute($job);

        session()->flash('message', 'Job moved to trash!');
    }
    public function openApprovalModal($jobId)
    {
        $this->selectedJob = JobPost::findOrFail($jobId);
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->selectedJob = null;
        $this->rejectionReason = '';
        $this->showApprovalModal = false;
    }

    public function approveJob($jobId)
    {
        $job = JobPost::findOrFail($jobId);
        $oldStatus = $job->status;

        $job->update([
            'status' => JobPost::STATUS_APPROVED
        ]);

        $job->decisions()->create([
            'admin_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => JobPost::STATUS_APPROVED,
            'reason' => 'Approved by admin',
        ]);

        event(new JobApproved($job));
        $employer = $job->employer;
        $employer->notify(new JobApprovedNotification($job, auth()->user()));

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Job approved successfully!'
        ]);

        $this->closeApprovalModal();
    }

    public function rejectJob($jobId)
    {
        $job = JobPost::findOrFail($jobId);
        $oldStatus = $job->status;

        if (empty(trim($this->rejectionReason))) {
            $this->addError('rejectionReason', 'Rejection reason is required.');
            return;
        }

        $job->update([
            'status' => JobPost::STATUS_REJECTED
        ]);

        $job->decisions()->create([
            'admin_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => JobPost::STATUS_REJECTED,
            'reason' => $this->rejectionReason,
        ]);

        event(new JobRejected($job));
        $employer = $job->employer;
        $employer->notify(new JobRejectedNotification($job, auth()->user()));

        $this->dispatch('toast', [
            'type' => 'error',
            'message' => 'Job rejected!'
        ]);

        $this->closeApprovalModal();
    }

    public function resubmitJob($jobId)
    {
        $job = JobPost::where('employer_id', auth()->id())->findOrFail($jobId);

        if (in_array($job->status, [JobPost::STATUS_REJECTED, JobPost::STATUS_EXPIRED])) {
            $job->update([
                'status' => JobPost::STATUS_PENDING,
                'is_active' => true // Re-activating for employer
            ]);

            $admins = User::role('admin')->get();
            Notification::send($admins, new JobCreatedNotification($job));

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Job re-submitted for approval!'
            ]);
        }
    }

    public function render()
    {
        $user = auth()->user();

        $query = JobPost::with(['category', 'employer', 'location.country', 'location.city'])->latest();

        if ($user->type === 'employer') {
            $query->where('employer_id', $user->id);
        }

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    $method = $i === 0 ? 'where' : 'orWhere';
                    $q->$method($field, 'like', "%{$this->search}%");
                }
            });
        }

        $jobs = $query->paginate(10);

        return view('livewire.jobs.job-list', compact('jobs'))->layout('layouts.app');
    }
}
