<?php

namespace App\Domains\Jobs\Services;

use App\Domains\Jobs\Models\JobPost;
use App\Notifications\JobExpiredNotification;
use Carbon\Carbon;

class JobDeadlineDetection
{
    /**
     * Detect if a job has passed its deadline.
     *
     * @param JobPost $job
     * @return bool
     */
    public function detect(JobPost $job): bool
    {
        if (!$job->deadline) {
            return false;
        }

        $deadline = Carbon::parse($job->deadline)->endOfDay();

        if ($deadline->isPast()) {
            // If the job is expired but still marked as active, deactivate it
            if ($job->is_active || $job->status !== JobPost::STATUS_EXPIRED) {
                $job->update([
                    'is_active' => false,
                    'status' => JobPost::STATUS_EXPIRED
                ]);

                // Notify employer about expiration
                $employer = $job->employer;
                if ($employer) {
                    $employer->notify(new JobExpiredNotification($job, null)); // Actor is System (null)
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Detect and handle expiration for all active jobs.
     *
     * @return int Number of jobs expired
     */
    public function detectAll(): int
    {
        $jobs = JobPost::where('is_active', true)->get();
        $expiredCount = 0;

        foreach ($jobs as $job) {
            if ($this->detect($job)) {
                $expiredCount++;
            }
        }

        return $expiredCount;
    }
}