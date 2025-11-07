<div class="py-4 px-3" data-bs-theme="auto">
    <div class="container">

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3">

            <!-- Header -->
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="h5 mb-0 fw-bold text-body">
                        {{ $applicationId ? 'Edit Application' : 'Create New Application' }}
                    </h2>
                    <small class="text-secondary">
                        {{ $applicationId ? 'Update the application information' : 'Fill in the details to add a new application' }}
                    </small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-body-tertiary"
                     style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-file-lines text-secondary"></i>
                </div>
            </div>

            <!-- Form Section -->
            <div class="card-body">

                @if (session()->has('message'))
                    <div class="alert alert-success d-flex align-items-center gap-2 small mb-4">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">

                    <!-- Candidate -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Candidate <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-user position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <select wire:model="candidate_id"
                                    class="form-select ps-5 @error('candidate_id') is-invalid @enderror"
                                    wire:change="$refresh">
                                <option value="">Select Candidate</option>
                                @foreach($candidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        @error('candidate_id')
                        <div class="invalid-feedback d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Job Post -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Job Post <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-briefcase position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <select wire:model="job_post_id"
                                    class="form-select ps-5 @error('job_post_id') is-invalid @enderror"
                                    wire:change="$refresh">
                                <option value="">Select Job Post</option>
                                @foreach($jobPosts as $jobPost)
                                    <option value="{{ $jobPost->id }}">{{ $jobPost->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('job_post_id')
                        <div class="invalid-feedback d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                        @enderror

                        <!-- عرض رسالة التقديم الموجود -->
                        @if($candidate_id && $job_post_id)
                            @php
                                $existingApplication = \App\Domains\Applications\Models\JobApplication::where('candidate_id', $candidate_id)
                                    ->where('job_post_id', $job_post_id)
                                    ->first();
                            @endphp
                            @if($existingApplication && (!$applicationId || $existingApplication->id != $applicationId))
                                <div class="alert alert-warning mt-2 py-2 small d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-exclamation-triangle"></i>
                                    <div>
                                        <strong>Duplicate Application Found!</strong>
                                        <br>
                                        This candidate has already applied to this job.
                                        @if($existingApplication)
                                            <br>
                                            <small>
                                                Current status:
                                                <span class="badge
                                                    @if($existingApplication->status == 'pending') bg-warning
                                                    @elseif($existingApplication->status == 'accepted') bg-success
                                                    @elseif($existingApplication->status == 'rejected') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($existingApplication->status) }}
                                                </span>
                                                - Applied on: {{ $existingApplication->created_at->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Cover Letter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cover Letter</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-envelope position-absolute"
                               style="left: 0.9rem; top: 1.2rem; color: var(--bs-secondary-color);"></i>
                            <textarea wire:model="cover_letter"
                                      class="form-control ps-5 @error('cover_letter') is-invalid @enderror"
                                      rows="5"
                                      placeholder="Enter cover letter content..."></textarea>
                        </div>
                        @error('cover_letter')
                        <div class="invalid-feedback d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-flag position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <select wire:model="status"
                                    class="form-select ps-5 @error('status') is-invalid @enderror">
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        @error('status')
                        <div class="invalid-feedback d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Current Resume (if editing) -->
                    @if($applicationId && $currentResumePath)
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Current Resume</label>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="{{ Storage::url($currentResumePath) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-eye"></i> View Current Resume
                                </a>
                                <a href="{{ Storage::url($currentResumePath) }}"
                                   download
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                <span class="small text-muted">Upload new resume to replace current one</span>
                            </div>
                        </div>
                    @endif

                    <!-- Resume Upload -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">
                            {{ $applicationId ? 'Upload New Resume' : 'Resume Upload' }}
                            @if(!$applicationId) <span class="text-danger">*</span> @endif
                        </label>
                        <div class="position-relative">
                            <i class="fa-solid fa-file-arrow-up position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <input type="file"
                                   wire:model="resume"
                                   class="form-control ps-5 @error('resume') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.txt">
                        </div>
                        @error('resume')
                        <div class="invalid-feedback d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                        @enderror

                        @if ($resume)
                            <div class="mt-2 p-2 border rounded bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-file text-primary"></i>
                                    <div>
                                        <small class="fw-semibold">{{ $resume->getClientOriginalName() }}</small>
                                        <br>
                                        <small class="text-muted">{{ round($resume->getSize() / 1024, 2) }} KB</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-text">
                            Accepted formats: PDF, DOC, DOCX, TXT (Max: 5MB)
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                        <button type="button" wire:click="cancel"
                                class="btn btn-outline-secondary fw-semibold px-4"
                                wire:loading.attr="disabled">
                            Cancel
                        </button>
                        <button type="submit"
                                class="btn btn-primary fw-semibold px-4"
                                wire:loading.attr="disabled"
                                @if($errors->has('job_post_id') && str_contains($errors->first('job_post_id'), 'already applied')) disabled @endif>
                            <span wire:loading.remove>
                                {{ $applicationId ? 'Update' : 'Create' }} Application
                            </span>
                            <span wire:loading>
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                {{ $applicationId ? 'Updating...' : 'Creating...' }}
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
